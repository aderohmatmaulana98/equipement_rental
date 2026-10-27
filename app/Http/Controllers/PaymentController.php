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

        // Cek ketersediaan stok barang sebelum memproses pembayaran DP
        foreach ($sewa->detailSewas as $ds) {
            $b = Barang::find($ds->id_barang);
            if (!$b || $b->stok < $ds->qty) {
                $nama = $b ? $b->nama_barang : 'Barang';
                return response()->json(['error' => "Maaf, stok {$nama} tidak mencukupi (sisa: " . ($b ? $b->stok : 0) . ")."], 400);
            }
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

        // Cek apakah sudah ada snap_token pelunasan yang tersimpan
        if ($sewa->snap_token_pelunasan) {
            return response()->json(['snapToken' => $sewa->snap_token_pelunasan]);
        }

        // Set Midtrans config
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);
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
            
            // Simpan snap_token pelunasan
            $sewa->update([
                'snap_token_pelunasan' => $snapToken,
            ]);

            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mendapatkan token Midtrans: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Proses update status pembayaran berdasarkan order_id dari Midtrans
     */
    public static function processPaymentStatus($orderId, $transactionStatus = null, $fraudStatus = null)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', false);

        // Jika status belum pasti, verifikasi langsung ke server Midtrans via status API
        if (!$transactionStatus || $transactionStatus === 'pending') {
            try {
                $status = \Midtrans\Transaction::status($orderId);
                $transactionStatus = $status->transaction_status ?? $transactionStatus;
                $fraudStatus = $status->fraud_status ?? $fraudStatus;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Midtrans status check error for {$orderId}: " . $e->getMessage());
            }
        }

        $isLunas = str_contains($orderId, '-LUNAS-');
        $kodeSewa = preg_replace('/-(DP|LUNAS)-\d+$/', '', $orderId);
        
        $sewa = Sewa::where('kode_sewa', $kodeSewa)->first();

        if (!$sewa) {
            return false;
        }

        // Handle transaksi sukses (capture atau settlement)
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            if ($fraudStatus == 'challenge') {
                return false;
            }

            if ($isLunas) {
                // Pelunasan berhasil
                if ($sewa->status !== 'disetujui') {
                    $previousStatus = $sewa->status;
                    $sewa->update([
                        'status' => 'disetujui',
                        'sisa_pembayaran' => 0,
                        'tgl_pembayaran' => now(),
                        'waktu_lunas' => now(),
                        'snap_token_pelunasan' => null,
                    ]);
                    if (!$sewa->waktu_dp) {
                        $sewa->update(['waktu_dp' => now()]);
                    }
                    $sewa->adjustStockForStatusChange($previousStatus, 'disetujui');
                }
            } else {
                // DP berhasil
                if ($sewa->status === 'belum bayar') {
                    $previousStatus = $sewa->status;
                    $sewa->update([
                        'status' => 'dp_lunas',
                        'tgl_pembayaran' => now(),
                        'waktu_dp' => now(),
                        'snap_token' => null,
                        'batas_waktu_pembayaran' => $sewa->tgl_loading 
                            ? $sewa->tgl_loading . ' ' . ($sewa->jam_loading ?? '08:00:00') 
                            : now()->addDays(7),
                    ]);

                    $sewa->adjustStockForStatusChange($previousStatus, 'dp_lunas');
                }
            }

            return $sewa->fresh();

        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            // Transaksi gagal - clear token
            if ($isLunas) {
                $sewa->update(['snap_token_pelunasan' => null]);
            } else {
                $previousStatus = $sewa->status;
                $sewa->update(['snap_token' => null]);
                if ($sewa->status === 'belum bayar') {
                    $sewa->update(['status' => 'batal']);
                    $sewa->adjustStockForStatusChange($previousStatus, 'batal');
                }
            }
            return $sewa->fresh();
        }

        return $sewa;
    }

    /**
     * Callback dari Midtrans
     */
    public function callback(Request $request)
    {
        try {
            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status ?? null;

            $sewa = self::processPaymentStatus($orderId, $transaction, $fraudStatus);

            if (!$sewa) {
                return response()->json(['message' => 'Data sewa tidak ditemukan atau review diperlukan'], 404);
            }

            return response()->json([
                'message' => 'Callback processed',
                'status' => $sewa->status,
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
