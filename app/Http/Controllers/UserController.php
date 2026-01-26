<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {

        $title = 'Dashboard Saya';
        $userId = Auth::id(); 
        
        // 1. Ambil 5 sewa terbaru untuk tabel
        $sewas = Sewa::with('barang')
            ->where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 2. Statistik
        $totalSewa = Sewa::where('id_user', $userId)->count();
        $sewaAktif = Sewa::where('id_user', $userId)
            ->whereIn('status', ['pending', 'dp_lunas', 'disetujui', 'berjalan'])
            ->count();
            
        $menungguPembayaran = Sewa::where('id_user', $userId)
            ->where('status', 'belum bayar')
            ->count();

        $totalPengeluaran = Sewa::where('id_user', $userId)
            ->whereIn('status', ['dp_lunas', 'disetujui', 'selesai'])
            ->sum(Sewa::raw('uang_muka + (CASE WHEN status = "selesai" OR status = "disetujui" THEN sisa_pembayaran ELSE 0 END)'));
            
        // Jika logic total pengeluaran mau simpel (berdasarkan total_biaya sewa yang sukses)
        // $totalPengeluaran = Sewa::where('id_user', $userId)->whereIn('status', ['selesai'])->sum('total_biaya'); 
        // Tapi mari gunakan perhitungan sederhana total biaya dari transaksi yang valid
        $totalPengeluaran = Sewa::where('id_user', $userId)
             ->whereIn('status', ['dp_lunas', 'disetujui', 'berjalan', 'selesai'])
             ->sum('total_biaya');

        // 3. Data Grafik Pengeluaran per Bulan (6 bulan terakhir)
        $chartData = Sewa::where('id_user', $userId)
            ->whereIn('status', ['dp_lunas', 'disetujui', 'berjalan', 'selesai'])
            ->selectRaw('MONTH(tgl_sewa) as bulan, SUM(total_biaya) as total')
            ->where('tgl_sewa', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');
            
        $bulanLabels = [];
        $bulanData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan = $date->month;
            $bulanLabels[] = $date->format('F'); // Januari, Februari, dst
            $bulanData[] = $chartData[$bulan] ?? 0;
        }

        return view('user.dashboard', compact(
            'title', 
            'sewas', 
            'totalSewa', 
            'sewaAktif', 
            'menungguPembayaran', 
            'totalPengeluaran',
            'bulanLabels',
            'bulanData'
        ));
    }
    public function list_barang()
    {
        $title = 'Ketersediaan Barang';
        $barangs = Barang::all();
        $jenisBarang = JenisBarang::all();
        return view('user.list_barang', compact('barangs', 'jenisBarang', 'title'));
    }
}
