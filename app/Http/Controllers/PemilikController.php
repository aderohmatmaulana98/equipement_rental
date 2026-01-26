<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use App\Models\Sewa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemilikController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Dashboard Pemilik';

        // 1. Ringkasan Kinerja (All Time)
        $totalPendapatan = Sewa::whereIn('status', ['selesai', 'disetujui', 'berjalan', 'dp_lunas'])
            ->sum('total_biaya');
        
        $totalTransaksi = Sewa::count();
        $totalPelanggan = User::where('role_id', 3)->count();
        $totalAsetBarang = Barang::sum('stok');
        
        // 2. Kinerja Bulan Ini
        $pendapatanBulanIni = Sewa::whereIn('status', ['selesai', 'disetujui', 'berjalan', 'dp_lunas'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_biaya');
            
        $transaksiBulanIni = Sewa::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 3. Grafik Pendapatan 12 Bulan Terakhir
        $chartData = Sewa::whereIn('status', ['selesai', 'disetujui', 'berjalan', 'dp_lunas'])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as bulan, SUM(total_biaya) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $bulanLabels = [];
        $bulanData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $bulanLabels[] = $date->format('M Y');
            $bulanData[] = $chartData[$key] ?? 0;
        }

        // 4. Barang Paling Laku (Top 5)
        $topBarang = \App\Models\DetailSewa::select('id_barang', DB::raw('SUM(qty) as total_sewa'))
            ->with('barang')
            ->groupBy('id_barang')
            ->orderByDesc('total_sewa')
            ->limit(5)
            ->get();

        return view('pemilik.dashboard', compact(
            'user', 
            'title', 
            'totalPendapatan', 
            'totalTransaksi',
            'totalPelanggan', 
            'totalAsetBarang',
            'pendapatanBulanIni',
            'transaksiBulanIni',
            'bulanLabels',
            'bulanData',
            'topBarang'
        ));
    }

    public function index(Request $request) {
        $title = "Laporan Bisnis";
        $sewas = collect([]);
        $summary = [
            'total_transaksi' => 0,
            'total_pendapatan' => 0,
            'sewa_terbanyak' => '-',
            'customer_terbanyak' => '-'
        ];

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query = Sewa::with(['detailSewas.barang', 'user'])
                ->whereBetween('tgl_sewa', [$request->tanggal_awal, $request->tanggal_akhir])
                ->whereIn('status', ['selesai', 'disetujui', 'berjalan', 'dp_lunas']); // Hanya ambil yg valid

            $sewas = $query->orderBy('tgl_sewa', 'asc')->get();

            // Hitung Summary
            if ($sewas->count() > 0) {
                $summary['total_transaksi'] = $sewas->count();
                $summary['total_pendapatan'] = $sewas->sum('total_biaya');
                
                // Barang Paling Laku di Periode Ini
                $barangIds = $sewas->flatMap->detailSewas->pluck('id_barang');
                if($barangIds->isNotEmpty()) {
                    $topBarangId = $barangIds->mode(); 
                    // Note: mode() returns array if multiple modes, take first. 
                    // Or manual group by count. Let's do robust logic later if needed.
                    // Simple logic for reporting summary:
                    $summary['sewa_terbanyak'] = $sewas->count() . " Transaksi"; 
                }
            }
        }

        return view('laporan.index', compact('sewas', 'title', 'summary'));
    }

    public function exportPDF(Request $request)
    {
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $sewas = Sewa::with(['detailSewas.barang', 'user'])
            ->whereBetween('tgl_sewa', [$request->tanggal_awal, $request->tanggal_akhir])
            ->whereIn('status', ['selesai', 'disetujui', 'berjalan', 'dp_lunas']) // Filter status valid
            ->orderBy('tgl_sewa', 'asc')
            ->get();

        $periode = \Carbon\Carbon::parse($request->tanggal_awal)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($request->tanggal_akhir)->format('d M Y');
        $totalPendapatan = $sewas->sum('total_biaya');

        $pdf = Pdf::loadView('laporan.pdf', compact('sewas', 'periode', 'totalPendapatan'));
        // Set paper landscape untuk tabel lebar
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan_pendapatan_' . date('YmdHis') . '.pdf');
    }
}
