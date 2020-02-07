<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use App\TransferHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Activity;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    private function position()
    {
        if (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
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
        return view($this->position().'.transfer.create',compact('branches','num'));
    }
    public function show_branch($id)
    {
        $info = Branch::where(['code' => $id])->firstOrFail();

        return $info;
    }
    public function show_item($id)
    {
        //$info = Branch_Inventory::with('inventory')->where(['branch_code' => $id, 'branch_code' => Auth::user()->branch]);
        //$info = Branch_Inventory::with('inventory')->where(['branch_code' => Auth::user()->branch]);
        $prod_from = Branch_Inventory::where('branch_code','=',$id)->get();
        $prod_to = Branch_Inventory::where('branch_code',Auth::user()->branch)->get();
        $prod_1 = array();
        $prod_2 = array();
        foreach ($prod_from as $key){
            $prod_1[] = $key->prod_code;
        }
        foreach ($prod_to as $key){
            $prod_2[] = $key->prod_code;
        }
        $result = array_intersect($prod_1, $prod_2);
        $info = Branch_Inventory::with('inventory')->where(['branch_code' =>  Auth::user()->branch])->whereIn('prod_code',$result);
        //return $result;

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
        /*$this->validate($request, [
            'tf_code' => 'required|string|unique:transfer_headers',
        ],['The transfer code has already been taken. Please refresh the page']);*/
        if(TransferHeaders::count()<1){
            $num = "TR-TF00001";
        }else{
            $num = TransferHeaders::max('tf_code');
            ++$num;
        }
        $request->merge(['tf_code' => $num]);
        $request->merge(['tf_status' => 'AP']);
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
                'tf_prod_cost' => $request->prod_cost[$item],
                'tf_prod_srp' => $request->prod_srp[$item],
            ]);

            //add to inventory to branch
            $to_inv = Branch_Inventory::where(['prod_code'=> $request->prod_code[$item], 'branch_code'=> $request->to_branch]);
            $to_inv->update(['quantity'=> DB::raw('quantity + '.$request->qty[$item])]);

            //minus to inventory from branch
            $fr_inv = Branch_Inventory::where(['prod_code'=> $request->prod_code[$item], 'branch_code'=> Auth::user()->branch]);
            $fr_inv->update(['quantity'=> DB::raw('quantity - '.$request->qty[$item])]);
        }
        Activity::log("Created Transfer Item # $request->tf_code", Auth::user()->id);
        return redirect('/transfer/create')->with('status', "TF# ".strtoupper($request->tf_code)." successfully created.");
    }
}
