<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sewas', function (Blueprint $table) {
            // Ubah kolom sisa_pembayaran agar bisa NULL
            $table->decimal('sisa_pembayaran', 15, 2)->nullable()->change();

            // Tambah opsi baru di kolom status
            $table->enum('status', [
                'belum bayar',
                'pending',
                'disetujui',
                'berjalan',
                'selesai',
                'batal',
                'dibatalkan',
            ])->default('pending')->change();

            // Tambah kolom batas waktu pembayaran
            $table->dateTime('batas_waktu_pembayaran')->nullable()->after('tgl_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('sewas', function (Blueprint $table) {
            // Balik ke semula
            $table->decimal('sisa_pembayaran', 15, 2)->default(0)->nullable(false)->change();

            $table->enum('status', [
                'pending',
                'disetujui',
                'berjalan',
                'selesai',
                'batal',
            ])->default('pending')->change();

            $table->dropColumn('batas_waktu_pembayaran');
        });
    }
};
