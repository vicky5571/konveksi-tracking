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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode')->index();
            $table->string('original_kode')->nullable()->index();
            $table->date('tanggal');
            $table->string('style');
            $table->string('warna');
            $table->unsignedInteger('total')->default(0);
            $table->string('penjahit')->nullable();
            $table->string('status')->default('potongan_selesai')->index();
            $table->string('kurir')->nullable();
            $table->string('resi')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
