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
        return view('warehouse.dashboard', compact('user', 'title', 'totalPemilik', 'totalAdmin', 'totalPenyewa', 'totalWarehouse', 'totalBarang', 'totalJenisBarang', 'barangs', 'jenisBarang', 'statusCounts', 'totalBiaya', 'totalUangMuka', 'totalSisa'));
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
        $sewas = Sewa::all();
        return view('warehouse.penyewaan', compact('title', 'sewas'));
    }

    public function show($id)
    {
        $title = 'Detail Sewa Barang';
        $sewa = Sewa::findOrFail($id);
        $detailSewa = DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();
        // dd($detailSewa);
        return view('warehouse.detail', compact('title', 'detailSewa', 'sewa'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sewas,id',
            'status' => 'required|in:pending,disetujui,berjalan,selesai,dibatalkan',
        ]);

        $sewa = Sewa::findOrFail($request->id);
        $sewa->status = $request->status;
        $sewa->save();

        return redirect()->back()->with('success', 'Status sewa berhasil diperbarui!');
    }
}
