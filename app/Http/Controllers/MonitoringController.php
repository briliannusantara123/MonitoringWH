<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonitoringController extends Controller
{
     public function index()
	{
	    $data = [
	        'title' => 'Monitoring',
	    ];
	    
	    return view('pages.monitoring', $data);
	}

}
