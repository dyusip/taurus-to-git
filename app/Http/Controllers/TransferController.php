<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use App\TransferHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    //
    public function index()
    {
        if(TransferHeaders::count()<1){
            $num = "TR-TF00001";
        }else{
            $num = TransferHeaders::max('tf_code');
            ++$num;
        }
        $branches = Branch::where(['status' => 'AC'])->whereNotIn('code' , [Auth::user()->branch])->get();
        return view('Purchasing.transfer.create',compact('branches','num'));
    }
    public function show_branch($id)
    {
        $info = Branch::where(['code' => $id])->firstOrFail();

        return $info;
    }
    public function show_item($id)
    {
        //$info = Branch_Inventory::with('inventory')->where(['branch_code' => $id]);
        $info = Branch_Inventory::with('inventory')->where(['branch_code' => Auth::user()->branch]);
        return $info->get();
    }
    public function item_data($id , $code)
    {
        //$item = Branch_Inventory::with('inventory')->where(['prod_code' => $id, 'branch_code' => $code])->firstOrFail();
        $item = Branch_Inventory::with('inventory')->where(['prod_code' => $id, 'branch_code' => Auth::user()->branch])->firstOrFail();
        return $item;
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'tf_code' => 'required|string|unique:transfer_headers',
        ],['The transfer code has already been taken. Please refresh the page']);
        $create = TransferHeaders::create($request->all());
        foreach ($request->prod_code as $item => $value){
            $create->tf_detail()->create([
                'td_code' => $request->tf_code,
                'tf_prod_code' => $request->prod_code[$item],
                'tf_prod_name' => $request->prod_name[$item],
                'tf_prod_uom' => $request->uom[$item],
                'tf_prod_qty' => $request->qty[$item],
                'tf_prod_price' => $request->cost[$item],
                'tf_prod_amount' => $request->amount[$item],
            ]);
        }
        return redirect('/transfer/create')->with('status', "TF# ".strtoupper($request->tf_code)." successfully created.");
    }
}
