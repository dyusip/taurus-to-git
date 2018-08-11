<?php

namespace App\Http\Controllers\SmInventory;

use App\Branch;
use App\Branch_Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;

class SmInventoryController extends Controller
{
    //
    public function index()
    {
        //
        $branches = Branch::where(['status' => 'AC'])->get();
        return view('Salesman.inventory.view',compact('branches'));
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

}
