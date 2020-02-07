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
use Activity;

class ReceivingController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
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
        return view($this->position().'.receiving.create',compact('pos','num'));
    }
    public function show_item($id)
    {
        $items = PoHeader::where(['po_code' => $id])->firstOrFail();
        //$products = $items->po_detail;
        $products = PoHeader::where(['po_code' => $id])
            ->join('po_details as pod','pod.pod_code','=','po_code')
            ->join('branch__inventories as bri', function ($join)  {
                $join->on('bri.prod_code','=','pod.prod_code')
                    ->where('bri.branch_code', '=','TR-BR00001');
            })
            ->select('pod.*','bri.cost','bri.price')
            ->get();
        //$details = ReceivingHeader::where(['rh_po_no' => $id])->firstOrFail();
        /*$details = ReceivingDetail::where(['rd_status' => 'LA'])->whereHas('re_header', function ($query) use ($id) {
            $query->where(['rh_po_no' => $id]);
        });*/
        $details = ReceivingDetail::where(['rd_status' => 'LA'])->whereHas('re_header', function ($query) use ($id) {
            $query->where(['rh_po_no' => $id]);
        })
            ->join('branch__inventories as bri', function ($join)  {
                $join->on('bri.prod_code','=','rd_prod_code')
                    ->where('branch_code', '=','TR-BR00001');
            })
            ->join('po_details as pod', function ($join) use($id){
                $join->on('pod.prod_code','=','rd_prod_code')
                    ->where('pod_code', '=',$id);
            })
            ->select(DB::raw('rd_prod_code,rd_prod_name,rd_prod_uom,rd_status,SUM(rd_prod_qty) as rd_prod_qty, bri.cost,bri.price, pod.prod_price'))
            ->groupBy('rd_prod_code','rd_prod_name','rd_prod_uom','rd_status','cost','price','prod_price');
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
    public function update_cost($id, $cost)
    {
        Branch_Inventory::where(['prod_code' => $id])->update(['cost' => $cost]);
        Activity::log("Updated Product $id", Auth::user()->id);
        /*$br_inv = Branch_Inventory::where(['prod_code' => $id]);
        $dt = date('Y-m-d');
        if($cost > $br_inv->first()->cost)
        {
            PriceChange::create([
                'pc_branch_code' => Auth::user()->branch,
                'pc_prod_code' => $id,
                'pc_cost' => $cost,
                'pc_srp' => 0,
                'pc_date' => $dt]);
        }*/

    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'rh_no' => 'required|string|unique:receiving_headers',
        ],['The receiving code has already been taken. Please refresh the page']);
        $create = ReceivingHeader::create($request->all());
        $po = PoHeader::where(['po_code' => $request->rh_po_no])->firstOrfail();
        $po->update(['status' => $request->po_status]);
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
        Activity::log("Received PO# $request->rh_po_no", Auth::user()->id);
        return redirect('/receiving/create')->with('status', "PO# ".strtoupper($request->rh_po_no)." successfully received.");
    }
}