<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;

class MonitoringController extends Controller
{
     public function index()
	{
	    $data = [
	        'title' => 'Monitoring',
	        'cabang' => Cabang::all(),
	    ];
	    
	    return view('pages.monitoring', $data);
	}

}
