<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Barang;
use App\Models\JenisBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $title = 'Admin Dashboard';

        // 1. Statistik Pengguna
        $totalPenyewa = User::where('role_id', 3)->count();
        $totalBarang = Barang::count();

        // 2. Statistik Transaksi
        $totalSewa = \App\Models\Sewa::count();
        $sewaAktif = \App\Models\Sewa::whereIn('status', ['dp_lunas', 'disetujui', 'berjalan'])->count();
        $menungguPembayaran = \App\Models\Sewa::where('status', 'belum bayar')->count();

        // 3. Statistik Keuangan (Hanya dari transaksi valid)
        $totalPendapatan = \App\Models\Sewa::whereIn('status', ['dp_lunas', 'disetujui', 'berjalan', 'selesai'])
            ->sum('total_biaya');
            
        $pendapatanBulanIni = \App\Models\Sewa::whereIn('status', ['dp_lunas', 'disetujui', 'berjalan', 'selesai'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_biaya');

        // 4. Data Grafik Pendapatan Bulanan (6 bulan terakhir)
        $chartData = \App\Models\Sewa::whereIn('status', ['dp_lunas', 'disetujui', 'berjalan', 'selesai'])
            ->selectRaw('MONTH(created_at) as bulan, SUM(total_biaya) as total')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $bulanLabels = [];
        $bulanData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan = $date->month;
            $bulanLabels[] = $date->format('F');
            $bulanData[] = $chartData[$bulan] ?? 0;
        }

        // 5. Transaksi Terbaru
        $sewaTerbaru = \App\Models\Sewa::with('user')->orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact(
            'user', 
            'title', 
            'totalPenyewa', 
            'totalBarang', 
            'totalSewa', 
            'sewaAktif', 
            'menungguPembayaran',
            'totalPendapatan',
            'pendapatanBulanIni',
            'bulanLabels',
            'bulanData',
            'sewaTerbaru'
        ));
    }
    public function admin()
    {
        $title = 'Admin Management';
        $users = User::with('role') // eager load relasi
            ->whereNotIn('role_id', [1, 3])
            ->get();
        $roles = Role::whereIn('id', [2, 4])->get();
        return view('admin.admin', compact('users', 'roles', 'title'));
    }
    public function admin_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirmationPassword' => 'required|same:password', // konfirmasi harus sama
        ], [
            'confirmationPassword.same' => 'Konfirmasi password tidak cocok dengan password.',
        ]);

        $user = User::create([
            'role_id' => $request->role_id,
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            return redirect()->route('admin.admin')->with('success', 'Pendaftaran akun admin berhasil! Silakan login.');
        } else {
            return back()->with('error', 'Pendaftaran gagal! Silakan coba lagi.');
        }
    }
    public function admin_delete($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.admin')->with('success', 'User berhasil dihapus.');
    }

    public function admin_update(Request $request, $id)
    {
        $users = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required',
            'role_id' => 'required',
            'email' => 'required|email',
        ]);


        $users->update([
            'name' => $request->name,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.admin')->with('success', 'Admin berhasil diperbarui.');
    }


    public function customer()
    {
        $title = 'Customer Management';
        $users = User::with('role') // eager load relasi
            ->whereNotIn('role_id', [1, 2, 4])
            ->get();
        return view('admin.customer', compact('users', 'title'));
    }
    public function customer_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirmationPassword' => 'required|same:password', // konfirmasi harus sama
        ], [
            'confirmationPassword.same' => 'Konfirmasi password tidak cocok dengan password.',
        ]);

        $user = User::create([
            'role_id' => 3,
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password),
        ]);

        if ($user) {
            return redirect()->route('customer.customer')->with('success', 'Pendaftaran akun customer berhasil! Silakan login.');
        } else {
            return back()->with('error', 'Pendaftaran gagal! Silakan coba lagi.');
        }
    }
    public function customer_delete($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('customer.customer')->with('success', 'User berhasil dihapus.');
    }

    public function customer_update(Request $request, $id)
    {
        $users = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'no_hp' => 'required',
            'email' => 'required|email',
        ]);


        $users->update([
            'name' => $request->name,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
        ]);

        return redirect()->route('customer.customer')->with('success', 'Customer berhasil diperbarui.');
    }

    // =============================================
    // PENYEWAAN MANAGEMENT
    // =============================================

    public function penyewaan()
    {
        $title = 'Sewa Barang';
        $sewas = \App\Models\Sewa::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.penyewaan', compact('title', 'sewas'));
    }

    public function showSewa($id)
    {
        $title = 'Detail Sewa Barang';
        $sewa = \App\Models\Sewa::with('user')->findOrFail($id);
        $detailSewa = \App\Models\DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();
        return view('admin.detail_sewa', compact('title', 'detailSewa', 'sewa'));
    }

    public function updateStatusSewa(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sewas,id',
            'status' => 'required|in:pending,disetujui,dibatalkan',
        ]);

        $sewa = \App\Models\Sewa::findOrFail($request->id);
        $previousStatus = $sewa->status;
        
        // Jika status berubah menjadi 'selesai', kembalikan stok barang
        if ($request->status === 'selesai' && $previousStatus !== 'selesai') {
            $detailSewa = \App\Models\DetailSewa::where('id_sewa', $sewa->id)->get();
            foreach ($detailSewa as $ds) {
                Barang::where('id', $ds->id_barang)->increment('stok', $ds->qty);
            }
        }
        
        // Jika status berubah dari 'selesai' ke status lain (rollback), kurangi stok lagi
        if ($previousStatus === 'selesai' && $request->status !== 'selesai') {
            $detailSewa = \App\Models\DetailSewa::where('id_sewa', $sewa->id)->get();
            foreach ($detailSewa as $ds) {
                Barang::where('id', $ds->id_barang)->decrement('stok', $ds->qty);
            }
        }
        
        $sewa->status = $request->status;
        $sewa->save();

        return redirect()->back()->with('success', 'Status sewa berhasil diperbarui!');
    }

    public function printInvoiceSewa($id)
    {
        $sewa = \App\Models\Sewa::with('user')->findOrFail($id);
        $detailSewa = \App\Models\DetailSewa::with('barang')->where('id_sewa', $sewa->id)->get();
        
        // Hitung status pembayaran
        $isLunas = in_array($sewa->status, ['disetujui', 'berjalan', 'selesai']);
        $isDPLunas = $sewa->status === 'dp_lunas';
        $sudahBayar = $isLunas ? $sewa->total_biaya : ($isDPLunas ? $sewa->uang_muka : 0);
        
        return view('admin.invoice_sewa', compact('sewa', 'detailSewa', 'isLunas', 'isDPLunas', 'sudahBayar'));
    }

    // =============================================
    // SEWA OFFLINE (Walk-in Customer)
    // =============================================

    public function createSewa()
    {
        $title = 'Tambah Sewa Offline';
        $customers = User::where('role_id', 3)->orderBy('name')->get();
        $barangs = Barang::where('stok', '>', 0)->get();
        return view('admin.create_sewa', compact('title', 'customers', 'barangs'));
    }

    public function storeSewa(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tgl_sewa' => 'required|date',
            'tgl_acara' => 'required|date',
            'jam_acara' => 'required',
            'tgl_loading' => 'required|date',
            'jam_loading' => 'required',
            'tgl_loading_out' => 'required|date',
            'alamat_acara' => 'required|string',
            'barang_id' => 'required|array|min:1',
            'qty' => 'required|array',
            'status_pembayaran' => 'required|in:belum_bayar,dp_lunas,lunas',
        ]);

        DB::beginTransaction();

        try {
            $kodeSewa = 'SEWA-' . date('Ymd') . '-' . str_pad(\App\Models\Sewa::count() + 1, 3, '0', STR_PAD_LEFT) . '-OFF';
            
            $tglAcara = \Carbon\Carbon::parse($request->tgl_acara);
            $tglLoadingOut = \Carbon\Carbon::parse($request->tgl_loading_out);
            $durasiHari = $tglAcara->diffInDays($tglLoadingOut);
            $durasiHari = $durasiHari > 0 ? $durasiHari : 1;

            // Hitung total dari barang
            $totalBiaya = 0;
            $detailItems = [];
            
            foreach ($request->barang_id as $index => $barangId) {
                $barang = Barang::findOrFail($barangId);
                $qty = (int) $request->qty[$index];
                
                if ($qty > $barang->stok) {
                    throw new \Exception("Jumlah sewa untuk {$barang->nama_barang} melebihi stok tersedia ({$barang->stok}).");
                }
                
                $subtotal = $barang->harga * $qty * $durasiHari;
                $totalBiaya += $subtotal;
                
                $detailItems[] = [
                    'id_barang' => $barangId,
                    'qty' => $qty,
                    'harga_satuan' => $barang->harga,
                    'subtotal' => $subtotal,
                ];
            }

            $uangMuka = $totalBiaya * 0.5;
            $sisaPembayaran = $totalBiaya - $uangMuka;

            // Determine status based on payment
            $status = 'belum bayar';
            if ($request->status_pembayaran === 'dp_lunas') {
                $status = 'dp_lunas';
            } elseif ($request->status_pembayaran === 'lunas') {
                $status = 'disetujui';
            }

            // Create sewa
            $sewa = \App\Models\Sewa::create([
                'kode_sewa' => $kodeSewa,
                'tgl_sewa' => $request->tgl_sewa,
                'tgl_acara' => $request->tgl_acara,
                'jam_acara' => $request->jam_acara,
                'tgl_loading' => $request->tgl_loading,
                'jam_loading' => $request->jam_loading,
                'tgl_loading_out' => $request->tgl_loading_out,
                'alamat_acara' => $request->alamat_acara,
                'batas_waktu_pembayaran' => now()->addDay(),
                'total_biaya' => $totalBiaya,
                'uang_muka' => $uangMuka,
                'sisa_pembayaran' => $sisaPembayaran,
                'status' => $status,
                'id_user' => $request->id_user,
            ]);

            // Create detail sewa and reduce stock
            foreach ($detailItems as $item) {
                \App\Models\DetailSewa::create([
                    'id_sewa' => $sewa->id,
                    'id_barang' => $item['id_barang'],
                    'qty' => $item['qty'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $item['subtotal'],
                ]);
                
                // Kurangi stok
                Barang::where('id', $item['id_barang'])->decrement('stok', $item['qty']);
            }

            DB::commit();

            return redirect()->route('admin.penyewaan')->with('success', 'Data sewa offline berhasil disimpan dengan kode: ' . $kodeSewa);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
