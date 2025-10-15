<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailSewa extends Model
{
    use HasFactory;

    protected $table = 'detail_sewas';

    protected $fillable = [
        'id_sewa',
        'id_barang',
        'qty',
        'harga_satuan',
        'subtotal',
        'keterangan',
    ];

    // 🔗 Relasi
    public function sewa()
    {
        return $this->belongsTo(Sewa::class, 'id_sewa');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // 🧮 Helper: hitung subtotal otomatis
    public function hitungSubtotal()
    {
        $this->subtotal = ($this->harga_satuan - $this->diskon) * $this->qty;
        $this->save();
    }
}
