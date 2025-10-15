<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailSewa;
use App\Models\Sewa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Sewa Barang';
        $sewas = Sewa::where('id_user', auth()->user()->id)->get();
        return view('sewa.index', compact('title', 'sewas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Tambah Sewa';
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('sewa.create', compact('title', 'barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tgl_sewa' => 'required|date',
            'tgl_acara' => 'required|date',
            'jam_acara' => 'required',
            'tgl_loading' => 'required|date',
            'jam_loading' => 'required',
            'tgl_loading_out' => 'required|date',
            'alamat_acara' => 'required|string',
            'barang_id' => 'required|array|min:1',
            'qty' => 'array', // jika nanti ada input qty per barang
        ]);

        DB::beginTransaction();

        try {
            // 🔹 1️⃣ Buat data utama sewa
            $kodeSewa = 'SEWA-' . date('Ymd') . '-' . str_pad(Sewa::count() + 1, 3, '0', STR_PAD_LEFT);

            $sewa = Sewa::create([
                'kode_sewa' => $kodeSewa,
                'tgl_sewa' => $request->tgl_sewa,
                'tgl_acara' => $request->tgl_acara,
                'jam_acara' => $request->jam_acara,
                'tgl_loading' => $request->tgl_loading,
                'jam_loading' => $request->jam_loading,
                'tgl_loading_out' => $request->tgl_loading_out,
                'alamat_acara' => $request->alamat_acara,
                'batas_waktu_pembayaran' => now()->addHour(), // ⏰ lebih rapi
                'total_biaya' => 0,
                'uang_muka' => 0,
                'status' => 'belum bayar',
                'id_user' => auth()->id(), // ✅ cara singkat untuk ambil ID user
            ]);

            $totalHarga = 0;

            // 🔹 2️⃣ Simpan detail sewa dan hitung total
            foreach ($request->barang_id as $barangId) {
                $barang = Barang::findOrFail($barangId);

                if ($barang->stok <= 0) {
                    throw new \Exception("Stok barang {$barang->nama_barang} habis.");
                }

                // Default qty = 1, nanti bisa diubah jika kamu ingin input qty di form
                $qty = isset($request->qty[$barangId]) ? (int)$request->qty[$barangId] : 1;

                if ($qty > $barang->stok) {
                    throw new \Exception("Jumlah sewa untuk {$barang->nama_barang} melebihi stok tersedia.");
                }

                $subtotal = $barang->harga * $qty;

                DetailSewa::create([
                    'id_sewa' => $sewa->id,
                    'id_barang' => $barang->id,
                    'qty' => $qty,
                    'harga_satuan' => $barang->harga,
                    'subtotal' => $subtotal,
                    'keterangan' => $barang->keterangan ?? '-',
                ]);

                // 🔹 Kurangi stok barang
                $barang->decrement('stok', $qty);

                $totalHarga += $subtotal;
            }

            // 🔹 3️⃣ Update total harga & uang muka
            $sewa->update([
                'total_biaya' => $totalHarga,
                'uang_muka' => $totalHarga * 0.5, // contoh: 50% DP
            ]);

            DB::commit();

            return redirect()->route('sewa.index')->with('success', 'Data sewa berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function confirm_pay(Request $request, $id) {
        $request->validate([
            'no_rekening' => 'required',
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $sewa = Sewa::findOrFail($id);

        $sewa->update([
            'status' => 'pending',
            'no_rekening' => $request->no_rekening,
            'sisa_pembayaran' =>$request->sisa_pembayaran,
            'tgl_pembayaran' => now(),
            'bukti_pembayaran' => $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public')
        ]);

        return redirect()->route('sewa.index')->with('success', 'Data konfirmasi berhasil disimpan.');
    }
}
