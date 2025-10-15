<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        $role_id = $user->role_id;
        
        // Jika tidak ada role yang ditentukan, izinkan
        if (empty($roles)) {
            return $next($request);
        }

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (in_array($role_id, $roles)) {
            return $next($request);
        }

        // Jika tidak memiliki akses, redirect ke halaman yang sesuai
        return redirect()->route('unauthorized')->with('error', 'You do not have access to this page. Your role ID: ' . $role_id);
    }
}