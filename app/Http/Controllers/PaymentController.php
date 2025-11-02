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
     public function createTransaction(Request $request, $id)
    {
        $sewa = Sewa::with('user')->find($id);

        if (!$sewa || !$sewa->user) {
            return response()->json(['error' => 'Data sewa atau user tidak ditemukan'], 404);
        }

        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = [
            'transaction_details' => [
                'order_id' => $sewa->kode_sewa,
                'gross_amount' => $sewa->uang_muka,
            ],
            'customer_details' => [
                'first_name' => $sewa->user->name,
                'email' => $sewa->user->email,
                'phone' => $sewa->user->no_hp,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            // Sewa::where('id', $id)->update(['status' => 'pending']);
            return response()->json(['snapToken' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;

        try {
          

            $notif = new \Midtrans\Notification();

            $transaction = $notif->transaction_status;
            $orderId = $notif->order_id;
            $type = $notif->payment_type;
            $fraudStatus = $notif->fraud_status;


            $sewa = Sewa::where('kode_sewa', $orderId)->first();

            $detailSewa = DetailSewa::where('id_sewa', $sewa->id)->get();

            foreach ($detailSewa as $ds) {
                Barang::where('id', $ds->id_barang)->decrement('stok', $ds->qty);
            }

            if (!$sewa) {

                return response()->json(['message' => 'Data sewa tidak ditemukan'], 404);
            }

            if ($transaction == 'capture') {
                if ($type == 'credit_card' && $fraudStatus == 'challenge') {
                    $sewa->update(['status' => 'belum bayar']);
                } else {
                    $sewa->update(['status' => 'pending']);
                }
            } elseif ($transaction == 'settlement') {
                $sewa->update(['status' => 'pending']);
            } elseif ($transaction == 'pending') {
                $sewa->update(['status' => 'belum bayar']);
            } elseif ($transaction == 'deny') {
                $sewa->update(['status' => 'batal']);
            } elseif ($transaction == 'expire') {
                $sewa->update(['status' => 'batal']);
            } elseif ($transaction == 'cancel') {
                $sewa->update(['status' => 'dibatalkan']);
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
