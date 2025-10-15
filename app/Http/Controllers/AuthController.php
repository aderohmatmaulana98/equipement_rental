<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()  {

        return view('auth.login');
    }
    public function login_action(Request $request) 
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            Session::put('id_role', $user->role_id);
            
            // Set flash message berhasil login
            if ($user->role_id == 1) {
                return redirect()->route('pemilik.dashboard')->with('success', 'Login berhasil! Selamat datang kembali, ' . $user->name);
            } elseif ($user->role_id == 2) {
                return redirect()->route('admin.dashboard')->with('success', 'Login berhasil! Selamat datang kembali, ' . $user->name);
            }
            elseif ($user->role_id == 4) {
                return redirect()->route('warehouse.dashboard')->with('success', 'Login berhasil! Selamat datang kembali, ' . $user->name);
            }else{

                return redirect()->route('user.dashboard')->with('success', 'Login berhasil! Selamat datang kembali, ' . $user->name);
            }
        }

        // Jika login gagal
        return back()->with('error', 'Login gagal! Silakan coba lagi.');
    }  
    
    public function not_authorized() {
        return view('auth.not_authorized');
    }

    public function logout(Request $request)
    {
        // Simpan nama user sebelum logout untuk pesan
        $userName = Auth::check() ? Auth::user()->name : 'User';
        
        // Logout user
        Auth::logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Selamat tinggal, ' . $userName . '. Anda telah berhasil logout.');
    }

    public function signup() {
        return view('auth.signup');
    }

    public function signup_action(Request $request)
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
            return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login.');
        }else{
            return back()->with('error', 'Pendaftaran gagal! Silakan coba lagi.');
        }

    }
}
