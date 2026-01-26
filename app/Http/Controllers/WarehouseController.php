<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\User;
use App\Models\Sewa;
use App\Models\DetailSewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function dashboard()
    {

        $user = Auth::user();
        $title = 'Dashboard Warehouse';

        // 1. Loading Hari Ini (Barang Keluar)
        $loadingHariIni = Sewa::with('user')
            ->whereDate('tgl_loading', now())
            ->whereIn('status', ['dp_lunas', 'disetujui', 'berjalan'])
            ->orderBy('jam_loading')
            ->get();

        // 2. Loading Out Hari Ini (Barang Masuk/Kembali)
        $loadingOutHariIni = Sewa::with('user')
            ->whereDate('tgl_loading_out', now())
            ->whereIn('status', ['berjalan', 'selesai'])
            ->orderBy('tgl_loading_out')
            ->get();

        // 3. Sewa Berjalan (Sedang Disewa)
        $sedangDisewa = Sewa::where('status', 'berjalan')->count();

        // 4. Perlu Dipersiapkan (H-1 atau Hari H tapi belum jalan)
        $perluPersiapan = Sewa::whereIn('status', ['dp_lunas', 'disetujui'])
            ->whereDate('tgl_loading', '<=', now()->addDay())
            ->count();

        // 5. Stok Barang Menipis (Misal < 5 unit)
        $stokMenipis = Barang::where('stok', '<', 5)->orderBy('stok', 'asc')->limit(10)->get();

        $totalBarang = Barang::count();
        $totalSewaAktif = Sewa::whereIn('status', ['dp_lunas', 'disetujui', 'berjalan'])->count();

        return view('warehouse.dashboard', compact(
            'user', 
            'title', 
            'loadingHariIni', 
            'loadingOutHariIni', 
            'sedangDisewa', 
            'perluPersiapan',
            'stokMenipis',
            'totalBarang',
            'totalSewaAktif'
        ));
    }

    public function list_barang()
    {
        $title = 'Ketersediaan Barang';
        $barangs = Barang::all();
        $jenisBarang = JenisBarang::all();
        return view('warehouse.list_barang', compact('barangs', 'jenisBarang', 'title'));
    }
    public function penyewaan()
    {
        $title = 'Sewa Barang';
        $sewas = Sewa::with('user')->orderBy('created_at', 'desc')->get();
        return view('warehouse.penyewaan', compact('title', 'sewas'));
    }

    public function show($id)
    {
        $title = 'Detail Sewa Barang';
        $sewa = Sewa::with('user')->findOrFail($id);
        $detailSewa = DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();
        return view('warehouse.detail', compact('title', 'detailSewa', 'sewa'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sewas,id',
            'status' => 'required|in:selesai',
        ]);

        $sewa = Sewa::findOrFail($request->id);
        $previousStatus = $sewa->status;
        
        // Jika status berubah menjadi 'selesai', kembalikan stok barang
        if ($request->status === 'selesai' && $previousStatus !== 'selesai') {
            $detailSewa = DetailSewa::where('id_sewa', $sewa->id)->get();
            foreach ($detailSewa as $ds) {
                Barang::where('id', $ds->id_barang)->increment('stok', $ds->qty);
            }
        }
        
        // Jika status berubah dari 'selesai' ke status lain (rollback), kurangi stok lagi
        if ($previousStatus === 'selesai' && $request->status !== 'selesai') {
            $detailSewa = DetailSewa::where('id_sewa', $sewa->id)->get();
            foreach ($detailSewa as $ds) {
                Barang::where('id', $ds->id_barang)->decrement('stok', $ds->qty);
            }
        }
        
        // Simpan catatan pengembalian jika ada
        if ($request->has('catatan_pengembalian')) {
            $sewa->catatan_pengembalian = $request->catatan_pengembalian;
        }

        $sewa->status = $request->status;
        $sewa->save();

        return redirect()->back()->with('success', 'Status sewa berhasil diperbarui!');
    }

    /**
     * Cetak Invoice
     */
    public function printInvoice($id)
    {
        $sewa = Sewa::with('user')->findOrFail($id);
        $detailSewa = DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();
        
        // Hitung status pembayaran
        $isLunas = in_array($sewa->status, ['disetujui', 'berjalan', 'selesai']);
        $isDPLunas = $sewa->status === 'dp_lunas';
        $sudahBayar = $isLunas ? $sewa->total_biaya : ($isDPLunas ? $sewa->uang_muka : 0);
        
        return view('warehouse.invoice', compact('sewa', 'detailSewa', 'isLunas', 'isDPLunas', 'sudahBayar'));
    }
}
