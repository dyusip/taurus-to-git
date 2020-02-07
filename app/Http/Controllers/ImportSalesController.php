<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use App\SalesLogs;
use App\SoDetail;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Excel;
use File;
use Activity;

class ImportSalesController extends Controller
{
    //
    public function index()
    {
        $branches = Branch::where(['status' => 'AC'])->get();
        return view('Purchasing.import.sales',compact('branches'));
    }
    public function store(Request $request)
    {
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required'
        ));

        if($request->hasFile('file')) {
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function ($reader) {
                })->get();
                $data_error = Excel::load($path, function ($reader) {
                })->get();
                if (!empty($data) && $data->count()) {
                    $so_date = Carbon::createFromFormat('m/d/Y', $request->so_date)->format('Y-m-d');
                    $so = SoHeader::where(['so_date' => $so_date, 'branch_code' => $request->branch_code])->count();
                    if ($so < 1) {
                        $branch = Branch::where(['code' => $request->branch_code])->first();
                        $si = array();
                        foreach ($data as $key => $value) {
                            $si[] = $value['si'];
                        }
                        $ctr = 0;
                        $error = "";
                        foreach ($si as $key_si) {//Unique SI. check file ERROR
                            $items = array();
                            foreach ($data_error as $key => $value) {//all data in the file
                                if($value['quantity']==""){
                                    $error = 'Quantity is required. Please check your file.';
                                    break 2;
                                }
                                if($value['price']==""){
                                    $error = 'Price is required. Please check your file.';
                                    break 2;
                                }
                                if ($key_si == $value['si']) {// check the unique SI and SI in the file
                                    if (in_array($value['name'], $items)) {//check items is present it the list
                                        $error = 'It seems you have duplicate items in your SI. Please check your file.';
                                        break 2;
                                    }
                                    $items[]= $value['name'];
                                    unset($data_error[$key]);
                                }
                            }
                        }// end of checking file ERROR
                        if($error != "" ){// checking if file has error
                            Session::flash('error', $error);// if has an error
                            return back();
                        }else{
                            foreach (array_unique($si) as $str) {
                                if (SoHeader::count() < 1) {
                                    $num = "TR-SO00001";
                                } else {
                                    $num = SoHeader::max('so_code');
                                    ++$num;
                                }
                                $logs = array();
                                $insertData = "";
                                $total = 0;
                                foreach ($data as $key => $value) {
                                    $ctr++;
                                    if ($value['si'] == $str) {
                                        $product = Inventory::where(['name' => $value->name]);
                                        if ($product->count() > 0) {
                                            $check = Branch_Inventory::where(['branch_code' => $request->branch_code,
                                                'prod_code' => $product->first()->code]);
                                            if ($check->count() > 0) {
                                                if ($check->first()->quantity >= $value->quantity) {
                                                    $amount = $value->price / $value->quantity;
                                                    $total += $value->price;
                                                    $check->update(['quantity' => DB::raw('quantity - ' . $value->quantity)]);
                                                    $insertData = SoDetail::create([
                                                        'sod_code' => $num,
                                                        'sod_prod_code' => $check->first()->prod_code,
                                                        'sod_prod_name' => $value->name,
                                                        'sod_prod_uom' => $product->first()->uom,
                                                        'sod_prod_qty' => $value->quantity,
                                                        'sod_prod_price' => $amount,
                                                        'sod_less' => 0,
                                                        'sod_prod_amount' => $value->price,
                                                    ]);
                                                } else {
                                                    $amount1 = $value->price / $value->quantity;
                                                    $logs[] = [
                                                        'code' => $product->first()->code,
                                                        'name' => $value->name,
                                                        'quantity' => $value->quantity,
                                                        'price' => $amount1,
                                                        'amount' => $value->price,
                                                        'remarks' => "Product quantity is not sufficient in {$branch->name}."
                                                    ];
                                                }
                                            } else {
                                                $amount2 = $value->price / $value->quantity;
                                                $logs[] = [
                                                    'code' => $product->first()->code,
                                                    'name' => $value->name,
                                                    'quantity' => $value->quantity,
                                                    'price' => $amount2,
                                                    'amount' => $value->price,
                                                    'remarks' => "Product is not found in {$branch->name}."
                                                ];
                                            }
                                        } else {
                                            $amount2 = $value->price / $value->quantity;
                                            $logs[] = [
                                                'code' => 'not found',
                                                'name' => $value->name,
                                                'quantity' => $value->quantity,
                                                'price' => $amount2,
                                                'amount' => $value->price,
                                                'remarks' => "Product is not found in the system."
                                            ];
                                        }
                                        unset($data[$key]);
                                    }
                                }
                                //if (isset($insertData) && $insertData != "") {
                                $salesman = ($branch->user == "") ? $branch->user->username : 'None';
                                SoHeader::create([
                                    'so_code' => $num,
                                    'branch_code' => $request->branch_code,
                                    'jo_code' => $str,
                                    'so_prepby' => Auth::user()->username,
                                    'so_salesman' => $salesman,
                                    'so_date' => $request->so_date,
                                    'so_amount' => $total
                                ]);
                                //}
                                if (isset($logs) && $logs != "") {
                                    foreach ($logs as $item => $value) {
                                        SalesLogs::create([
                                            'sol_code' => $num,
                                            'sol_prod_code' => $value['code'],
                                            'sol_prod_name' => $value['name'],
                                            'sol_prod_qty' => $value['quantity'],
                                            'sol_prod_price' => $value['price'],
                                            'sol_prod_amount' => $value['amount'],
                                            'sol_remarks' => $value['remarks'],
                                        ]);
                                    }
                                }
                            }
                            if ($ctr > 0) {
                                Session::flash('success', "Your Data has successfully imported.");
                                Activity::log("Imported sales order in $branch->name branch", Auth::user()->id);
                                return back();
                            } else {
                                Session::flash('error', 'Error inserting the data..');
                                return back();
                            }
                        }

                    } else {
                        Session::flash('error', 'This sales order is already been imported.');
                        return back();
                    }
                } else {
                    Session::flash('error', 'File is a ' . $extension . ' file.!! Please upload a valid xls/csv file..!!');
                    return back();
                }
            }
        }
    }
}