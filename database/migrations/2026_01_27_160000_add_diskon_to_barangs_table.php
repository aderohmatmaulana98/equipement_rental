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
        Schema::table('barangs', function (Blueprint $table) {
            $table->integer('diskon_persen')->nullable()->after('gambar')->comment('Persentase diskon (0-100)');
            $table->decimal('diskon_nominal', 15, 2)->nullable()->after('diskon_persen')->comment('Nilai nominal diskon dalam Rupiah');
            $table->date('diskon_mulai')->nullable()->after('diskon_nominal')->comment('Tanggal mulai promo');
            $table->date('diskon_sampai')->nullable()->after('diskon_mulai')->comment('Tanggal berakhir promo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['diskon_persen', 'diskon_nominal', 'diskon_mulai', 'diskon_sampai']);
        });
    }
};
