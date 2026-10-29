<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdukSatuan extends Model
{
    protected $fillable = ['produk_id', 'satuan_id', 'konversi', 'harga_jual'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
}
