<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailSewa;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    /**
     * Buat transaksi pembayaran DP (50%)
     */
    public function createTransaction(Request $request, $id)
    {
        $sewa = Sewa::with('user')->find($id);

        if (!$sewa || !$sewa->user) {
            return response()->json(['error' => 'Data sewa atau user tidak ditemukan'], 404);
        }

        // Cek apakah sudah ada snap_token yang masih valid (belum expired)
        // Token Midtrans expired setelah 24 jam, tapi kita set batas 1 jam di batas_waktu_pembayaran
        if ($sewa->snap_token && $sewa->batas_waktu_pembayaran && now()->lt($sewa->batas_waktu_pembayaran)) {
            return response()->json(['snapToken' => $sewa->snap_token]);
        }

        // Set Midtrans config
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Buat order_id unik dengan timestamp untuk menghindari duplikasi
        $orderId = $sewa->kode_sewa . '-DP-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $sewa->uang_muka,
            ],
            'customer_details' => [
                'first_name' => $sewa->user->name,
                'email' => $sewa->user->email,
                'phone' => $sewa->user->no_hp ?? '',
            ],
            'item_details' => [
                [
                    'id' => $sewa->kode_sewa,
                    'price' => (int) $sewa->uang_muka,
                    'quantity' => 1,
                    'name' => 'DP Sewa ' . $sewa->kode_sewa,
                ]
            ],
            'callbacks' => [
                'finish' => url('/user/sewa'),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan snap_token dan update batas waktu pembayaran
            $sewa->update([
                'snap_token' => $snapToken,
                'batas_waktu_pembayaran' => now()->addHour(),
            ]);

            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mendapatkan token Midtrans: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Buat transaksi pelunasan (sisa 50%)
     */
    public function createPelunasan(Request $request, $id)
    {
        $sewa = Sewa::with('user')->find($id);

        if (!$sewa || !$sewa->user) {
            return response()->json(['error' => 'Data sewa atau user tidak ditemukan'], 404);
        }

        if ($sewa->status !== 'dp_lunas') {
            return response()->json(['error' => 'Status sewa harus DP Lunas untuk melakukan pelunasan'], 400);
        }

        // Cek apakah sudah ada snap_token pelunasan yang masih valid
        if ($sewa->snap_token_pelunasan && $sewa->batas_waktu_pembayaran && now()->lt($sewa->batas_waktu_pembayaran)) {
            return response()->json(['snapToken' => $sewa->snap_token_pelunasan]);
        }

        // Set Midtrans config
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Buat order_id unik untuk pelunasan
        $orderId = $sewa->kode_sewa . '-LUNAS-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $sewa->sisa_pembayaran,
            ],
            'customer_details' => [
                'first_name' => $sewa->user->name,
                'email' => $sewa->user->email,
                'phone' => $sewa->user->no_hp ?? '',
            ],
            'item_details' => [
                [
                    'id' => $sewa->kode_sewa . '-LUNAS',
                    'price' => (int) $sewa->sisa_pembayaran,
                    'quantity' => 1,
                    'name' => 'Pelunasan Sewa ' . $sewa->kode_sewa,
                ]
            ],
            'callbacks' => [
                'finish' => url('/user/sewa'),
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            
            // Simpan snap_token pelunasan dan update batas waktu
            $sewa->update([
                'snap_token_pelunasan' => $snapToken,
                'batas_waktu_pembayaran' => now()->addHour(),
            ]);

            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mendapatkan token Midtrans: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Callback dari Midtrans
     */
    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;

        try {
            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $orderId = $notif->order_id;
            $type = $notif->payment_type;
            $fraudStatus = $notif->fraud_status ?? null;

            // Extract kode_sewa dari order_id (format: KODE_SEWA-DP-timestamp atau KODE_SEWA-LUNAS-timestamp)
            // Contoh: SEWA-20260125-001-abc123-DP-1737817200
            $isLunas = str_contains($orderId, '-LUNAS-');
            
            // Ambil kode_sewa dengan menghapus suffix -DP-xxx atau -LUNAS-xxx
            $kodeSewa = preg_replace('/-(DP|LUNAS)-\d+$/', '', $orderId);
            
            $sewa = Sewa::where('kode_sewa', $kodeSewa)->first();

            if (!$sewa) {
                return response()->json(['message' => 'Data sewa tidak ditemukan'], 404);
            }

            // Handle berbagai status transaksi
            if ($transaction == 'capture' || $transaction == 'settlement') {
                if ($fraudStatus == 'challenge') {
                    // Transaksi perlu review, jangan ubah status
                    return response()->json(['message' => 'Transaction needs review', 'order_id' => $orderId]);
                }
                
                if ($isLunas) {
                    // Pelunasan berhasil
                    $sewa->update([
                        'status' => 'disetujui',
                        'sisa_pembayaran' => 0,
                        'tgl_pembayaran' => now(),
                        'snap_token_pelunasan' => null, // Clear token setelah sukses
                    ]);
                    
                } else {
                    // DP berhasil
                    $sewa->update([
                        'status' => 'dp_lunas',
                        'tgl_pembayaran' => now(),
                        'snap_token' => null, // Clear token setelah sukses
                    ]);
                    
                    // Kurangi stok barang (hanya saat DP berhasil, bukan saat pelunasan)
                    $detailSewa = DetailSewa::where('id_sewa', $sewa->id)->get();
                    foreach ($detailSewa as $ds) {
                        Barang::where('id', $ds->id_barang)->decrement('stok', $ds->qty);
                    }
                }
                
            } elseif ($transaction == 'pending') {
                // Tidak perlu ubah status, masih menunggu pembayaran
                
            } elseif (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                // Transaksi gagal - clear token supaya bisa buat baru
                if ($isLunas) {
                    $sewa->update(['snap_token_pelunasan' => null]);
                } else {
                    $sewa->update(['snap_token' => null]);
                }

                // Jika DP expire/cancel dan status masih belum bayar, set status batal
                if (!$isLunas && $sewa->status === 'belum bayar') {
                    $sewa->update(['status' => 'batal']);
                }
            }

            return response()->json([
                'message' => 'Callback processed',
                'status' => $sewa->fresh()->status,
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
