<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_satuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('satuan_id')->constrained('satuans')->onDelete('restrict');
            $table->integer('konversi')->default(1);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['produk_id', 'satuan_id']);
        });

        foreach (['tim', 'bks'] as $nama) {
            DB::table('satuans')->insertOrIgnore([
                'nama_satuan' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $produks = DB::table('produks')->select('id', 'satuan_id', 'harga_jual')->get();
        foreach ($produks as $produk) {
            DB::table('produk_satuans')->insertOrIgnore([
                'produk_id' => $produk->id,
                'satuan_id' => $produk->satuan_id,
                'konversi' => 1,
                'harga_jual' => $produk->harga_jual,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->string('satuan_jual', 20)->nullable()->after('jumlah');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['harga_grosir', 'min_qty_grosir']);
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->decimal('harga_grosir', 15, 2)->default(0)->after('harga_jual');
            $table->integer('min_qty_grosir')->default(0)->after('harga_grosir');
        });

        Schema::table('detail_transaksis', function (Blueprint $table) {
            $table->dropColumn('satuan_jual');
        });

        Schema::dropIfExists('produk_satuans');

        DB::table('satuans')->whereIn('nama_satuan', ['tim', 'bks'])->delete();
    }
};
