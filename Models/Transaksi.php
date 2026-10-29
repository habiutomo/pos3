<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['no_transaksi', 'user_id', 'total_harga', 'bayar', 'kembalian'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class);
    }
}
