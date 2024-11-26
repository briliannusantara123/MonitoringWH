<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        $id_user = auth()->id();
        $users = User::find($id_user);
        $data = [
            'title' => 'Settings',
            'sub_title' => 'Settings',
            'users' => $users,
        ];
        return view('pages.settings',$data);
    }
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        if ($request->new_password !== $request->retype_password) {
            return back()->with('error', 'Re-type password does not match the new password.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password has been updated successfully.');
    }

    public function changeEmail(Request $request)
    {
        $user = Auth::user();

        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Email has been updated successfully.');
    }
}
