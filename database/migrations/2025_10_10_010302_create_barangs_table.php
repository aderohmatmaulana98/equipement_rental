<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_barang_id')->nullable();
            $table->string('nama_barang');
            $table->string('satuan');
            $table->decimal('harga', 15, 2);
            $table->text('keterangan')->nullable();
            $table->integer('stok')->default(0);
            $table->string('gambar')->nullable();
            $table->timestamps();

            $table->foreign('jenis_barang_id')->references('id')->on('jenis_barang')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
