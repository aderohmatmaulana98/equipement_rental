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
        'snap_token',
        'snap_token_pelunasan',
        'sisa_pembayaran',
        'batas_waktu_pembayaran',
        'status',
        'keterangan',
        'catatan_pengembalian',
        'diskon_persen',
        'diskon_nominal',
        'total_sebelum_diskon',
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

    /**
     * Cek apakah status sewa merupakan status aktif yang menahan / mengurangi stok barang.
     * Status aktif (stok keluar): dp_lunas, disetujui, berjalan.
     * Status tidak menahan stok: belum bayar, pending, batal, dibatalkan, expired, selesai.
     */
    public static function isStockHeldStatus(?string $status): bool
    {
        return in_array($status, ['dp_lunas', 'disetujui', 'berjalan']);
    }

    /**
     * Sesuaikan stok barang saat terjadi perubahan status sewa.
     * - Inactive -> Active   : Barang KELUAR (stok berkurang)
     * - Active   -> Inactive : Barang MASUK kembali / Selesai / Dibatalkan (stok bertambah)
     * - Active   -> Active   : Tidak ada perubahan stok (misal: dp_lunas -> disetujui)
     * - Inactive -> Inactive : Tidak ada perubahan stok (misal: belum bayar -> expired)
     */
    public function adjustStockForStatusChange(?string $fromStatus, ?string $toStatus): void
    {
        $wasHeld = self::isStockHeldStatus($fromStatus);
        $nowHeld = self::isStockHeldStatus($toStatus);

        if (!$wasHeld && $nowHeld) {
            // Barang KELUAR: kurangi stok
            foreach ($this->detailSewas as $ds) {
                Barang::where('id', $ds->id_barang)->decrement('stok', $ds->qty);
            }
        } elseif ($wasHeld && !$nowHeld) {
            // Barang MASUK (kembali ke gudang / dibatalkan): tambahkan stok
            foreach ($this->detailSewas as $ds) {
                Barang::where('id', $ds->id_barang)->increment('stok', $ds->qty);
            }
        }
    }
}
