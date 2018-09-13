<?php

namespace App\Http\Controllers\SmInventory;

use App\Branch;
use App\Branch_Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;

class SmInventoryController extends Controller
{
    //
    public function index()
    {
        //
        $branches = Branch::where(['status' => 'AC'])->get();
        if(Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }elseif (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }
        return view($position.'.inventory.view',compact('branches'));
    }
    public function show($id){
        $inventories = Branch_Inventory::join('inventories','inventories.code','=','branch__inventories.prod_code')->where(['branch_code' => $id])->select('*');

        /*if($inventories->count()==0){
            $inventories->firstOrFail();
        }*/
        return Datatables::of($inventories) ->addColumn('action', function ($inventories) {
            return new HtmlString('<a href="#edit-prod-modal" data-toggle="modal" class="text-success" id="btn-edit" data-id='.$inventories->id.' data-branch='.$inventories->branch_code.' data-prod='.$inventories->prod_code.'><i class="fa fa-edit"></i></a>');
        })->make();
    }
    public function edit($id)
    {
        //
        $inventory = Branch_Inventory::where(['branch_code' => Auth::user()->branch, 'prod_code' => $id])->firstOrFail();
        return $inventory;
    }
    public function update(Request $request)
    {
        //
        $inventory = Branch_Inventory::where(['branch_code' => Auth::user()->branch, 'prod_code' => $request->prod_code]);
        $inventory->update(['price' => $request->price]);

        return redirect('/salesman/inventory')->with('status', "Product {$inventory->firstOrFail()->inventory->name} successfully updated");
    }

}
