<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewas', function (Blueprint $table) {
            // Tambah kolom untuk menyimpan Midtrans snap token
            $table->string('snap_token')->nullable()->after('bukti_pembayaran');
            $table->string('snap_token_pelunasan')->nullable()->after('snap_token');
        });

        // Update enum status untuk menambah 'dp_lunas'
        DB::statement("ALTER TABLE sewas MODIFY COLUMN status ENUM('belum bayar', 'pending', 'dp_lunas', 'disetujui', 'berjalan', 'selesai', 'batal', 'dibatalkan') DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('sewas', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'snap_token_pelunasan']);
        });

        // Kembalikan enum status ke semula
        DB::statement("ALTER TABLE sewas MODIFY COLUMN status ENUM('belum bayar', 'pending', 'disetujui', 'berjalan', 'selesai', 'batal', 'dibatalkan') DEFAULT 'pending'");
    }
};
