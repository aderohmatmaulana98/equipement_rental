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
        'diskon_persen',
        'diskon_nominal',
        'diskon_mulai',
        'diskon_sampai',
    ];

    protected $casts = [
        'diskon_mulai' => 'date',
        'diskon_sampai' => 'date',
    ];

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class);
    }

    /**
     * Check if discount is currently active
     */
    public function isDiskonAktif(): bool
    {
        // No discount set
        if (!$this->diskon_persen && !$this->diskon_nominal) {
            return false;
        }

        $today = now()->startOfDay();
        
        // Check period if set
        if ($this->diskon_mulai && $today->lt($this->diskon_mulai)) {
            return false;
        }
        if ($this->diskon_sampai && $today->gt($this->diskon_sampai->endOfDay())) {
            return false;
        }

        return true;
    }

    /**
     * Get price after discount
     */
    public function getHargaDiskon(): float
    {
        if (!$this->isDiskonAktif()) {
            return $this->harga;
        }

        if ($this->diskon_persen && $this->diskon_persen > 0) {
            return $this->harga - ($this->harga * $this->diskon_persen / 100);
        }

        if ($this->diskon_nominal && $this->diskon_nominal > 0) {
            $hargaDiskon = $this->harga - $this->diskon_nominal;
            return max(0, $hargaDiskon); // Don't go negative
        }

        return $this->harga;
    }

    /**
     * Get discount amount
     */
    public function getPotonganDiskon(): float
    {
        return $this->harga - $this->getHargaDiskon();
    }
}

