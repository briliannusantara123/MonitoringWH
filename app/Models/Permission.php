<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permission';

    static public function getSingle($id)
    {
    	return Permission::find($id);
    }

    static public function getRecord()
    {
    	$permission = Permission::where('auto',0)->groupBy('groupby')->get();
    	$result = array();
    	foreach ($permission as $value) {
    		$premissinGroup = Permission::permissionGroup($value->groupby);
    		$data = array();
    		$data['id'] = $value->id;
    		$data['name'] = $value->name;
    		$group = array();
    		foreach ($premissinGroup as $valueG) {
    			$dataG = array();
    			$dataG['id'] = $valueG->id;
    			$dataG['name'] = $valueG->name;
    			$group[] = $dataG;
    		}
    		$data['group'] = $group;
    		$result[] = $data;
    	}
    	return $result;
    }
    static public function permissionGroup($groupby)
    {
    	return Permission::where('groupby', '=', $groupby)->get();
    }
}
