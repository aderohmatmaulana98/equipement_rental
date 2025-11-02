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
        $barangs = Barang::all();
        $jenisBarang = JenisBarang::all();

   $totalPemilik = User::where('role_id', 1)->count();
    $totalAdmin = User::where('role_id', 2)->count();
    $totalPenyewa = User::where('role_id', 3)->count();
    $totalWarehouse = User::where('role_id', 4)->count();
       $totalBarang = Barang::count();
    $totalJenisBarang = JenisBarang::count();


        $title = 'Admin Dashboard';
        return view('admin.dashboard', compact('user', 'title', 'barangs', 'jenisBarang', 'totalPemilik', 'totalAdmin', 'totalPenyewa', 'totalWarehouse', 'totalBarang', 'totalJenisBarang'));
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
}
