<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;
use App\Models\PermissionUsers;
use Auth;

class UsersController extends Controller
{
    public function users()
    {
        $PermissionUsers = PermissionUsers::getPermission('Users',Auth::user()->id);
        if (empty($PermissionUsers)) {
            abort(404);
        }
        $users = User::all();
        $data = [
            'title' => 'Users',
            'sub_title' => 'Master Data',
            'users' => $users,
            'PermissionAdd' => PermissionUsers::getPermission('Add Users',Auth::user()->id),
            'PermissionEdit' => PermissionUsers::getPermission('Edit Users',Auth::user()->id),
            'PermissionDelete' => PermissionUsers::getPermission('Delete Users',Auth::user()->id),
        ];
        return view('pages.user.users',$data);
    }
    public function adduser()
    {
        $data = [
            'title' => 'Add New User',
            'sub_title' => 'Master Data',
            'permission' => Permission::getRecord(),
        ];
        return view('pages.user.adduser',$data);
    }
    public function create_user(Request $request)
    {
        $save = new User;
        $save->name = $request->name;
        $save->email = $request->email;
        $save->password = Hash::make($request->password);
        $save->save();

        PermissionUsers::InsertUpdateRecord($request->permission_id,$save->id);
        return redirect()->intended('/users')->with('success', 'Successfully added a new user');

    }
    public function edit($id)
    {
        $user = User::find($id);
        $data = [
            'title' => 'Edit User',
            'sub_title' => 'Master Data',
            'permission' => Permission::getRecord(),
            'userpermission' => PermissionUsers::getUserPermission($id),
            'user' => $user,
        ];
        return view('pages.user.edituser',$data);
    }
    public function update_users(Request $request, $id)
    {
        $save = User::getSingle($id);
        $save->name = $request->name;
        $save->email = $request->email;
        if ($request->filled('password')) {
            $save->password = Hash::make($request->password);
        }
        $save->save();
        PermissionUsers::InsertUpdateRecord($request->permission_id, $save->id);
        return redirect()->route('users.index')->with('success', 'User successfully updated');

    }
    public function hapus_users($id)
    {
        $user = User::getSingle($id); // Cari user berdasarkan ID
        $user->delete(); // Hapus user

        return redirect()->route('users.index')->with('success', 'User successfully deleted');
    }
}
