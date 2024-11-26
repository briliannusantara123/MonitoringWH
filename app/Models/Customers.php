<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customers extends Model
{
    protected $table = 'sh_m_customer';

    public static function GetCustWH($startDate, $endDate)
	{
	    return DB::table('sh_m_customer as cust')
	        ->leftJoin('sh_t_transactions as transactions', 'cust.custstid', '=', 'transactions.custstid')
	        ->whereBetween('cust.create_date', [$startDate, $endDate])
	        ->selectRaw('transactions.cabang, DATE(cust.create_date) as date, COUNT(cust.id) as total_count')
	        ->groupBy('transactions.cabang', 'date')
	        ->get()
	        ->groupBy('cabang')
	        ->map(function ($item) {
	            return $item->keyBy('date');
	        });
	}
	public static function GetCustOutP($startDate, $endDate)
	{
	    return DB::connection('puripagi') // Gunakan koneksi puripagi
            ->table('sh_m_customer as cust')
            ->leftJoin('sh_t_transactions as transactions', 'cust.id', '=', 'transactions.id_customer')
            ->whereBetween('cust.create_date', [$startDate, $endDate])
            ->selectRaw('transactions.cabang, DATE(cust.create_date) as date, COUNT(cust.id) as total_count')
            ->groupBy('transactions.cabang', 'date')
            ->get()
            ->groupBy('cabang')
            ->map(function ($item) {
                return $item->keyBy('date');
            });
	}
	public static function GetCustOutM($startDate, $endDate)
	{
	    return DB::connection('purimalam') // Gunakan koneksi purimalam
            ->table('sh_m_customer as cust')
            ->leftJoin('sh_t_transactions as transactions', 'cust.id', '=', 'transactions.id_customer')
            ->whereBetween('cust.create_date', [$startDate, $endDate])
            ->selectRaw('transactions.cabang, DATE(cust.create_date) as date, COUNT(cust.id) as total_count')
            ->groupBy('transactions.cabang', 'date')
            ->get()
            ->groupBy('cabang')
            ->map(function ($item) {
                return $item->keyBy('date');
            });
	}
	public static function Count($startDate, $endDate)
	{
		return Customers::whereBetween('create_date', [$startDate, $endDate])
        ->select('cabang')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('cabang')
        ->pluck('count', 'cabang');
	}

}
