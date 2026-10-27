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
            $table->dateTime('waktu_dp')->nullable()->after('sisa_pembayaran');
            $table->dateTime('waktu_lunas')->nullable()->after('waktu_dp');
        });

        // Backfill data lama
        // Untuk data dengan status dp_lunas, disetujui, berjalan, selesai: isi waktu_dp
        DB::statement("
            UPDATE sewas 
            SET waktu_dp = COALESCE(updated_at, tgl_pembayaran, created_at)
            WHERE status IN ('dp_lunas', 'disetujui', 'berjalan', 'selesai')
        ");

        // Untuk data dengan status disetujui, berjalan, selesai: isi waktu_lunas
        DB::statement("
            UPDATE sewas 
            SET waktu_lunas = COALESCE(updated_at, tgl_pembayaran)
            WHERE status IN ('disetujui', 'berjalan', 'selesai')
        ");
    }

    public function down(): void
    {
        Schema::table('sewas', function (Blueprint $table) {
            $table->dropColumn(['waktu_dp', 'waktu_lunas']);
        });
    }
};
