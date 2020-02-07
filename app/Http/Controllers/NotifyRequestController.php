<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\PrHeader;
use App\ReqDetail;
use App\ReqHeader;
use App\TransferHeaders;
use Illuminate\Http\Request;
use Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifyRequestController extends Controller
{
    //
    public function index()
    {
        $requests = ReqHeader::where(['req_status' => 'PD'])->get();
        return view('Partsman.request_item.notification',compact('requests'));
    }
    public function show($id)
    {
        $request = ReqHeader::where(['rqh_code' => $id])->firstOrFail();
        $details = ReqDetail::where(['rqd_code' => $id])
            ->select('*')
            ->selectSub(function ($query)  {

                /** @var $query \Illuminate\Database\Query\Builder */
                $query->from('branch__inventories as bri')
                    ->selectRaw('sum(quantity)')
                    ->where('bri.branch_code', '=', 'TR-BR00001')
                    ->whereRaw('`bri`.`prod_code` = `rqd_prod_code`')->groupBy('prod_code');

            }, 'cw_qty')
            ->selectSub(function ($query)  use($request) {

                /** @var $query \Illuminate\Database\Query\Builder */
                $query->from('branch__inventories as bri')
                    ->selectRaw('sum(quantity)')
                    ->where('bri.branch_code', '=', $request->req_from)
                    ->whereRaw('`bri`.`prod_code` = `rqd_prod_code`')->groupBy('prod_code');

            }, 'br_qty')
            ->get();
        return json_encode(['header'=>$request, 'detail'=>$details,'branch'=> $request->req_from_branch]);
    }
    public function store(Request $request)
    {
        if($request->button == 'create')
        {
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
                $fr_inv = Branch_Inventory::where(['prod_code'=> $request->prod_code[$item], 'branch_code'=> $request->from_branch]);
                $fr_inv->update(['quantity'=> DB::raw('quantity - '.$request->qty[$item])]);
            }
            //SE = Served
            //CA = Cancelled
            ReqHeader::where(['rqh_code' => $request->rqh_code])->update(['req_status' => 'SE','req_checkby' => Auth::user()->username]);
            ReqDetail::where(['rqd_code' => $request->rqh_code])->whereIn('rqd_prod_code',$request->prod_code)
            ->update(['rqd_status' => 'SE']);
            $req_detail = ReqDetail::where(['rqd_code' => $request->rqh_code])->whereNotIn('rqd_prod_code',$request->prod_code);
            $req_detail->update(['rqd_status' => 'CA']);
            //Saving for Purchase Request if partsman cancel or remove the item in the list
            /*if($req_detail->count() > 0 )
            {
                if(PrHeader::count()<1){
                    $code = "TR-PR00001";
                }else{
                    $code = PrHeader::max('prh_no');
                    ++$code;
                }
                $pr_date = date('Y-m-d');
                $create = PrHeader::create([
                    'prh_no'    => $code,
                    'prh_reqby' => Auth::user()->username,
                    'pr_date'   => $pr_date,
                    'pr_total'  => 0,
                ]);
                $pr_total = 0;
                foreach ($req_detail->get() as $item)
                {
                    $create->pr_detail()->create([
                        'prd_code'      => $code,
                        'prd_prod_code' => $item->rqd_prod_code,
                        'prd_prod_name' => $item->rqd_prod_name,
                        'prd_prod_uom'  => $item->rqd_prod_uom,
                        'prd_prod_qty'  => $item->rqd_prod_qty,
                        'prd_prod_price'  => $item->rqd_prod_price,
                        'prd_prod_amount'  => $item->rqd_prod_amount,
                    ]);
                    $pr_total += $item->total;
                }
                PrHeader::where(['prh_no' => $code])->update(['pr_total' => $pr_total]);
                Activity::log("Purchase Request# $code", Auth::user()->id);
            } // end of saving in Purchase Request*/
            Activity::log("Created Transfer Item # $request->tf_code", Auth::user()->id);
            return redirect('/request_item/request')->with('status', "TF# ".strtoupper($request->tf_code)." successfully created.");
        }elseif ($request->button == 'cancel'){
            ReqHeader::where(['rqh_code' => $request->rqh_code])->update(['req_status' => 'CA', 'req_checkby' => Auth::user()->username
            ]);
            ReqDetail::where(['rqd_code' => $request->rqh_code])->update(['rqd_status' => 'CA']);
            Activity::log("Cancelled Request Item # $request->rqh_code", Auth::user()->id);
            return redirect('/request_item/request')->with('status', "TF# ".strtoupper($request->rqh_code)." successfully cancelled.");
        }

    }
}