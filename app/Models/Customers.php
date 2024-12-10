<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customers extends Model
{
    protected $table = 'sh_m_customer';
    public $timestamps = false;
    protected $fillable = [
        'id_real',
        'custstid',
        'cabang',
        'is_main_customer',
        'id_member',
        'member_name',
        'customer_name',
        'no_telp',
        'email',
        'passcode',
        'create_date',
        'is_waiting',
        'is_checkin',
        'sequence',
        'total_pax',
        'total_real_pax',
        'point',
        'visit_type',
        'visit_outlet',
        'antrian',
        'antrian_prefix',
        'checkin_type',
    ];

    public static function GetCustWH($startDate, $endDate)
	{
	    $results = collect();

	    DB::table('sh_m_customer as cust')
	        ->leftJoin('sh_t_transactions as transactions', 'cust.custstid', '=', 'transactions.custstid')
	        ->whereBetween(DB::raw('DATE(cust.create_date)'), [$startDate, $endDate]) // Ensure only date is compared
	        ->selectRaw('transactions.cabang, DATE(cust.create_date) as date, COUNT(cust.id) as total_count')
	        ->groupBy('transactions.cabang', 'date')
	        ->orderBy('transactions.cabang')
	        ->chunk(100, function ($chunk) use (&$results) {
	            $results = $results->merge($chunk);
	        });

	    return $results->groupBy('cabang')->map(function ($item) {
	        return $item->keyBy('date');
	    });
	}
	private static function getConnectionByCabang($cabang, $waktu)
	{
	    $connections = [
	        'pagi' => [
	            15 => 'puripagi',
	            20 => 'amperapagi',
	            5  => 'alsutpagi',
	            2  => 'bintaropagi',
	            13 => 'sutamipagi',
	            26 => 'jbtowerpagi',
	            19 => 'bekasipagi',
	            14 => 'cilakipagi',
	            21 => 'kuncitpagi',
	            4  => 'lbpagi',
	            11 => 'margondapagi',
	            12 => 'bogorpagi',
	            27 => 'sunterpagi',
	            9  => 'gatsu',
	            7  => 'tb',
	            6  => 'gading',
	        ],
	        'malam' => [
	            15 => 'purimalam',
	            20 => 'amperamalam',
	            5  => 'alsutmalam',
	            2  => 'bintaromalam',
	            13 => 'sutamimalam',
	            26 => 'jbtowermalam',
	            19 => 'bekasimalam',
	            14 => 'cilakimalam',
	            21 => 'kuncitmalam',
	            4  => 'lbmalam',
	            11 => 'margondamalam',
	            12 => 'bogormalam',
	            27 => 'suntermalam',
	        ],
	    ];

	    return isset($connections[$waktu][$cabang]) 
	        ? DB::connection($connections[$waktu][$cabang]) 
	        : null;
	}
	public static function getDataOutlet($cabang, $date, $waktu)
	{
	    $status = DB::table('sh_m_cabang')
	        ->where('id', $cabang)
	        ->select('status_ip_pagi', 'status_ip_malam')
	        ->first();
	    if (!$status) {
	        return collect();
	    }

	    $connection = null;
	    if ($status->status_ip_pagi === 'online' && $status->status_ip_malam === 'online') {
	        $connection = self::getConnectionByCabang($cabang, $waktu);
	    } elseif ($status->status_ip_pagi === 'online' && $status->status_ip_malam === 'offline') {
	        $connection = self::getConnectionByCabang($cabang, 'pagi');
	    } elseif ($status->status_ip_pagi === 'offline' && $status->status_ip_malam === 'online') {
	        $connection = self::getConnectionByCabang($cabang, 'malam');
	    }

	    if (!$connection) {
	        return collect();
	    }

	    $startDate = $date . ' 00:00:00';
	    $endDate = $date . ' 23:59:59';

	    // Query data with date filter
	    return $connection
	        ->table('sh_m_customer as cust')
	        ->leftJoin('sh_t_transactions as transactions', 'cust.id', '=', 'transactions.id_customer')
	        ->whereBetween(DB::raw('DATE(cust.create_date)'), [$startDate, $endDate]) // Ensure only date is compared
	        ->selectRaw('COALESCE(transactions.cabang, ?) as cabang, DATE(cust.create_date) as date, COUNT(cust.id) as total_count', [$cabang])
	        ->groupBy('cabang', 'date')
	        ->get()
	        ->groupBy('cabang')
	        ->map(function ($item) {
	            return $item->keyBy('date');
	        });
	}
	public static function Count($startDate, $endDate)
	{
		return Customers::whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate])  // Filter by date part only
        ->select('cabang')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('cabang')
        ->pluck('count', 'cabang');
	}

}
