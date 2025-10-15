<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'jenis_barang_id',
        'nama_barang',
        'satuan',
        'harga',
        'keterangan',
        'stok',
        'gambar',
    ];

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class);
    }
}

