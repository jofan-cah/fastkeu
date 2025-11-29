<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        // Kalau udah login, redirect ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string',
            'password' => 'required',
        ], [
            'nip.required' => 'NIP wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);


        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->only('nip', 'remember'));
        }

        // Ambil credentials
        $credentials = $request->only('nip', 'password');
        $remember = $request->has('remember');

        // Cek user aktif
        $user = User::where('nip', $request->nip)->first();
        // dd($validator);

        if ($user && !$user->is_active) {
            return redirect()->back()
                ->withErrors(['nip' => 'Akun Anda tidak aktif. Hubungi administrator.'])
                ->withInput($request->only('nip'));
        }

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Log activity
            logActivity('login', Auth::user()->full_name . ' logged in');
                    // dd('Login berhasil', Auth::check(), Auth::user());

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . Auth::user()->full_name . '!');
        }

        // Login gagal
        return redirect()->back()
            ->withErrors(['nip' => 'NIP atau password salah.'])
            ->withInput($request->only('nip', 'remember'));
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Log activity before logout
        logActivity('logout', Auth::user()->full_name . ' logged out');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }
}
