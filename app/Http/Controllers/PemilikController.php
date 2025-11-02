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
    public function dashboard() {
        $user = Auth::user();

        $title = 'Dashboard Warehouse';
        $totalPemilik = User::where('role_id', 1)->count();
        $totalAdmin = User::where('role_id', 2)->count();
        $totalPenyewa = User::where('role_id', 3)->count();
        $totalWarehouse = User::where('role_id', 4)->count();
        $totalBarang = Barang::count();
        $totalJenisBarang = JenisBarang::count();
        $barangs = Barang::all();
        $jenisBarang = JenisBarang::all();
        $statusCounts = Sewa::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Ambil total dari tabel sewa
        $totalBiaya = Sewa::sum('total_biaya');
        // dd($totalBiaya);
        $totalUangMuka = Sewa::sum('uang_muka');
        $totalSisa = $totalBiaya - $totalUangMuka;
        return view('pemilik.dashboard', compact('user', 'title', 'totalPemilik', 'totalAdmin', 'totalPenyewa', 'totalWarehouse', 'totalBarang', 'totalJenisBarang', 'barangs', 'jenisBarang', 'statusCounts', 'totalBiaya', 'totalUangMuka', 'totalSisa'));
    }
    public function index(Request $request) {
        $title = "Laporan";
         $sewas = [];

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $sewas = Sewa::with('detailSewas.barang')
                ->whereBetween('tgl_sewa', [$request->tanggal_awal, $request->tanggal_akhir])
                ->get();
        }

        return view('laporan.index', compact('sewas', 'title'));
    }

    public function exportPDF(Request $request)
    {
        $sewas = Sewa::with('detailSewas.barang')
            ->whereBetween('tgl_sewa', [$request->tanggal_awal, $request->tanggal_akhir])
            ->get();

        $pdf = Pdf::loadView('laporan.pdf', compact('sewas'));
        return $pdf->download('laporan_sewa.pdf');
    }
}
