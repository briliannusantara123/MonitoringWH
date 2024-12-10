<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required',
            'password' => 'required'
        ]);

        // Cek kredensial
        $credentials = $request->only('name', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Jika gagal, kembali ke halaman login dengan pesan error
        return back()->withErrors([
            'name' => 'Incorrect username or password',
        ])->onlyInput('name');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect('/login')->with('error', 'Email not found');
        }

        $token = Str::random(64); // Generate reset token

        // Simpan token di database atau buat table `password_resets`
        \DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        try {
            // Kirim email menggunakan Laravel Mail
            Mail::send('auth.emailResetPassword', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset Request');
            });

            // Jika berhasil
            return redirect('/login')->with('success', 'The password reset link has been sent. Please check your email to reset your password');
        } catch (\Exception $e) {
            // Jika gagal
            return redirect('/login')->with('error', 'Failed to send the reset link. Please try again later');
        }
    }


    public function showResetForm($token)
    {
        return view('auth.resetPassword', ['token' => $token]);
    }
    public function resetPassword(Request $request)
    {
        $reset = \DB::table('password_resets')->where('token', $request->token)->first();

        if (!$reset) {
             return redirect('/login')->with('error', 'Invalid Token');
        }

        $user = User::where('email', $reset->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token setelah digunakan
        \DB::table('password_resets')->where('email', $reset->email)->delete();

        return redirect('/login')->with('success', 'Your password has been successfully reset. Please log in again');
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
