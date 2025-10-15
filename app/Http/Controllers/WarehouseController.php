<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\JenisBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WarehouseController extends Controller
{
    public function dashboard() {
        
        $user = Auth::user();

        $title = 'Dashboard';
        return view('warehouse.dashboard', compact('user', 'title'));
    }

    public function list_barang()
    {
        $title = 'Ketersediaan Barang';
        $barangs = Barang::all();
        $jenisBarang = JenisBarang::all();
        return view('warehouse.list_barang', compact('barangs','jenisBarang','title'));
    }

}
