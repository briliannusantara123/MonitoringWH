<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function users()
    {
        $users = User::all();
        $data = [
            'title' => 'Users',
            'users' => $users,
        ];
        return view('pages.users',$data);
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function create_user(Request $request)
    {
        User::create([
            'name' => $request->input('name'),
            'role' => $request->input('role'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        return redirect()->intended('/users')->with('success', 'Berhasil menambahkan user baru.');

    }
    public function update_users(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
            'role' => 'required|string',
        ]);

        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Update data user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Update password jika field password tidak kosong
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }
    public function hapus_users($id)
    {
        $user = User::findOrFail($id); // Cari user berdasarkan ID
        $user->delete(); // Hapus user

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required', // Menggunakan 'name' sebagai input
            'password' => 'required'
        ]);

        // Cek kredensial
        $credentials = $request->only('name', 'password'); // Menggunakan 'name' alih-alih 'email'
        
        if (Auth::attempt($credentials)) {
            // Jika berhasil, regenerate session dan redirect ke dashboard
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'name' => 'Nama pengguna atau password salah.',
        ])->onlyInput('name');
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
