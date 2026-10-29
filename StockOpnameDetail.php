<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    protected $fillable = ['stock_opname_id', 'produk_id', 'stok_sistem', 'stok_fisik', 'selisih'];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
