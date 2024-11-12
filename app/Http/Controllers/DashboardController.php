<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Misalnya kita ingin mengambil data dari tabel users

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil semua data dari tabel users
        $users = User::all();
        
        // Kirim data ke view 'dashboard'
        return view('dashboard', ['users' => $users]);
    }
}
