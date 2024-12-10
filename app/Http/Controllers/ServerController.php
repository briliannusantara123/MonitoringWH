<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServerController extends Controller
{
    public function index()
    {
        $cabang = Cabang::all();
        $port = 5082; 
        foreach ($cabang as $c) {
            $ip_pagi = $c->ip_pagi;
            $ip_malam = $c->ip_malam;

            // Check connection for ip_pagi
            if ($this->checkConnection($ip_pagi, $port)) {
                $this->updateStatus($c->id, 'online', 'ip_pagi');
            } else {
                $this->updateStatus($c->id, 'offline', 'ip_pagi');
            }

            if (empty($ip_malam)) {
                $this->updateStatus($c->id, 'offline', 'ip_malam');
            }else{
                if ($this->checkConnection($ip_malam, $port)) {
                    $this->updateStatus($c->id, 'online', 'ip_malam');
                } else {
                    $this->updateStatus($c->id, 'offline', 'ip_malam');
                }
            }
        }
        return back()->with('success', 'Server Successfully Refreshed');
    }

    public function auto()
    {
        $cabang = Cabang::all();
        $port = 5082; 
        foreach ($cabang as $c) {
            $ip_pagi = $c->ip_pagi;
            $ip_malam = $c->ip_malam;

            // Check connection for ip_pagi
            if ($this->checkConnection($ip_pagi, $port)) {
                $this->updateStatus($c->id, 'online', 'ip_pagi');
            } else {
                $this->updateStatus($c->id, 'offline', 'ip_pagi');
            }

            if (empty($ip_malam)) {
                $this->updateStatus($c->id, 'offline', 'ip_malam');
            }else{
                if ($this->checkConnection($ip_malam, $port)) {
                    $this->updateStatus($c->id, 'online', 'ip_malam');
                } else {
                    $this->updateStatus($c->id, 'offline', 'ip_malam');
                }
            }
        }
        return redirect('/closetab');
    }

    private function checkConnection($ip, $port)
    {
        $connection = @fsockopen($ip, $port, $errno, $errstr, 5);
        if ($connection) {
            fclose($connection);
            return true;
        } else {
            return false;
        }
    }

    private function updateStatus($id, $status, $type)
    {
        $data = [];
        if ($type === 'ip_pagi') {
            $data['status_ip_pagi'] = $status;
        } else if ($type === 'ip_malam') {
            $data['status_ip_malam'] = $status;
        }
        Cabang::where('id', $id)->update($data);
    }
}
