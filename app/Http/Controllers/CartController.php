<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class CartController extends Controller
{
    public function index()
    {
        $title = 'Daftar Barang';
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('barang.index', compact('title', 'barangs'));
    }

    public function add(Request $request)
    {
        $barang = Barang::findOrFail($request->barang_id);
        $qty = max(1, (int)$request->qty);

        $cart = session()->get('cart', []);

        if (isset($cart[$barang->id])) {
            $cart[$barang->id]['qty'] += $qty;
        } else {
            $cart[$barang->id] = [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'harga' => $barang->harga,
                'stok' => $barang->stok,
                'qty' => $qty,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', "{$barang->nama_barang} berhasil ditambahkan ke keranjang.");
    }

    public function show()
    {
        $title = 'Keranjang Sewa';
        $cart = session()->get('cart', []);
        return view('cart.show', compact('title', 'cart'));
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        unset($cart[$request->barang_id]);
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }
}
