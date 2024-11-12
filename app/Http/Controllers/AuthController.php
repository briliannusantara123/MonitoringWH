<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function create_user(){
      User::create([
        'name' => 'nusantara',
        'role' => 'operation',
        'email' => 'nusantara@gmail.com',
        'password' => Hash::make('123'),
      ]);
    }
    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cek kredensial
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            // Jika berhasil, redirect ke halaman dashboard
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
