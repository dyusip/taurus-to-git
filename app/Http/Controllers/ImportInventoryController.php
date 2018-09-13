<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;
use Excel;
use File;

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
                        $insertData = Inventory::updateOrCreate(['code' => $num],
                            ['name' => $value->name,
                            'desc' => $value->desc,
                            'uom' => $value->uom,
                            'pqty' => $value->pqty,
                            'status' => 'AC',]);
                    }

                    if(!empty($insert)){

                        //$insertData = DB::table('inventories')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
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
                        $insert[] = [
                            'branch_code' => $value->branch_code,
                            'prod_code' => $value->prod_code,
                            'price' => $value->price,
                            'cost' => $value->cost,
                            'quantity' => $value->quantity,

                        ];
                        $product = Inventory::where('name', 'like', '%' . $value->name . '%')->first();
                        $insertData = Branch_Inventory::updateOrCreate(['branch_code' => $request->branch_code,
                            'prod_code' => $product->code],
                            ['price' => $value->price,
                            'cost' => $value->cost,
                            'quantity' => $value->quantity]);
                    }

                    if(!empty($insert)){

                        //$insertData = DB::table('branch__inventories')->insert($insert);

                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
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
