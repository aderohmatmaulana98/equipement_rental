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

        $title = 'User Dashboard';

        $userId = Auth::id(); // ambil user login saat ini
        $barangs = Barang::all();
        // Ambil semua sewa milik user
        $sewas = Sewa::with('barang') // pastikan relasi 'barang' ada di model Sewa
            ->where('id_user', $userId)
            ->orderBy('tgl_sewa', 'desc')
            ->get();

        // Hitung total keseluruhan
        $totalBiaya = $sewas->sum('total_biaya');
        $totalUangMuka = $sewas->sum('uang_muka');
        $totalSisa = $totalBiaya - $totalUangMuka;

        return view('user.dashboard', compact('sewas', 'totalBiaya', 'totalUangMuka', 'totalSisa', 'title','barangs'));
    }
    public function list_barang()
    {
        $title = 'Ketersediaan Barang';
        $barangs = Barang::all();
        $jenisBarang = JenisBarang::all();
        return view('user.list_barang', compact('barangs', 'jenisBarang', 'title'));
    }
}
