<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\PoDetail;
use App\PoHeader;
use App\ReceivingHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    //
    public function index()
    {
        if(ReceivingHeader::count()<1){
            $num = "TR-RE00001";
        }else{
            $num = ReceivingHeader::max('rh_no');
            ++$num;
        }
        $pos = PoHeader::where(['status' => 'AP'])->get();
        return view('Purchasing.receiving.create',compact('pos', 'num'));
    }
    public function show_item($id)
    {
        $items = PoHeader::where(['po_code' => $id])->firstOrFail();
        return json_encode(['header'=>$items, 'detail'=>$items->po_detail]);
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'rh_no' => 'required|string|unique:receiving_headers',
        ],['The receiving code has already been taken. Please refresh the page']);
        $create = ReceivingHeader::create($request->all());
        $po = PoHeader::where(['po_code' => $request->rh_po_no])->firstOrfail();
        $po->update(['status' => $request->rh_status]);
        foreach ($request->prod_code as $item => $value){
            $create->re_detail()->create([
                'rd_code' => $request->rh_no,
                'rd_prod_code' => $request->prod_code[$item],
                'rd_prod_name' => $request->prod_name[$item],
                'rd_prod_uom' => $request->uom[$item],
                'rd_prod_qty' => $request->rec_qty[$item],
                'rd_status' => $request->status[$item]
            ]);
            $inventory = Branch_Inventory::where(['prod_code'=> $request->prod_code[$item], 'branch_code'=>$request->rh_branch_code]);
            $inventory->update(['quantity'=> DB::raw('quantity + '.$request->rec_qty[$item])]);
        }
        return redirect('/receiving/create')->with('status', "PO# ".strtoupper($request->rh_po_no)." successfully received.");
    }
}