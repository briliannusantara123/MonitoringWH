<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Details extends Model
{
    protected $table = 'sh_t_transaction_details';
    public $timestamps = false;
    protected $fillable = [
        'id_real',
        'trstoreid',
        'itemcodest',
        'id_trans',
        'item_code',
        'qty',
        'unit_price',
        'unit_price_no_sc',
        'description',
        'start_time_order',
        'end_time_order',
        'end_time_runner',
        'entry_by',
        'submit_time',
        'extra_notes',
        'disc',
        'is_cancel',
        'is_paid',
        'is_exclude',
        'order_type',
        'floor',
        'session_item',
        'cabang',
        'selected_table_no',
        'seat_id',
        'delivered_by',
        'delivered_date',
        'checker_by',
        'qty_finish',
        'processed_by',
        'qty_processed',
        'runner_by',
        'waitress_by',
        'sort_id',
        'is_printed',
        'qty_print',
        'is_finish',
        'qty_selected',
        'runner_scan_date',
        'waitress_scan_date',
        'print_code',
        'as_take_away',
    ];


    public static function GetDetailsWH($startDate, $endDate)
    {
    	return DB::table('sh_t_transaction_details as details')
        ->join('sh_t_transactions as transactions', 'details.trstoreid', '=', 'transactions.trstoreid')
        ->whereBetween('transactions.create_date', [$startDate, $endDate])
        ->selectRaw('
            transactions.cabang, 
            DATE(transactions.create_date) as date, 
            SUM(details.unit_price * details.qty) as total_value
        ') // Hitung total hasil perkalian unit_price * qty
        ->groupBy('transactions.cabang', 'date') // Kelompokkan berdasarkan cabang dan tanggal
        ->get()
        ->groupBy('cabang') // Kelompokkan ulang berdasarkan cabang
        ->map(function ($item) {
            return $item->keyBy('date'); // Kelompokkan data berdasarkan tanggal untuk setiap cabang
        });
    }

    public static function SumPayment($startDate, $endDate)
    {
    	return DB::table('sh_t_transaction_details as details')
        ->join('sh_t_transactions as transactions', 'details.trstoreid', '=', 'transactions.trstoreid')
        ->whereBetween(DB::raw('DATE(transactions.create_date)'), [$startDate, $endDate]) // Menggunakan DATE() untuk filter tanggal
        ->select('details.cabang')
        ->selectRaw('SUM(details.qty * details.unit_price) as total_payment') // Total pembayaran
        ->groupBy('details.cabang')
        ->pluck('total_payment', 'details.cabang'); // Mengambil total_payment dengan cabang sebagai key

    }
    public static function getTop($id_cabang, $startDate, $endDate)
    {
        return DB::table('sh_t_transaction_details as details')
        ->join('sh_t_transactions as transactions', 'details.trstoreid', '=', 'transactions.trstoreid')
        ->select(
            'details.item_code',
            'details.description',
            'details.unit_price',
            DB::raw('SUM(details.qty) as total_qty')
        )
        ->where('details.cabang', $id_cabang)
        ->whereBetween(DB::raw('DATE(transactions.create_date)'), [$startDate, $endDate])  // Gunakan DATE() untuk filter hanya Y-m-d
        ->groupBy('details.item_code', 'details.description', 'details.unit_price')
        ->orderBy('total_qty', 'desc')
        ->paginate(10);

    }
    public static function getTopChart($id_cabang, $startDate, $endDate)
    {
        return DB::table('sh_t_transactions AS t')
        ->select(
            'd.description', // Item name
            'd.unit_price', // Unit price for each item
            DB::raw('SUM(d.qty) as total_items') // Total quantity sold
        )
        ->join('sh_t_transaction_details AS d', 'd.trstoreid', '=', 't.trstoreid')
        ->where('d.cabang', $id_cabang)
        ->whereBetween(DB::raw('DATE(t.create_date)'), [$startDate, $endDate]) // Gunakan DATE() untuk filter hanya Y-m-d
        ->groupBy('d.description', 'd.unit_price') // Group by item name and unit price
        ->orderBy('total_items', 'desc')
        ->get();

    }
    public static function getTopOneMenu($startDate, $endDate)
    {
        return DB::table('sh_t_transaction_details as details')
        ->join('sh_t_transactions as transactions', 'details.trstoreid', '=', 'transactions.trstoreid')
        ->whereBetween(DB::raw('DATE(transactions.create_date)'), [$startDate, $endDate]) // Menggunakan DATE() untuk memfilter tanggal
        ->select(
            'details.cabang', // Cabang
            'details.description', // Nama item
            DB::raw('SUM(details.qty) as total_qty'), // Total jumlah item
            'details.unit_price' // Harga satuan
        )
        ->groupBy('details.cabang', 'details.description', 'details.unit_price') // Kelompokkan per cabang dan per item
        ->orderBy('details.cabang') // Pastikan data dikelompokkan berdasarkan cabang
        ->orderBy('total_qty', 'desc') // Urutkan berdasarkan total qty terbesar
        ->get()
        ->groupBy('cabang') // Kelompokkan data berdasarkan cabang
        ->mapWithKeys(function ($items, $cabang) {
            $topItem = $items->first(); // Ambil item pertama (qty terbesar)
            return [$cabang => (array) $topItem]; // Konversi ke array agar bisa diakses dengan []
        })
        ->toArray(); // Konversi hasil akhir menjadi array

    }
   public static function getDetails($id_cabang, $startDate, $endDate)
    {
        // Start building the query
        $query = DB::table('sh_t_transactions as d')
            ->join('sh_t_transaction_details as b', 'd.trstoreid', '=', 'b.trstoreid')
            ->join('sh_m_cabang as c', 'b.cabang', '=', 'c.id')
            ->join('sh_m_customer as cs', 'd.custstid', '=', 'cs.custstid')
            ->select(
                'b.cabang',
                'c.cabang_name',
                'd.create_date',
                'd.order_no',
                'cs.customer_name',
                'b.selected_table_no',
                'cs.total_pax',
                'd.payment_by',
                'd.trstoreid',
                'b.disc',
                'd.bill_discount',
                'd.down_payment',
                'd.payment_type',
                'd.payment_amount',
                'd.kembalian',
                'd.sc_amount',
                'd.tax_amount',
            )
            ->where('b.is_cancel', 0)
            ->where('b.cabang', $id_cabang)
            ->whereBetween(DB::raw('DATE(d.create_date)'), [$startDate, $endDate])
            ->groupBy('cs.customer_name');

        // Paginate the results
        $query = $query->paginate(20); // Specify the number of results per page

        return $query; // This will return a paginator object
    }
    public static function countDetails($trstoreid)
    {
        return DB::table('sh_t_transaction_details')
        ->where('trstoreid', $trstoreid) // Apply the condition
        ->count(); // Count the records matching the condition
    }
    public static function getDetailsReport($trstoreid)
    {
        $query = DB::table('sh_t_transaction_details as d')
            ->where('d.trstoreid', $trstoreid)
            ->select('d.description', DB::raw('SUM(d.qty) as total_qty'), 'd.unit_price','d.disc')
            ->groupBy('d.itemcodest')
            ->havingRaw('SUM(d.qty) > 0');
        return $query->get(); 
    }

    public static function hitungSubTotal($trstoreid)
    {
        // Get the details for the given transaction store ID
        $details = DB::table('sh_t_transaction_details')
                    ->where('trstoreid', $trstoreid)
                    ->get(); // Retrieve the details from the database

        $subTotalTransaction = 0; // Initialize the subtotal transaction

        // Loop through each detail and calculate subtotal (qty * unit_price - disc)
        foreach ($details as $detail) {
            $qty = $detail->qty ?? 0; // Default to 0 if qty is null
            $unit_price = $detail->unit_price ?? 0; // Default to 0 if unit_price is null
            $disc = $detail->disc ?? 0; // Default to 0 if disc is null

            // Calculate the subtotal for each item
            $subTotal = ($qty * $unit_price) - $disc;

            // Add to the overall transaction subtotal
            $subTotalTransaction += $subTotal;
        }
        return $subTotalTransaction; // Return the total calculated subtotal
    }






    // public static function getDetails($id_cabang, $startDate, $endDate)
    // {
    //     $scP = 5;
    //     $taxP = 10;

    //     $query = "
    //         SELECT
    //             d.cabang, 
    //             c.cabang_name,
    //             SUM((d.unit_price * d.qty) - (d.unit_price * d.qty * (d.disc / 100))) AS total,
    //             SUM(((d.unit_price * d.qty) - (d.unit_price * d.qty * (d.disc / 100))) * ($scP / 100)) AS sc,
    //             SUM(
    //                 (
    //                     ((d.unit_price * d.qty) - (d.unit_price * d.qty * (d.disc / 100))) * ($scP / 100)
    //                 ) * ($taxP / 100) 
    //                 + 
    //                 (
    //                     ((d.unit_price * d.qty) - (d.unit_price * d.qty * (d.disc / 100))) * ($taxP / 100)
    //                 )
    //             ) AS ppn
    //         FROM 
    //             sh_t_transaction_details d
    //         INNER JOIN 
    //             sh_t_transactions b ON d.trstoreid = b.trstoreid
    //         INNER JOIN 
    //             sh_m_cabang c ON d.cabang = c.id
    //         WHERE 
    //             d.is_paid = 0 
    //             AND d.is_cancel = 0 
    //             AND DATE(b.create_date) BETWEEN :start_date AND :end_date
    //         GROUP BY 
    //             d.cabang, c.cabang_name
    //     ";

    //     return DB::select($query, [
    //         'start_date' => $startDate,
    //         'end_date' => $endDate
    //     ]);
    // }

}
