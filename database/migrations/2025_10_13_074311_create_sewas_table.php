<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewas', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // penyewa

            // Informasi umum sewa
            $table->string('kode_sewa')->unique(); // contoh: SEWA-20251013-001
            $table->string('alamat_acara');
            $table->date('tgl_sewa'); // tanggal transaksi dibuat
            $table->date('tgl_acara');
            $table->time('jam_acara')->nullable();

            // Logistik
            $table->date('tgl_loading')->nullable();
            $table->time('jam_loading')->nullable();
            $table->date('tgl_loading_out')->nullable();

            // Keuangan
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('uang_muka', 15, 2)->default(0);
            $table->string('no_rekening')->nullable();
            $table->date('tgl_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->decimal('sisa_pembayaran', 15, 2)->default(0);

            // Status & catatan
            $table->enum('status', ['pending', 'disetujui', 'berjalan', 'selesai', 'batal'])->default('pending');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewas');
    }
};
