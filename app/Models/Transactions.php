<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transactions extends Model
{
    protected $table = 'sh_t_transactions';
    public $timestamps = false;
    protected $fillable = [
        'id_real',
        'trstoreid',
        'custstid',
        'stbookid',
        'parent_id',
        'id_rel_reservasi',
        'id_customer',
        'create_by',
        'create_date',
        'is_canceled',
        'is_closed',
        'payment_number',
        'payment_type',
        'payment_card_type',
        'payment_bank_card',
        'payment_amount',
        'rounding_amount',
        'payment_date',
        'is_bill_printed',
        'bill_printed_count',
        'is_payment_printed',
        'total_amount',
        'down_payment',
        'redeem_point_amount',
        'voucher_no',
        'voucher_amount',
        'kembalian',
        'payment_by',
        'order_no',
        'is_member',
        'transaction_point',
        'date_order_menu',
        'is_order_menu_active',
        'start_time_order',
        'end_time_order',
        'entry_by',
        'catatan',
        'cabang',
        'id_booking',
        'booking_name',
        'is_take_away',
        'dp_used',
        'biaya_kirim',
        'trans_types',
        'checker_printed',
        'daily_trans_no',
        'sc_percent',
        'tax_percent',
        'tax_amount',
        'bill_discount',
        'bill_discount_percent',
    ];


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
            ->whereBetween(DB::raw('DATE(t.create_date)'), [$startDate, $endDate])  // Filter by date part only
            ->groupBy('c.id', 'c.cabang_name')  // Group by cabang ID and name
            ->get();  // Execute the query
    }

    public static function GetTransWH($startDate, $endDate)
    {
        return DB::table('sh_t_transactions')
            ->whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate])  // Filter by date part only
            ->selectRaw('cabang, DATE(create_date) as date, COUNT(*) as total_count')
            ->groupBy('cabang', 'date')
            ->get()
            ->groupBy('cabang')
            ->map(function ($item) {
                return $item->keyBy('date'); // Map each item by its date
            });
    }


    // public static function getDataOutletPagi($cabang, $date)
    // {
    //     // Tentukan koneksi berdasarkan $cabang
    //     if ($cabang == 15) {
    //         $connection = DB::connection('puripagi');
    //     } elseif ($cabang == 20) {
    //         $connection = DB::connection('amperapagi');
    //     } elseif ($cabang == 5) {
    //         $connection = DB::connection('alsutpagi');
    //     } elseif ($cabang == 2) {
    //         $connection = DB::connection('bintaropagi');
    //     } elseif ($cabang == 13) {
    //         $connection = DB::connection('sutamipagi');
    //     } elseif ($cabang == 26) {
    //         $connection = DB::connection('jbtowerpagi');
    //     } elseif ($cabang == 26) {
    //         $connection = DB::connection('bekasipagi');
    //     } elseif ($cabang == 26) {
    //         $connection = DB::connection('cilakipagi');
    //     } elseif ($cabang == 21) {
    //         $connection = DB::connection('kuncitpagi');
    //     } elseif ($cabang == 4) {
    //         $connection = DB::connection('lbpagi');
    //     } elseif ($cabang == 11) {
    //         $connection = DB::connection('margondapagi');
    //     } elseif ($cabang == 12) {
    //         $connection = DB::connection('bogorpagi');
    //     } elseif ($cabang == 9) {
    //         $connection = DB::connection('gatsu');
    //     } elseif ($cabang == 7) {
    //         $connection = DB::connection('tb');
    //     } elseif ($cabang == 6) {
    //         $connection = DB::connection('gading');
    //     } else {
    //         return collect(); // Return collection kosong jika $cabang tidak valid
    //     }

    //     // Pastikan tanggal start dan end berasal dari $date
    //     $startDate = $date . ' 00:00:00';
    //     $endDate = $date . ' 23:59:59';

    //     // Query data berdasarkan koneksi
    //     return $connection // Gunakan koneksi puripagi
    //     ->table('sh_t_transactions')
    //     ->whereBetween('create_date', [$startDate, $endDate]) // Filter berdasarkan rentang tanggal
    //     ->selectRaw('cabang, DATE(create_date) as date, COUNT(id) as total_count') // Pilih kolom
    //     ->groupBy('cabang', 'date') // Kelompokkan berdasarkan cabang dan tanggal
    //     ->get()
    //     ->groupBy('cabang') // Kelompokkan kembali berdasarkan cabang menggunakan Laravel Collection
    //     ->map(function ($item) {
    //         return $item->keyBy('date'); // Buat key untuk setiap tanggal di dalam cabang
    //     });
    // }

    // public static function getDataOutletMalam($cabang, $date)
    // {
    //     // Tentukan koneksi berdasarkan $cabang
    //     if ($cabang == 15) {
    //         $connection = DB::connection('purimalam');
    //     } elseif ($cabang == 20) {
    //         $connection = DB::connection('amperamalam');
    //     } elseif ($cabang == 5) {
    //         $connection = DB::connection('alsutmalam');
    //     } elseif ($cabang == 2) {
    //         $connection = DB::connection('bintaromalam');
    //     } elseif ($cabang == 13) {
    //         $connection = DB::connection('sutamimalam');
    //     } elseif ($cabang == 26) {
    //         $connection = DB::connection('jbtowermalam');
    //     } elseif ($cabang == 21) {
    //         $connection = DB::connection('kuncitmalam');
    //     } elseif ($cabang == 19) {
    //         $connection = DB::connection('bekasimalam');
    //     } elseif ($cabang == 14) {
    //         $connection = DB::connection('cilakimalam');
    //     } elseif ($cabang == 4) {
    //         $connection = DB::connection('lbmalam');
    //     } elseif ($cabang == 11) {
    //         $connection = DB::connection('margondamalam');
    //     } elseif ($cabang == 12) {
    //         $connection = DB::connection('bogormalam');
    //     } else {
    //         return collect(); // Return collection kosong jika $cabang tidak valid
    //     }

    //     // Pastikan tanggal start dan end berasal dari $date
    //     $startDate = $date . ' 00:00:00';
    //     $endDate = $date . ' 23:59:59';

    //     // Query data berdasarkan koneksi
    //     return $connection // Gunakan koneksi puripagi
    //     ->table('sh_t_transactions')
    //     ->whereBetween('create_date', [$startDate, $endDate]) // Filter berdasarkan rentang tanggal
    //     ->selectRaw('cabang, DATE(create_date) as date, COUNT(id) as total_count') // Pilih kolom
    //     ->groupBy('cabang', 'date') // Kelompokkan berdasarkan cabang dan tanggal
    //     ->get()
    //     ->groupBy('cabang') // Kelompokkan kembali berdasarkan cabang menggunakan Laravel Collection
    //     ->map(function ($item) {
    //         return $item->keyBy('date'); // Buat key untuk setiap tanggal di dalam cabang
    //     });
    // }

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
                27 => 'sunterpagi',
                4  => 'lbpagi',
                11 => 'margondapagi',
                12 => 'bogorpagi',
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
                27 => 'suntermalam',
                4  => 'lbmalam',
                11 => 'margondamalam',
                12 => 'bogormalam',
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

    return $connection
        ->table('sh_t_transactions')
        ->whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate])  // Filter by date part only
        ->selectRaw('cabang, DATE(create_date) as date, COUNT(id) as total_count')
        ->groupBy('cabang', 'date')
        ->get()
        ->groupBy('cabang')
        ->map(function ($item) {
            return $item->keyBy('date'); // Map each item by its date
        });
}


    public static function Count($startDate, $endDate)
{
    return Transactions::whereBetween(DB::raw('DATE(create_date)'), [$startDate, $endDate])  // Filter by date part only
        ->select('cabang')
        ->selectRaw('COUNT(*) as count')
        ->groupBy('cabang')
        ->pluck('count', 'cabang');
}

}
