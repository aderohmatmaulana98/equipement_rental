<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum status untuk menambah 'expired'
        DB::statement("ALTER TABLE sewas MODIFY COLUMN status ENUM('belum bayar', 'pending', 'dp_lunas', 'disetujui', 'berjalan', 'selesai', 'batal', 'dibatalkan', 'expired') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Kembalikan enum status ke semula (tanpa expired)
        DB::statement("ALTER TABLE sewas MODIFY COLUMN status ENUM('belum bayar', 'pending', 'dp_lunas', 'disetujui', 'berjalan', 'selesai', 'batal', 'dibatalkan') DEFAULT 'pending'");
    }
};
