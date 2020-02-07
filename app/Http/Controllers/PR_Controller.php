<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\PrHeader;
use App\ReqHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Activity;
class PR_Controller extends Controller
{
    //
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->from)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->to)->format('Y-m-d');
        if(isset($request->prod_code)) {
            $inventories = Branch_Inventory::whereIn('branch__inventories.prod_code', $request->prod_code)
                ->where(['branch__inventories.branch_code' => $request->branch_code])
                //->join('so_headers as soh','soh.branch_code','=','branch__inventories.branch_code').

                ->join('so_headers as soh', function ($join) use ($from, $to) {
                    $join->on('soh.branch_code', '=', 'branch__inventories.branch_code')
                        ->whereBetween('so_date', [$from, $to]);
                })
                ->join('so_details as sod', function ($join) use ($request) {
                    $join->on('sod.sod_code', '=', 'soh.so_code');
                    $join->on('branch__inventories.prod_code', '=', 'sod.sod_prod_code');
                })
                ->join('branch__inventories as bri2', function ($join)  {
                    $join->on('sod.sod_prod_code', '=', 'bri2.prod_code')
                        ->where('bri2.branch_code', '=','TR-BR00001');
                })
                ->select(DB::raw('bri2.cost,bri2.quantity, bri2.price,sum(sod_prod_qty) as qty,sum(sod_prod_qty * bri2.cost) total, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code', 'cost', 'price','quantity', 'sod_prod_name', 'sod_prod_uom')
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        ->where('bri.branch_code', '=', 'TR-BR00001')
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'cw_qty')
                ->get();
        }


        if(isset($request->prod_code1)){
            if(PrHeader::count()<1){
                $num = "TR-PR00001";
            }else{
                $num = PrHeader::max('prh_no');
                ++$num;
            }
            $prs = Branch_Inventory::whereIn('prod_code',$request->prod_code1)
                ->where(['branch__inventories.branch_code' => $request->branch_code])
                ->join('so_headers as soh', function ($join) use($from, $to) {
                    $join->on('soh.branch_code','=','branch__inventories.branch_code')
                        ->whereBetween('so_date', [$from, $to]);
                })
                ->join('so_details as sod', function ($join) use($request) {
                    $join->on('sod.sod_code', '=', 'soh.so_code');
                    $join->on('branch__inventories.prod_code','=','sod.sod_prod_code');
                })->select(DB::raw('cost, sum(sod_prod_qty) as qty,sum(sod_prod_qty * cost) total, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','cost','sod_prod_name','sod_prod_uom')
                ->get();
            $pr_date = date('Y-m-d');
            $create = PrHeader::create([
                'prh_no'    => $num,
                'prh_reqby' => Auth::user()->username,
                'pr_date'   => $pr_date,
                'pr_total'  => 0,
            ]);
            $pr_total = 0;
            foreach ($prs as $pr)
            {
                $create->pr_detail()->create([
                    'prd_code'      => $num,
                    'prd_prod_code' => $pr->sod_prod_code,
                    'prd_prod_name' => $pr->sod_prod_name,
                    'prd_prod_uom'  => $pr->sod_prod_uom,
                    'prd_prod_qty'  => $pr->qty,
                    'prd_prod_price'  => $pr->cost,
                    'prd_prod_amount'  => $pr->total,
                ]);
                $pr_total += $pr->total;
            }
            PrHeader::where(['prh_no' => $num])->update(['pr_total' => $pr_total]);
            Activity::log("Purchase Request # $num", Auth::user()->id);
            if(!isset($request->prod_code)) {
                return redirect('/inventory_analysis')->with('status', "Purchase Request# ".strtoupper($num)." successfully created.");
            }
        }

       /* foreach ($inventories as $inventory)
        {
            echo $inventory->sod_prod_name." - ". $inventory->totalqty."<br>";
        }*/
        if(isset($request->prod_code)) {
            $branch = Branch::where(['code' => $request->branch_code])->first();
            return view('Purchasing.request.create', compact('inventories', 'branch', 'request'));
        }

    }
    public function show_item($id)
    {
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
        if(ReqHeader::count()<1){
            $num = "TR-RQ00001";
        }else{
            $num = ReqHeader::max('rqh_code');
            ++$num;
        }
        $request->merge(['rqh_code' => $num]);
        $create = ReqHeader::create($request->all());
        foreach ($request->prod_code as $item => $value){
            $create->req_detail()->create([
                'rqd_code' => $request->rqh_code,
                'rqd_prod_code' => $request->prod_code[$item],
                'rqd_prod_name' => $request->prod_name[$item],
                'rqd_prod_uom' => $request->uom[$item],
                'rqd_prod_qty' => $request->qty[$item],
                'rqd_prod_price' => $request->cost[$item],
                'rqd_prod_amount' => $request->amount[$item],
                'rqd_prod_cost' => $request->prod_cost[$item],
                'rqd_prod_srp' => $request->prod_srp[$item],
            ]);
        }
        Activity::log("Created Transfer Item # $request->tf_code", Auth::user()->id);
        return redirect('/inventory_analysis')->with('status', "RQ# ".strtoupper($request->rqh_code)." successfully created.");
        //view('Purchasing.inventory_analysis.inventory_analysis');
    }
}
