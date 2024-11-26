<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionUsers extends Model
{
    protected $table = 'permission_users';
    protected $fillable = [
        'user_id',
        'permission_id',
    ];

    static public function InsertUpdateRecord($permission_ids,$user_id)
    {
    	PermissionUsers::where('user_id','=',$user_id)->delete();
    	foreach ($permission_ids as $pid) {
    		$save = new PermissionUsers;
    		$save->permission_id = $pid;
    		$save->user_id = $user_id;
    		$save->save();
    	}
    }
    static public function getUserPermission($user_id)
    {
    	return PermissionUsers::where('user_id','=',$user_id)->get();
    }
    static public function getPermission($slug,$user_id)
    {
    	return PermissionUsers::select('permission_users.id')
    			->join('permission', 'permission.id', '=', 'permission_users.permission_id')
    			->where('permission_users.user_id','=',$user_id)
    			->where('permission.slug','=',$slug)
    			->count();
    }
}
