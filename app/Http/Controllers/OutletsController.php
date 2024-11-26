<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\PermissionUsers;
use App\Models\Permission;
use App\Models\Transactions;
use App\Models\Customers;
use App\Models\Details;
use Auth;

class OutletsController extends Controller
{
     public function index()
	{
		$PermissionOutlets = PermissionUsers::getPermission('Outlets',Auth::user()->id);
		if (empty($PermissionOutlets)) {
			abort(404);
		}
	    $data = [
	        'title' => 'Outlets',
            'sub_title' => 'Reporting',
	        'cabang' => Cabang::all(),
	        'data' => '',
	        'startDate' => date('Y-m-d'),
	        'endDate' => date('Y-m-d'),
            'labels' => json_encode('Item_name'),
            'datasets' => json_encode([
                [
                    'label' => 'Total Items',
                    'data' => '',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y',
                ],
            ])
	    ];
	    
	    return view('pages.outlets', $data);
	}
	public function view($id_cabang, $startDate, $endDate)
    {
        $data = Cabang::find($id_cabang);
        $topchart = Details::getTopChart($id_cabang, $startDate, $endDate);
        $topmenu = Details::getTopOneMenu($startDate, $endDate);
        $details = Details::getDetails($id_cabang, $startDate, $endDate);
        $labels = [];
        $totalItems = [];
        $totalPrice = [];

        foreach ($topchart as $c) {
            $labels[] = $c->description; // Item name
            $totalItems[] = $c->total_items;
            $totalPrice[] = $c->total_items * $c->unit_price; // Assuming you want to calculate total price
        }

        $data = [
            'title' => 'Outlets',
            'sub_title' => 'Reporting',
            'permission' => Permission::getRecord(),
            'userpermission' => PermissionUsers::getUserPermission($id_cabang),
            'cabang' => Cabang::all(),
            'data' => $data,
            'transcount' => Transactions::Count($startDate, $endDate),
            'custcount' => Customers::Count($startDate, $endDate),
            'totalpayment' => Details::SumPayment($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'topmenu' => Details::getTop($id_cabang, $startDate, $endDate),
            'topmenuone' => $topmenu,
            'details' => $details,
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
            ])
        ];

        return view('pages.outlets', $data);
    }
    public function search(Request $request)
    {
        $id_cabang = $request->id_cabang;
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $data = Cabang::find($id_cabang);
        $topchart = Details::getTopChart($id_cabang, $startDate, $endDate);
        $topmenu = Details::getTopOneMenu($startDate, $endDate);
        $details = Details::getDetails($id_cabang, $startDate, $endDate);
        $labels = [];
        $totalItems = [];
        $totalPrice = [];

        foreach ($topchart as $c) {
            $labels[] = $c->description; // Item name
            $totalItems[] = $c->total_items;
            $totalPrice[] = $c->total_items * $c->unit_price; // Assuming you want to calculate total price
        }

        $data = [
            'title' => 'Outlets',
            'sub_title' => 'Reporting',
            'permission' => Permission::getRecord(),
            'userpermission' => PermissionUsers::getUserPermission($id_cabang),
            'cabang' => Cabang::all(),
            'data' => $data,
            'transcount' => Transactions::Count($startDate, $endDate),
            'custcount' => Customers::Count($startDate, $endDate),
            'totalpayment' => Details::SumPayment($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'topmenu' => Details::getTop($id_cabang, $startDate, $endDate),
            'topmenuone' => $topmenu,
            'details' => $details,
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
            ])
        ];

        return view('pages.outlets', $data);
    }


}
