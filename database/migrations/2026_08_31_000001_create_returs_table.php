<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returs', function (Blueprint $table) {
            $table->id();
            $table->string('no_retur')->unique();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_pengembalian', 15, 2)->default(0);
            $table->text('alasan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_returs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retur_id')->constrained('returs')->onDelete('cascade');
            $table->foreignId('detail_transaksi_id')->constrained('detail_transaksis')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('jumlah_dasar');
            $table->string('satuan_jual')->nullable();
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_returs');
        Schema::dropIfExists('returs');
    }
};
