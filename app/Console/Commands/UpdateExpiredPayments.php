<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sewa;
use Carbon\Carbon;

class UpdateExpiredPayments extends Command
{
    protected $signature = 'payments:update-expired';
    protected $description = 'Update status sewa yang batas pembayarannya sudah lewat menjadi batal';

    public function handle()
    {
        $now = Carbon::now();

        // Ambil semua sewa yang belum bayar dan batas waktunya sudah lewat
        $expiredSewa = Sewa::where('status', 'belum bayar')
            ->where('batas_waktu_pembayaran', '<', $now)
            ->get();

        foreach ($expiredSewa as $sewa) {
            $sewa->update(['status' => 'batal']);
        }

        $this->info('Berhasil memperbarui ' . $expiredSewa->count() . ' sewa yang kedaluwarsa.');
    }
}
