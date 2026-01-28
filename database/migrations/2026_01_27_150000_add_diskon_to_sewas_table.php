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
        Schema::table('sewas', function (Blueprint $table) {
            $table->integer('diskon_persen')->nullable()->after('sisa_pembayaran')->comment('Persentase diskon (0-100)');
            $table->decimal('diskon_nominal', 15, 2)->nullable()->after('diskon_persen')->comment('Nilai nominal diskon dalam Rupiah');
            $table->decimal('total_sebelum_diskon', 15, 2)->nullable()->after('diskon_nominal')->comment('Total biaya sebelum diskon diterapkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sewas', function (Blueprint $table) {
            $table->dropColumn(['diskon_persen', 'diskon_nominal', 'total_sebelum_diskon']);
        });
    }
};
