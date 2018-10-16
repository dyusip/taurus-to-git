<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;
use Excel;
use File;
use Activity;

class ImportInventoryController extends Controller
{
    //
    public function index()
    {
        return view('Purchasing.import.create');
    }
    public function import(Request $request){
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required'
        ));

        if($request->hasFile('file')){
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {
                })->get();
                //$status = Inventory::count()<1?'firstOrCreate':'updateOrCreate';
                if(!empty($data) && $data->count()){

                    foreach ($data as $key => $value) {
                        $insert[] = [
                            'code' => $value->code,
                            'name' => $value->name,
                            'desc' => $value->desc,
                            'uom' => $value->uom,
                            'pqty' => $value->pqty,
                            'status' => $value->status,

                        ];
                        if(Inventory::count()<1){
                            $num = "TR-ITM00001";
                        }else{
                            $num = Inventory::max('code');
                            ++$num;
                        }
                        //$insertData = Inventory::updateOrCreate(['name' => $value->name],
                        $insertData = Inventory::firstOrCreate(['name' => $value->name],
                            ['code' => $num,
                            'desc' => $value->desc,
                            'uom' => $value->uom,
                            'pqty' => $value->pqty,
                            'status' => 'AC',]);
                    }

                    if(!empty($insert)){

                        //$insertData = DB::table('inventories')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                            Activity::log("Imported inventory account", Auth::user()->id);
                        }else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }

                return back();

            }else {
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
                return back();
            }
        }
    }
    public function branch_index()
    {
        $branches = Branch::where(['status' => 'AC'])->get();
        return view('Purchasing.import.branch',compact('branches'));
    }
    public function branch_import(Request $request){
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required'
        ));

        if($request->hasFile('file')){
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {

                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {
                })->get();
                if(!empty($data) && $data->count()){

                    foreach ($data as $key => $value) {
                        $price = $value->price!=''?$value->price:0;
                        $cost = $value->cost!=''?$value->cost:0;
                        $quantity = $value->quantity!=''?$value->quantity:0;
                        $insert[] = [
                            'branch_code' => $value->branch_code,
                            'prod_code' => $value->prod_code,
                            'price' => $price,
                            'cost' => $cost,
                            'quantity' => $quantity,

                        ];
                        //$product = Inventory::where('name', 'like', '%' . $value->name . '%')->first();
                        $product = Inventory::where(['name' => $value->name]);
                        if($product->count()>0){
                            $check = Branch_Inventory::where(['branch_code' => $request->branch_code,
                                'prod_code' => $product->first()->code]);
                            $check_cp = Branch_Inventory::where(['prod_code' => $product->first()->code])->where('branch_code', '!=' , $request->branch_code);
                            $quantity = $check->count()>0?$check->first()->quantity:$quantity;
                            if($check_cp->count()>0){
                                $price = ($check->count()>0 && $check->first()->price == 0)?$check_cp->first()->price:$price;
                                $cost =  ($check->count()>0 && $check->first()->cost == 0)?$check_cp->first()->cost:$cost;
                            }
                            $insertData = Branch_Inventory::updateOrCreate(['branch_code' => $request->branch_code,
                                'prod_code' => $product->first()->code],
                                ['price' => $price,
                                    'cost' => $cost,
                                    'quantity' => $quantity]);
                        }
                    }

                    if(!empty($insert)){

                        //$insertData = DB::table('branch__inventories')->insert($insert);

                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                            Activity::log("Imported inventory in $request->branch_code branch", Auth::user()->id);
                        }else {
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }

                return back();

            }else {
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
                return back();
            }
        }
    }
}