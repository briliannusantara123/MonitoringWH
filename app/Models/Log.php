<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    protected $table = 'log_autoinsert';
    public $timestamps = false;

    public static function GetLog($startDate, $endDate, $cb, $status)
    {
        $query = DB::table('log_autoinsert as log')
            ->leftJoin('sh_m_cabang as cabang', 'log.cabang', '=', 'cabang.id')
            ->whereBetween(DB::raw('DATE(log.tgl_insert)'), [$startDate, $endDate])
            ->orderBy('log.tgl_insert')
            ->select('log.*', 'cabang.cabang_name');

        if ($cb !== 'all') {
            $query->where('cabang.id', $cb);
        }
        if ($status !== 'all') {
            $query->where('log.status', $status);
        }

        return $query->paginate(20); // Batasi hasil dengan pagination
    }
    public static function CekLog($startDate, $endDate)
    {
        $query = DB::table('log_autoinsert as log')
            ->whereBetween(DB::raw('DATE(log.tgl_insert)'), [$startDate, $endDate])
            ->where('log.status','failed')
            ->orderBy('log.tgl_insert')
            ->select('log.*');

        return $query->get();
    }

}
