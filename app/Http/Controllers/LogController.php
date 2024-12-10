<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\TembakData;
use App\Models\Details;
use App\Models\Customers;
use App\Models\Transactions;
use App\Models\LogInsert;
use App\Models\Cabang;
use App\Models\Permission;
use App\Models\PermissionUsers;
use Carbon\Carbon;
use Auth;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $PermissionUsers = PermissionUsers::getPermission('Log',Auth::user()->id);
        if (empty($PermissionUsers)) {
            abort(404);
        }
        $start = $request->startDate;
        $end = $request->endDate;
        if ($start) {
        	$startDate = $start;
			$endDate = $end;
			$cb = $request->cabang;
			$status = $request->status;
        }else{
        	$startDate = Date('Y-m-d');
			$endDate = Date('Y-m-d');
			$cb = "all";
			$status = "all";
        }
		$log = Log::GetLog($startDate, $endDate, $cb, $status);
        $data = [
            'title' => 'Log',
            'sub_title' => 'Log',
            'cabang' => Cabang::all(),
            'cb' => $cb,
	        'startDate' => $startDate,
	        'endDate' => $endDate,
            'log' => $log,
        ];
        return view('pages.log',$data);
    }
    public function updateserver(Request $request)
	{
	    $start = $request->startDate;
	    $end = $request->endDate;
	    $cabanglog = Log::CekLog($start, $end);
	    foreach ($cabanglog as $cl) {
	    	if ($cl->type == 'insert details') {
	    		$this->Tembakdetails($cl->cabang,$cl->tgl_insert); 
	    	}elseif($cl->type == 'insert transactions'){
	    		$this->Tembaktransactions($cl->cabang,$cl->tgl_insert); 
	    	}elseif($cl->type == 'insert customer'){
	    		$this->Tembakcustomer($cl->cabang,$cl->tgl_insert); 
	    	}
	    }
	    
	    return redirect('log')->with('success', 'Data from the outlet has been successfully updated');
	}
	public function updateserverD(Request $request)
	{
	    $start = $request->startDate;
	    $end = $request->endDate;
	    $cabanglog = Log::CekLog($start, $end);
	    foreach ($cabanglog as $cl) {
	    	if ($cl->type == 'insert details') {
	    		$this->Tembakdetails($cl->cabang,$cl->tgl_insert); 
	    	}elseif($cl->type == 'insert transactions'){
	    		$this->Tembaktransactions($cl->cabang,$cl->tgl_insert); 
	    	}elseif($cl->type == 'insert customer'){
	    		$this->Tembakcustomer($cl->cabang,$cl->tgl_insert); 
	    	}
	    }
	    
	    return redirect('dashboard')->with('success', 'Data from the outlet has been successfully updated');
	}
	public function Tembakcustomer($id_cabang,$tgl)
    {
        $awal = Carbon::parse($tgl)->format('Y-m-d');
	    $akhir = Carbon::parse($tgl)->format('Y-m-d');

        $cabang = Cabang::find($id_cabang);
        if (!$cabang) {
            LogInsert::create([
                'type' => 'insert customer',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Cabang tidak ditemukan',
                'tgl_insert' => now()
            ]);
            return response()->json(['message' => 'Cabang tidak ditemukan'], 404);
        }

        $details = collect();

        try {
            $status_ip_pagi = $cabang->status_ip_pagi;
            $status_ip_malam = $cabang->status_ip_malam;

            if ($status_ip_pagi === 'online' && $status_ip_malam === 'online') {
                $cust_pagi = collect(TembakData::getCustomers($awal, $akhir, $cabang->id, 'pagi'));
                $cust_malam = collect(TembakData::getCustomers($awal, $akhir, $cabang->id, 'malam'));
                $details = $cust_pagi->concat($cust_malam);
            } elseif ($status_ip_pagi === 'offline' && $status_ip_malam === 'online') {
                $details = collect(TembakData::getCustomers($awal, $akhir, $cabang->id, 'malam'));
            } elseif ($status_ip_pagi === 'online' && $status_ip_malam === 'offline') {
                $details = collect(TembakData::getCustomers($awal, $akhir, $cabang->id, 'pagi'));
            }
        } catch (\Exception $e) {
            LogInsert::create([
                'type' => 'insert customer',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
                'tgl_insert' => now()
            ]);
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil data', 'error' => $e->getMessage()], 500);
        }

        if ($details->isNotEmpty()) {
            $insert_data = $details->map(function ($data) use ($cabang) {
                $bulan = Carbon::parse($data->create_date)->format('m');
                $tahun = Carbon::parse($data->create_date)->format('y');
                $custstid = "HG" . $cabang->id . $tahun . $bulan . $data->id;

                return [
                    'id_real' => $data->id,
                    'custstid' => $custstid,
                    'cabang' => $cabang->id,
                    'is_main_customer' => $data->is_main_customer,
                    'id_member' => $data->id_member,
                    'member_name' => $data->member_name,
                    'customer_name' => $data->customer_name,
                    'no_telp' => $data->no_telp,
                    'email' => $data->email,
                    'passcode' => $data->passcode,
                    'create_date' => $data->create_date,
                    'is_waiting' => $data->is_waiting,
                    'is_checkin' => $data->is_checkin,
                    'sequence' => $data->sequence,
                    'total_pax' => $data->total_pax,
                    'total_real_pax' => $data->total_real_pax,
                    'point' => $data->point,
                    'visit_type' => $data->visit_type,
                    'visit_outlet' => $data->visit_outlet,
                    'antrian' => $data->antrian,
                    'antrian_prefix' => $data->antrian_prefix,
                    'checkin_type' => $data->checkin_type,
                ];
            });


            try {
			    $dataInserted = false; // Flag untuk memeriksa apakah ada data yang berhasil diinsert
			    $dataAlreadyExist = false; // Flag untuk memeriksa apakah ada data yang sudah ada
			    
			    // Proses insert data
			    $insert_data->each(function ($data) use ($id_cabang, &$dataInserted, &$dataAlreadyExist) {
			        // Periksa apakah data sudah ada
			        $exists = Customers::where('create_date', $data['create_date'])
			            ->where('cabang', $data['cabang'])
			            ->exists();

			        if (!$exists) {
			            // Jika data belum ada, insert data baru
			            Customers::create($data);
			            $dataInserted = true; // Tandai bahwa data berhasil diinsert
			        } else {
			            $dataAlreadyExist = true; // Tandai bahwa ada data yang sudah ada
			        }
			    });
			    $logInserted = false; 
				if ($dataInserted && !$logInserted) {
				    // Jika ada data yang berhasil diinsert
				    LogInsert::create([
				        'type' => 'insert customer',
				        'cabang' => $id_cabang,
				        'status' => 'success',
				        'deskripsi' => 'Data berhasil diinsert',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				} elseif ($dataAlreadyExist && !$logInserted) {
				    // Jika ada data yang sudah ada
				    LogInsert::create([
				        'type' => 'insert customer',
				        'cabang' => $id_cabang,
				        'status' => 'failed',
				        'deskripsi' => 'Data sudah ada',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				} elseif (!$dataInserted && !$dataAlreadyExist) {
				    // Jika tidak ada data yang diproses (misalnya tidak ada data baru untuk diinsert)
				    LogInsert::create([
				        'type' => 'insert customer',
				        'cabang' => $id_cabang,
				        'status' => 'failed',
				        'deskripsi' => 'Tidak ada data untuk diinsert',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				}


			} catch (\Exception $e) {
			    // Jika terjadi error, catat log kesalahan
			    LogInsert::create([
			        'type' => 'insert customer',
			        'cabang' => $id_cabang,
			        'status' => 'failed',
			        'deskripsi' => 'Gagal menginsert data: ' . $e->getMessage(),
			        'tgl_insert' => now()
			    ]);
			}


        } else {
            LogInsert::create([
                'type' => 'insert customer',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Tidak ada data untuk diinsert',
                'tgl_insert' => now()
            ]);
        }

        return redirect('/closetab');
    }
    public function Tembaktransactions($id_cabang,$tgl)
    {
        $awal = Carbon::parse($tgl)->format('Y-m-d');
	    $akhir = Carbon::parse($tgl)->format('Y-m-d');

        $cabang = Cabang::find($id_cabang);
        if (!$cabang) {
            LogInsert::create([
                'type' => 'insert transactions',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Cabang tidak ditemukan',
                'tgl_insert' => now()
            ]);
            return response()->json(['message' => 'Cabang tidak ditemukan'], 404);
        }

        $details = collect();

        try {
            $status_ip_pagi = $cabang->status_ip_pagi;
            $status_ip_malam = $cabang->status_ip_malam;

            if ($status_ip_pagi === 'online' && $status_ip_malam === 'online') {
                $cust_pagi = collect(TembakData::getTransactions($awal, $akhir, $cabang->id, 'pagi'));
                $cust_malam = collect(TembakData::getTransactions($awal, $akhir, $cabang->id, 'malam'));
                $details = $cust_pagi->concat($cust_malam);
            } elseif ($status_ip_pagi === 'offline' && $status_ip_malam === 'online') {
                $details = collect(TembakData::getTransactions($awal, $akhir, $cabang->id, 'malam'));
            } elseif ($status_ip_pagi === 'online' && $status_ip_malam === 'offline') {
                $details = collect(TembakData::getTransactions($awal, $akhir, $cabang->id, 'pagi'));
            }
        } catch (\Exception $e) {
            LogInsert::create([
                'type' => 'insert transactions',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
                'tgl_insert' => now()
            ]);
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil data', 'error' => $e->getMessage()], 500);
        }

        if ($details->isNotEmpty()) {
            $insert_data = $details->map(function ($data) use ($cabang) {
                $bulan = Carbon::parse($data->create_date)->format('m');
                $tahun = Carbon::parse($data->create_date)->format('y');
                $custstid = "HG" . $cabang->id . $tahun . $bulan . $data->id_customer;
                $stbookid = "HG" . $cabang->id . $tahun . $bulan . $data->id_booking;
                $trstoreid = "HG" . $cabang->id . $tahun . $bulan . $data->id;

                return [
                    'id_real' => $data->id,
                    'trstoreid' => $trstoreid,
                    'custstid' => $custstid,
                    'stbookid' => $stbookid,
                    'parent_id' => $data->parent_id,
                    'id_rel_reservasi' => $data->id_rel_reservasi,
                    'id_customer' => $data->id_customer,
                    'create_by' => $data->create_by,
                    'create_date' => $data->create_date,
                    'is_canceled' => $data->is_canceled,
                    'is_closed' => $data->is_closed,
                    'payment_number' => $data->payment_number,
                    'payment_type' => $data->payment_type,
                    'payment_card_type' => $data->payment_card_type,
                    'payment_bank_card' => $data->payment_bank_card,
                    'payment_amount' => $data->payment_amount,
                    'rounding_amount' => $data->rounding_amount,
                    'payment_date' => $data->payment_date,
                    'is_bill_printed' => $data->is_bill_printed,
                    'bill_printed_count' => $data->bill_printed_count,
                    'is_payment_printed' => $data->is_payment_printed,
                    'total_amount' => $data->total_amount,
                    'down_payment' => $data->down_payment,
                    'redeem_point_amount' => $data->redeem_point_amount,
                    'voucher_no' => $data->voucher_no,
                    'voucher_amount' => $data->voucher_amount,
                    'kembalian' => $data->kembalian,
                    'payment_by' => $data->payment_by,
                    'order_no' => $data->order_no,
                    'is_member' => $data->is_member,
                    'transaction_point' => $data->transaction_point,
                    'date_order_menu' => $data->date_order_menu,
                    'is_order_menu_active' => $data->is_order_menu_active,
                    'start_time_order' => $data->start_time_order,
                    'end_time_order' => $data->end_time_order,
                    'entry_by' => $data->entry_by,
                    'catatan' => $data->catatan,
                    'cabang' => $cabang->id,
                    'id_booking' => $data->id_booking,
                    'booking_name' => $data->booking_name,
                    'is_take_away' => $data->is_take_away,
                    'dp_used' => $data->dp_used,
                    'biaya_kirim' => $data->biaya_kirim,
                    'trans_types' => $data->trans_types,
                    'checker_printed' => $data->checker_printed,
                    'daily_trans_no' => $data->daily_trans_no,
                    'sc_percent' => $data->sc_percent,
                    'tax_percent' => $data->tax_percent,
                    'tax_amount' => $data->tax_amount,
                    'bill_discount' => $data->bill_discount,
                    'bill_discount_percent' => $data->bill_discount_percent,
                ];
            });


            try {
			    $dataInserted = false; // Flag untuk memeriksa apakah ada data yang berhasil diinsert
			    $dataAlreadyExist = false; // Flag untuk memeriksa apakah ada data yang sudah ada
			    
			    // Proses insert data
			    $insert_data->each(function ($data) use ($id_cabang, &$dataInserted, &$dataAlreadyExist) {
			        // Periksa apakah data sudah ada
			        $exists = Transactions::where('create_date', $data['create_date'])
			            ->where('cabang', $data['cabang'])
			            ->exists();

			        if (!$exists) {
			            // Jika data belum ada, insert data baru
			            Customers::create($data);
			            $dataInserted = true; // Tandai bahwa data berhasil diinsert
			        } else {
			            $dataAlreadyExist = true; // Tandai bahwa ada data yang sudah ada
			        }
			    });
			    $logInserted = false; 
				if ($dataInserted && !$logInserted) {
				    // Jika ada data yang berhasil diinsert
				    LogInsert::create([
				        'type' => 'insert transactions',
				        'cabang' => $id_cabang,
				        'status' => 'success',
				        'deskripsi' => 'Data berhasil diinsert',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				} elseif ($dataAlreadyExist && !$logInserted) {
				    // Jika ada data yang sudah ada
				    LogInsert::create([
				        'type' => 'insert transactions',
				        'cabang' => $id_cabang,
				        'status' => 'failed',
				        'deskripsi' => 'Data sudah ada',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				} elseif (!$dataInserted && !$dataAlreadyExist) {
				    // Jika tidak ada data yang diproses (misalnya tidak ada data baru untuk diinsert)
				    LogInsert::create([
				        'type' => 'insert transactions',
				        'cabang' => $id_cabang,
				        'status' => 'failed',
				        'deskripsi' => 'Tidak ada data untuk diinsert',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				}


			} catch (\Exception $e) {
			    // Jika terjadi error, catat log kesalahan
			    LogInsert::create([
			        'type' => 'insert transactions',
			        'cabang' => $id_cabang,
			        'status' => 'failed',
			        'deskripsi' => 'Gagal menginsert data: ' . $e->getMessage(),
			        'tgl_insert' => now()
			    ]);
			}


        } else {
            LogInsert::create([
                'type' => 'insert transactions',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Tidak ada data untuk diinsert',
                'tgl_insert' => now()
            ]);
        }
    }
    public function Tembakdetails($id_cabang,$tgl)
    {
        $awal = Carbon::parse($tgl)->format('Y-m-d');
	    $akhir = Carbon::parse($tgl)->format('Y-m-d');

        $cabang = Cabang::find($id_cabang);
        if (!$cabang) {
            LogInsert::create([
                'type' => 'insert details',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Cabang tidak ditemukan',
                'tgl_insert' => now()
            ]);
            return response()->json(['message' => 'Cabang tidak ditemukan'], 404);
        }

        $details = collect();

        try {
            $status_ip_pagi = $cabang->status_ip_pagi;
            $status_ip_malam = $cabang->status_ip_malam;

            if ($status_ip_pagi === 'online' && $status_ip_malam === 'online') {
                $cust_pagi = collect(TembakData::getDetails($awal, $akhir, $cabang->id, 'pagi'));
                $cust_malam = collect(TembakData::getDetails($awal, $akhir, $cabang->id, 'malam'));
                $details = $cust_pagi->concat($cust_malam);
            } elseif ($status_ip_pagi === 'offline' && $status_ip_malam === 'online') {
                $details = collect(TembakData::getDetails($awal, $akhir, $cabang->id, 'malam'));
            } elseif ($status_ip_pagi === 'online' && $status_ip_malam === 'offline') {
                $details = collect(TembakData::getDetails($awal, $akhir, $cabang->id, 'pagi'));
            }
        } catch (\Exception $e) {
            LogInsert::create([
                'type' => 'insert details',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
                'tgl_insert' => now()
            ]);
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil data', 'error' => $e->getMessage()], 500);
        }

        if ($details->isNotEmpty()) {
            $insert_data = $details->map(function ($data) use ($cabang) {
                $bulan = Carbon::parse($data->create_date)->format('m');
                $tahun = Carbon::parse($data->create_date)->format('y');
                $itemcodest = $cabang->id . $data->item_code;
                $trstoreid = "HG" . $cabang->id . $tahun . $bulan . $data->id_trans;

                // Array untuk insert
                return [
                    'id_real' => $data->idd ?? null,
                    'trstoreid' => $trstoreid,
                    'itemcodest' => $itemcodest,
                    'id_trans' => $data->id_trans ?? null,
                    'item_code' => $data->item_code ?? null,
                    'qty' => $data->qty ?? 0,
                    'unit_price' => $data->unit_price ?? 0,
                    'unit_price_no_sc' => $data->unit_price_no_sc ?? 0,
                    'description' => $data->description ?? '',
                    'start_time_order' => $data->start_time_order ?? null,
                    'end_time_order' => $data->end_time_order ?? null,
                    'end_time_runner' => $data->end_time_runner ?? null,
                    'entry_by' => $data->entry_by ?? null,
                    'submit_time' => $data->submit_time ?? null,
                    'extra_notes' => $data->extra_notes ?? null,
                    'disc' => $data->disc ?? 0,
                    'is_cancel' => $data->is_cancel ?? 0,
                    'is_paid' => $data->is_paid ?? 0,
                    'is_exclude' => $data->is_exclude ?? 0,
                    'order_type' => $data->order_type ?? null,
                    'floor' => $data->floor ?? null,
                    'session_item' => $data->session_item ?? null,
                    'cabang' => $cabang->id, // ID cabang
                    'selected_table_no' => $data->selected_table_no ?? null,
                    'seat_id' => $data->seat_id ?? null,
                    'delivered_by' => $data->delivered_by ?? null,
                    'delivered_date' => $data->delivered_date ?? null,
                    'checker_by' => $data->checker_by ?? null,
                    'qty_finish' => $data->qty_finish ?? 0,
                    'processed_by' => $data->processed_by ?? null,
                    'qty_processed' => $data->qty_processed ?? 0,
                    'runner_by' => $data->runner_by ?? null,
                    'waitress_by' => $data->waitress_by ?? null,
                    'sort_id' => $data->sort_id ?? null,
                    'is_printed' => $data->is_printed ?? 0,
                    'qty_print' => $data->qty_print ?? 0,
                    'is_finish' => $data->is_finish ?? 0,
                    'qty_selected' => $data->qty_selected ?? 0,
                    'runner_scan_date' => $data->runner_scan_date ?? null,
                    'waitress_scan_date' => $data->waitress_scan_date ?? null,
                    'print_code' => $data->print_code ?? null,
                    'as_take_away' => $data->as_take_away ?? 0,
                ];
            });


            try {
			    $dataInserted = false; // Flag untuk memeriksa apakah ada data yang berhasil diinsert
			    $dataAlreadyExist = false; // Flag untuk memeriksa apakah ada data yang sudah ada
			    
			    // Proses insert data
			    $insert_data->each(function ($data) use ($id_cabang, &$dataInserted, &$dataAlreadyExist) {
			        // Periksa apakah data sudah ada
			        $exists = Details::where('submit_time', $data['submit_time'])
			            ->where('cabang', $data['cabang'])
			            ->exists();

			        if (!$exists) {
			            // Jika data belum ada, insert data baru
			            Customers::create($data);
			            $dataInserted = true; // Tandai bahwa data berhasil diinsert
			        } else {
			            $dataAlreadyExist = true; // Tandai bahwa ada data yang sudah ada
			        }
			    });
			    $logInserted = false; 
				if ($dataAlreadyExist && !$logInserted) {
				    LogInsert::create([
				        'type' => 'insert details',
				        'cabang' => $id_cabang,
				        'status' => 'failed',
				        'deskripsi' => 'Data sudah ada',
				        'tgl_insert' => now()
				    ]);
				    
				    $logInserted = true; // Tandai log sudah dimasukkan
				} elseif ($dataInserted && !$logInserted) {
				    LogInsert::create([
				        'type' => 'insert details',
				        'cabang' => $id_cabang,
				        'status' => 'success',
				        'deskripsi' => 'Data berhasil diinsert',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				} elseif (!$dataInserted && !$dataAlreadyExist) {
				    // Jika tidak ada data yang diproses (misalnya tidak ada data baru untuk diinsert)
				    LogInsert::create([
				        'type' => 'insert details',
				        'cabang' => $id_cabang,
				        'status' => 'failed',
				        'deskripsi' => 'Tidak ada data untuk diinsert',
				        'tgl_insert' => now()
				    ]);
				    $logInserted = true; // Tandai log sudah dimasukkan
				}


			} catch (\Exception $e) {
			    // Jika terjadi error, catat log kesalahan
			    LogInsert::create([
			        'type' => 'insert details',
			        'cabang' => $id_cabang,
			        'status' => 'failed',
			        'deskripsi' => 'Gagal menginsert data: ' . $e->getMessage(),
			        'tgl_insert' => now()
			    ]);
			}


        } else {
            LogInsert::create([
                'type' => 'insert details',
                'cabang' => $id_cabang,
                'status' => 'failed',
                'deskripsi' => 'Tidak ada data untuk diinsert',
                'tgl_insert' => now()
            ]);
        }
    }
}
