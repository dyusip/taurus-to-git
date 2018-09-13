<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\PoDetail;
use App\PoHeader;
use App\ReceivingDetail;
use App\ReceivingHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivingController extends Controller
{
    //
    public function index()
    {
        $route = Auth::user()->position == 'PURCHASING'?'Purchasing':'Partsman';
        if(ReceivingHeader::count()<1){
            $num = "TR-RE00001";
        }else{
            $num = ReceivingHeader::max('rh_no');
            ++$num;
        }
        $pos = PoHeader::where(['status' => 'AP'])->orWhere(['status' => 'OP'])->get();
        return view($route.'.receiving.create',compact('pos','num'));
    }
    public function show_item($id)
    {
        $items = PoHeader::where(['po_code' => $id])->firstOrFail();
        $products = $items->po_detail;
        //$details = ReceivingHeader::where(['rh_po_no' => $id])->firstOrFail();
        $details = ReceivingDetail::where(['rd_status' => 'LA'])->whereHas('re_header', function ($query) use ($id) {
            $query->where(['rh_po_no' => $id]);
        });
        if($details->count()>0){
            $products = $details->get();
        }
        return json_encode(['header'=>$items, 'detail'=> $products]);
    }
    public function show_qty($id, $code)
    {
        $item = PoDetail::where(['pod_code' => $id, 'prod_code' =>$code])->firstOrFail();
        return $item->prod_qty;
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