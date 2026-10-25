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

        if ($barang->stok < $qty) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok {$barang->nama_barang} tidak mencukupi (tersedia: {$barang->stok})."
                ], 400);
            }
            return redirect()->back()->with('error', 'Stok barang tidak mencukupi.');
        }

        if (isset($cart[$barang->id])) {
            $newQty = $cart[$barang->id]['qty'] + $qty;
            if ($newQty > $barang->stok) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Jumlah di keranjang ({$cart[$barang->id]['qty']}) ditambah {$qty} melebihi stok tersedia ({$barang->stok})."
                    ], 400);
                }
                return redirect()->back()->with('error', 'Jumlah total melebihi stok barang.');
            }
            $cart[$barang->id]['qty'] = $newQty;
        } else {
            $cart[$barang->id] = [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'harga_asli' => $barang->harga,
                'harga' => $barang->getHargaDiskon(), // Use discounted price
                'has_diskon' => $barang->isDiskonAktif(),
                'diskon_persen' => $barang->diskon_persen,
                'diskon_nominal' => $barang->diskon_nominal,
                'stok' => $barang->stok,
                'qty' => $qty,
                'gambar' => $barang->gambar,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            $totalCount = count($cart);
            $totalQty = collect($cart)->sum('qty');
            $totalHarga = collect($cart)->sum(fn($item) => $item['harga'] * $item['qty']);

            return response()->json([
                'success' => true,
                'message' => "{$barang->nama_barang} berhasil ditambahkan ke keranjang.",
                'cart' => $cart,
                'item_id' => $barang->id,
                'current_qty' => $cart[$barang->id]['qty'],
                'total_items' => $totalCount,
                'total_qty' => $totalQty,
                'total_harga' => $totalHarga,
                'formatted_total' => 'Rp ' . number_format($totalHarga, 0, ',', '.'),
            ]);
        }

        return redirect()->back()->with('success', "{$barang->nama_barang} berhasil ditambahkan ke keranjang.");
    }

    public function show()
    {
        $title = 'Keranjang Sewa';
        $cart = session()->get('cart', []);
        return view('cart.show', compact('title', 'cart'));
    }

    public function update(Request $request)
    {
        $barangId = $request->barang_id;
        $qty = (int)$request->qty;
        $cart = session()->get('cart', []);

        if (!isset($cart[$barangId])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan di keranjang.'], 404);
            }
            return redirect()->back();
        }

        $barang = Barang::find($barangId);
        if ($qty <= 0) {
            unset($cart[$barangId]);
        } else {
            if ($barang && $qty > $barang->stok) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Jumlah melebihi stok yang tersedia ({$barang->stok})."
                    ], 400);
                }
                return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
            $cart[$barangId]['qty'] = $qty;
        }

        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            $totalCount = count($cart);
            $totalQty = collect($cart)->sum('qty');
            $totalHarga = collect($cart)->sum(fn($item) => $item['harga'] * $item['qty']);

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui.',
                'cart' => $cart,
                'total_items' => $totalCount,
                'total_qty' => $totalQty,
                'total_harga' => $totalHarga,
                'formatted_total' => 'Rp ' . number_format($totalHarga, 0, ',', '.'),
            ]);
        }

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function remove(Request $request)
    {
        $cart = session()->get('cart', []);
        $name = isset($cart[$request->barang_id]) ? $cart[$request->barang_id]['nama_barang'] : 'Barang';
        unset($cart[$request->barang_id]);
        session()->put('cart', $cart);

        if ($request->wantsJson() || $request->ajax()) {
            $totalCount = count($cart);
            $totalQty = collect($cart)->sum('qty');
            $totalHarga = collect($cart)->sum(fn($item) => $item['harga'] * $item['qty']);

            return response()->json([
                'success' => true,
                'message' => "{$name} berhasil dihapus dari keranjang.",
                'cart' => $cart,
                'total_items' => $totalCount,
                'total_qty' => $totalQty,
                'total_harga' => $totalHarga,
                'formatted_total' => 'Rp ' . number_format($totalHarga, 0, ',', '.'),
            ]);
        }

        return redirect()->back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }
}
