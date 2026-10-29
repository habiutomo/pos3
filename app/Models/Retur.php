<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $fillable = ['no_retur', 'transaksi_id', 'user_id', 'total_pengembalian', 'alasan'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailReturs()
    {
        return $this->hasMany(DetailRetur::class);
    }
}
