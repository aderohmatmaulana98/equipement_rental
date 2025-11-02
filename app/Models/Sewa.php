<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    use HasFactory;

    protected $table = 'sewas';

    protected $fillable = [
        'id_user',
        'kode_sewa',
        'alamat_acara',
        'tgl_sewa',
        'tgl_acara',
        'jam_acara',
        'tgl_loading',
        'jam_loading',
        'tgl_loading_out',
        'total_biaya',
        'uang_muka',
        'no_rekening',
        'tgl_pembayaran',
        'bukti_pembayaran',
        'sisa_pembayaran',
        'batas_waktu_pembayaran',
        'status',
        'keterangan',
    ];

    // 🔗 Relasi
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function detailSewas()
    {
        return $this->hasMany(DetailSewa::class, 'id_sewa');
    }

    // 🧮 Helper: hitung total biaya otomatis
    public function hitungTotal()
    {
        $this->total_biaya = $this->detailSewas->sum('subtotal');
        $this->sisa_pembayaran = $this->total_biaya - $this->uang_muka;
        $this->save();
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id');
    }
}
