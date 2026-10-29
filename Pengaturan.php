<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function toko(): array
    {
        return [
            'ukuran_kertas' => static::get('ukuran_kertas', 'auto'),
            'panjang_kertas' => static::get('panjang_kertas', 'auto'),
            'nama_toko' => static::get('nama_toko', config('app.name', 'Toko')),
            'alamat_toko' => static::get('alamat_toko', ''),
            'telp_toko' => static::get('telp_toko', ''),
        ];
    }

    public static function stokAktif(): bool
    {
        return static::get('fitur_stok', '1') === '1';
    }
}
