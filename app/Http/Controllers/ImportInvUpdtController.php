<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Excel;
use File;
use Activity;

class ImportInvUpdtController extends Controller
{
    //
    public function index()
    {
        return view('Purchasing.import.inventory');
    }
    public function import(Request $request)
    {
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
                        $insert[] = [
                            'code' => $value->code,
                            'name' => $value->name,
                            'cost' => $value->cost,
                            'price' => $value->price,

                        ];
                        //$product = Inventory::where('name', 'like', '%' . $value->name . '%')->first();
                        $product = Inventory::where(['code' => $value->code]);
                        if($product->count()>0){
                            $insertData = $product->update(['desc' => $value->desc,'name' => $value->name]);
                            /*$insertData = Branch_Inventory::where(['prod_code' => $product->first()->code])
                            ->update(['price' => $price,
                                    'cost' => $cost]);*/

                        }
                    }
                    if(!empty($insert)){

                        //$insertData = DB::table('branch__inventories')->insert($insert);

                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                            Activity::log("Imported updated cost and price in Master List.", Auth::user()->id);
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
