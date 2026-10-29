<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRetur extends Model
{
    protected $fillable = [
        'retur_id', 'detail_transaksi_id', 'produk_id', 'jumlah',
        'jumlah_dasar', 'satuan_jual', 'harga_satuan', 'subtotal',
    ];

    public function retur()
    {
        return $this->belongsTo(Retur::class);
    }

    public function detailTransaksi()
    {
        return $this->belongsTo(DetailTransaksi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
