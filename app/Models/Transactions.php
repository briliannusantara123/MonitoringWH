<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transactions extends Model
{
    protected $table = 'sh_t_transactions';

    /**
     * Get cabang with total items and total price for the given date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Support\Collection
     */
    public static function getCabangWithTotalItems($startDate, $endDate)
    {
        return DB::table('sh_t_transactions AS t')
            ->select(
                'c.cabang_name',  // Get the cabang name
                DB::raw('SUM(d.qty) as total_items'),  // Sum the quantity
                DB::raw('SUM(d.unit_price * d.qty) as total_price')  // Sum the total price (qty * unit_price)
            )
            ->join('sh_t_transaction_details AS d', 'd.trstoreid', '=', 't.trstoreid')  // Join transaction details
            ->join('sh_m_cabang AS c', 't.cabang', '=', 'c.id')  // Join cabang table
            ->whereBetween('t.create_date', [$startDate, $endDate])  // Filter by date range
            ->groupBy('c.id', 'c.cabang_name')  // Group by cabang ID and name
            ->get();  // Execute the query
    }
    public static function GetTransWH($startDate, $endDate)
    {
        return DB::table('sh_t_transactions')
        ->whereBetween('create_date', [$startDate, $endDate])
        ->selectRaw('cabang, DATE(create_date) as date, COUNT(*) as total_count')
        ->groupBy('cabang', 'date')
        ->get()
        ->groupBy('cabang')
        ->map(function ($item) {
            return $item->keyBy('date');
        });
    }
    public static function GetTransOUTP($startDate, $endDate){
        return DB::connection('puripagi') // Gunakan koneksi puripagi
        ->table('sh_t_transactions')
        ->whereBetween('create_date', [$startDate, $endDate]) // Filter berdasarkan rentang tanggal
        ->selectRaw('cabang, DATE(create_date) as date, COUNT(id) as total_count') // Pilih kolom
        ->groupBy('cabang', 'date') // Kelompokkan berdasarkan cabang dan tanggal
        ->get()
        ->groupBy('cabang') // Kelompokkan kembali berdasarkan cabang menggunakan Laravel Collection
        ->map(function ($item) {
            return $item->keyBy('date'); // Buat key untuk setiap tanggal di dalam cabang
        });
    }
    public static function GetTransOUTM($startDate, $endDate){
        return DB::connection('purimalam') // Gunakan koneksi puripagi
        ->table('sh_t_transactions')
        ->whereBetween('create_date', [$startDate, $endDate]) // Filter berdasarkan rentang tanggal
        ->selectRaw('cabang, DATE(create_date) as date, COUNT(id) as total_count') // Pilih kolom
        ->groupBy('cabang', 'date') // Kelompokkan berdasarkan cabang dan tanggal
        ->get()
        ->groupBy('cabang') // Kelompokkan kembali berdasarkan cabang menggunakan Laravel Collection
        ->map(function ($item) {
            return $item->keyBy('date'); // Buat key untuk setiap tanggal di dalam cabang
        });
    }
    public static function Count($startDate, $endDate)
    {
        return Transactions::whereBetween('create_date', [$startDate, $endDate])
        ->select('cabang')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('cabang')
        ->pluck('count', 'cabang');
    }
}
