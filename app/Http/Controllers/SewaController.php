<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailSewa;
use App\Models\Sewa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $title = 'Sewa Barang';
        
        // Handle return dari Midtrans payment redirect
        if ($request->filled('order_id')) {
            $orderId = $request->query('order_id');
            $status = $request->query('transaction_status');
            
            $updatedSewa = PaymentController::processPaymentStatus($orderId, $status);
            
            if ($updatedSewa) {
                $isLunas = str_contains($orderId, '-LUNAS-');
                $msg = $isLunas
                    ? 'Pembayaran pelunasan berhasil! Status sewa Anda telah disetujui (Lunas).'
                    : 'Pembayaran DP 50% berhasil diverifikasi! Status sewa sekarang DP Lunas. Anda dapat melakukan pelunasan sebelum hari acara.';
                
                return redirect()->route('sewa.index')->with('success', $msg);
            }
        }
        
        // Auto-update status expired untuk sewa yang sudah lewat batas waktu pembayaran
        Sewa::where('id_user', auth()->id())
            ->where('status', 'belum bayar')
            ->whereNotNull('batas_waktu_pembayaran')
            ->where('batas_waktu_pembayaran', '<', now())
            ->update(['status' => 'expired']);
        
        $sewas = Sewa::where('id_user', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('sewa.index', compact('title', 'sewas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Pilih Peralatan Sewa';
        $barangs = Barang::with('jenisBarang')->where('stok', '>', 0)->get();
        $categories = \App\Models\JenisBarang::all();
        $cart = session()->get('cart', []);
        return view('sewa.create', compact('title', 'barangs', 'categories', 'cart'));
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

    public function checkout(Request $request)
    {
        $request->validate([
            'tgl_sewa' => 'required|date',
            'tgl_acara' => 'required|date',
            'jam_acara' => 'required',
            'tgl_loading' => 'required|date',
            'jam_loading' => 'required',
            'tgl_loading_out' => 'required|date|after_or_equal:tgl_acara',
            'alamat_acara' => 'required|string',
            'uang_muka' => 'nullable|numeric|min:0',
        ], [
            'tgl_loading_out.after_or_equal' => 'Tanggal Loading Out harus sama dengan atau setelah Tanggal Acara.',
            'uang_muka.numeric' => 'Nominal DP harus berupa angka.',
            'uang_muka.min' => 'Nominal DP tidak boleh kurang dari 0.',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'Keranjang kosong!'], 400);
        }

        // Validasi stok barang di keranjang sebelum checkout
        foreach ($cart as $item) {
            $barang = Barang::find($item['id']);
            if (!$barang || $barang->stok < $item['qty']) {
                $stokTersedia = $barang ? $barang->stok : 0;
                $namaBarang = $item['nama'] ?? ($barang ? $barang->nama_barang : 'Barang');
                return back()->withInput()->with('error', "Stok barang '{$namaBarang}' tidak mencukupi (tersedia: {$stokTersedia}). Silakan sesuaikan kuantitas di keranjang.");
            }
        }

        DB::beginTransaction();

        try {
            $kodeSewa = 'SEWA-' . date('Ymd') . '-' . str_pad(Sewa::count() + 1, 3, '0', STR_PAD_LEFT).'-'.uniqid();

            $tglAcara = Carbon::parse($request->tgl_acara);
            $tglLoadingOut = Carbon::parse($request->tgl_loading_out);

            // Hitung durasi hari sewa dari tgl_acara sampai tgl_loading_out (inklusif)
            // Tanggal sama = 1 hari, tanggal 11 ke 12 = 2 hari, dst.
            $durasiHari = max(1, $tglAcara->diffInDays($tglLoadingOut) + 1);

            // Hitung total harga dari keranjang: harga × qty × durasi
            $subtotalBarang = collect($cart)->sum(fn($item) => $item['harga'] * $item['qty']);
            $totalBiaya = $subtotalBarang * $durasiHari;
            
            // Hitung DP (Uang Muka) - Minimal 50% dari total sewa
            $minDP = round($totalBiaya * 0.5);
            if ($request->filled('uang_muka')) {
                $uangMuka = (float) $request->uang_muka;
                if ($totalBiaya > 0 && $uangMuka < ($totalBiaya * 0.5 - 0.01)) {
                    return back()->withInput()->with('error', "Nominal DP minimal 50% dari total sewa (Minimal Rp " . number_format($minDP, 0, ',', '.') . ").");
                }
                if ($uangMuka > $totalBiaya) {
                    return back()->withInput()->with('error', "Nominal DP tidak boleh melebihi total sewa (Maksimal Rp " . number_format($totalBiaya, 0, ',', '.') . ").");
                }
            } else {
                $uangMuka = $minDP;
            }
            $sisaPembayaran = max(0, $totalBiaya - $uangMuka);

            // Buat data sewa
            $sewa = Sewa::create([
                'kode_sewa' => $kodeSewa,
                'tgl_sewa' => $request->tgl_sewa,
                'tgl_acara' => $request->tgl_acara,
                'jam_acara' => $request->jam_acara,
                'tgl_loading' => $request->tgl_loading,
                'jam_loading' => $request->jam_loading,
                'tgl_loading_out' => $request->tgl_loading_out,
                'alamat_acara' => $request->alamat_acara,
                'batas_waktu_pembayaran' => now()->addHour(),
                'total_biaya' => $totalBiaya,
                'uang_muka' => $uangMuka,
                'sisa_pembayaran' => $sisaPembayaran,
                'status' => 'belum bayar',
                'id_user' => auth()->id(),
            ]);

            // Simpan detail sewa per item
            foreach ($cart as $item) {
                DetailSewa::create([
                    'id_sewa' => $sewa->id,
                    'id_barang' => $item['id'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['harga'] * $item['qty'] * $durasiHari,
                ]);
            }

            DB::commit();
            session()->forget('cart');

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
        $title = 'Detail Sewa';
        $sewa = Sewa::findOrFail($id);
        $detailSewa = DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();

        return view('user.detail_sewa', compact('detailSewa', 'title', 'sewa'));
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

    public function printInvoice($id)
    {
        $sewa = Sewa::with('user')->findOrFail($id);

        // Pastikan hanya user bersangkutan yang bisa cetak invoice
        if ($sewa->id_user !== auth()->id()) {
            abort(403);
        }

        $detailSewa = DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();
        
        // Hitung status pembayaran
        $isLunas = in_array($sewa->status, ['disetujui', 'berjalan', 'selesai']);
        $isDPLunas = $sewa->status === 'dp_lunas';
        $sudahBayar = $isLunas ? $sewa->total_biaya : ($isDPLunas ? $sewa->uang_muka : 0);
        
        return view('user.invoice_sewa', compact('sewa', 'detailSewa', 'isLunas', 'isDPLunas', 'sudahBayar'));
    }
}
