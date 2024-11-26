<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Misalnya kita ingin mengambil data dari tabel users
use App\Models\Cabang;
use App\Models\Transactions;
use App\Models\Customers;
use App\Models\Details;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('dari');
        $endDate = $request->input('sampai');
        if (!$startDate || !$endDate) {
            $startDate = now()->subDays(7)->toDateString();
            $endDate = now()->toDateString();
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
        $custOUTP = Customers::GetCustOutP($startDate, $endDate);
        $custOUTM = Customers::GetCustOutM($startDate, $endDate);
        $transWH = Transactions::GetTransWH($startDate, $endDate);
        $transOUTP = Transactions::GetTransOUTP($startDate, $endDate);
        $transOUTM = Transactions::GetTransOUTM($startDate, $endDate);
        $detailsWH = Details::GetDetailsWH($startDate,$endDate);
        $detailsOUTP = Details::GetDetailsOUTP($startDate,$endDate);
        $detailsOUTM = Details::GetDetailsOUTM($startDate,$endDate);
        $transactionsCount = Transactions::Count($startDate, $endDate);
        $custCount = Customers::Count($startDate, $endDate);
        $totalPayments = Details::SumPayment($startDate, $endDate);
        $topmenu = Details::getTopOneMenu($startDate, $endDate);
        // dd($topmenu);
        $users = User::all();
        $data = [
	        'title' => 'Dashboard',
            'sub_title' => 'Dashboard',
	        'users' => $users,
            'cabang' => Cabang::all(),
            'start' => $startDate,
            'end' => $endDate,
            'dates' => $dates,
            'count' => $transactionsCount,
            'custcount' => $custCount,
            'totalpayment' => $totalPayments,
            'topmenu' => $topmenu,
            'custWH' => $custWH,
            'custOUTP' => $custOUTP,
            'custOUTM' => $custOUTM,
            'transWH' => $transWH,
            'transOUTP' => $transOUTP,
            'transOUTM' => $transOUTM,
            'detailsWH' => $detailsWH,
            'detailsOUTP' => $detailsOUTP,
            'detailsOUTM' => $detailsOUTM,
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
}
