<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan', 20)->unique();
            $table->timestamps();
        });

        $defaults = ['pcs', 'dus', 'box', 'pack', 'renceng', 'kg', 'gram', 'liter', 'ml', 'meter', 'lembar', 'botol', 'kaleng', 'sachet', 'unit'];
        foreach ($defaults as $nama) {
            DB::table('satuans')->insertOrIgnore([
                'nama_satuan' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('produks', function (Blueprint $table) {
            $table->foreignId('satuan_id')->nullable()->after('nama_produk');
        });

        foreach (DB::table('produks')->select('id', 'satuan')->get() as $produk) {
            $nama = strtolower(trim($produk->satuan)) ?: 'pcs';
            $satuanId = DB::table('satuans')->where('nama_satuan', $nama)->value('id');
            if (!$satuanId) {
                $satuanId = DB::table('satuans')->insertGetId([
                    'nama_satuan' => Str::limit($nama, 20, ''),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('produks')->where('id', $produk->id)->update(['satuan_id' => $satuanId]);
        }

        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->foreignId('satuan_id')->nullable(false)->change();
            $table->foreign('satuan_id')->references('id')->on('satuans')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropColumn('satuan_id');
            $table->string('satuan', 20)->default('pcs')->after('nama_produk');
        });

        foreach (DB::table('satuans')->get() as $satuan) {
            DB::table('produks')->where('satuan_id', $satuan->id)->update(['satuan' => $satuan->nama_satuan]);
        }

        Schema::dropIfExists('satuans');
    }
};
