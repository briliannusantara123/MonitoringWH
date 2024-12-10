<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Misalnya kita ingin mengambil data dari tabel users
use App\Models\Cabang;
use App\Models\Transactions;
use App\Models\Customers;
use App\Models\Details;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('dari');
        $endDate = $request->input('sampai');
        if (!$startDate || !$endDate) {
            // $startDate = now()->subDays(7)->toDateString();
            // $endDate = now()->toDateString();
            $startDate = now()->subDays(1)->toDateString();
            $endDate = now()->subDays(1)->toDateString();
        }

        $dates = collect();
        $currentDate = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        while ($currentDate->lte($end)) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }
        $cabang = Cabang::all();
        $dataCabang = Transactions::getCabangWithTotalItems($startDate, $endDate);
        $labels = [];
        $totalItems = [];
        $totalPrice = [];

        foreach ($dataCabang as $c) {
            $labels[] = $c->cabang_name;
            $totalItems[] = $c->total_items;
            $totalPrice[] = $c->total_price;
        }

        $custWH = Customers::GetCustWH($startDate, $endDate);
        $transWH = Transactions::GetTransWH($startDate, $endDate);
        $detailsWH = Details::GetDetailsWH($startDate,$endDate);
        $transactionsCount = Transactions::Count($startDate, $endDate);
        $custCount = Customers::Count($startDate, $endDate);
        $totalPayments = Details::SumPayment($startDate, $endDate);
        $topmenu = Details::getTopOneMenu($startDate, $endDate);
        $users = User::all();

        $data = [
	        'title' => 'Dashboard',
            'sub_title' => 'Dashboard',
	        'users' => $users,
            'cabang' => Cabang::orderBy('cabang_name', 'asc')->get(),
            'start' => $startDate,
            'end' => $endDate,
            'dates' => $dates,
            'count' => $transactionsCount,
            'custcount' => $custCount,
            'totalpayment' => $totalPayments,
            'topmenu' => $topmenu,
            'custWH' => $custWH,
            'transWH' => $transWH,
            'detailsWH' => $detailsWH,
            'labels' => json_encode($labels),
            'datasets' => json_encode([
                [
                    'label' => 'Total Items',
                    'data' => $totalItems,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Price',
                    'data' => $totalPrice,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y1',
                ],
            ])
	    ];
        // Kirim data ke view 'dashboard'
        return view('dashboard', $data);
    }

    public function getOutletData()
{
    // Get the required dates (this will depend on your actual data model)
    $dates = ['2024-11-01', '2024-11-02', '2024-11-03', '2024-11-04', '2024-11-05'];  // Example dates, you may get them dynamically

    $outlets = DB::table('sh_m_cabang')->get();

    $result = [];
    foreach ($outlets as $outlet) {
        $outletData = [
            'cabang_name' => $outlet->cabang_name,
            'warehouse' => [],
            'outlet' => []
        ];

        foreach ($dates as $date) {
            // Getting data for warehouse (example)
            $custWarehouse = Customers::getDataOutlet($outlet->id, $date, 'pagi');
            $transWarehouse = Transactions::getDataOutlet($outlet->id, $date, 'pagi');

            // Getting data for outlet (example)
            $custpagi = \App\Models\Customers::getDataOutlet($outlet->id, $date, 'pagi')->pluck('total_count')->first() ?? 0;
            $transpagi = \App\Models\Transactions::getDataOutlet($outlet->id, $date, 'pagi')->pluck('total_count')->first() ?? 0;

            $custmalam = \App\Models\Customers::getDataOutlet($outlet->id, $date, 'malam')->pluck('total_count')->first() ?? 0;
            $transmalam = \App\Models\Transactions::getDataOutlet($outlet->id, $date, 'malam')->pluck('total_count')->first() ?? 0;

            $custOutlet = $custpagi + $custmalam;
            $transOutlet = $transpagi + $transmalam;

            // Storing the values in the result array
            $outletData['warehouse'][$date] = [
                'cust' => $custWarehouse[$outlet->id][$date]->total_count ?? 0,
                'trans' => $transWarehouse[$outlet->id][$date]->total_count ?? 0
            ];

            $outletData['outlet'][$date] = [
                'cust' => $custOutlet[$outlet->id][$date]->total_count ?? 0,
                'trans' => $transOutlet[$outlet->id][$date]->total_count ?? 0
            ];
        }

        $result['cabang'][] = $outletData;
    }

    $result['dates'] = $dates;  // Include the dates for rendering in JS
    return response()->json($result);
}
}
