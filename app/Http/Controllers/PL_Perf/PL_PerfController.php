<?php

namespace App\Http\Controllers\PL_Perf;

use App\ReqHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PL_PerfController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
    private function process($request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = ReqHeader::whereBetween('req_date',[$from, $to])
            ->join('req_details as rqd','rqh_code','=','rqd.rqd_code')
            ->join('branches as br','br.code','=','req_from')
            ->select(DB::raw('br.name,req_from, SUM(rqd_prod_qty) as rqd_prod_qty,SUM(rqd_prod_qty * rqd_prod_price) as total_rqd_cost'))
            ->selectSub(function ($query) use ($from, $to) {

                /** @var $query \Illuminate\Database\Query\Builder */
                $query->from('transfer_headers as tfh')
                    ->whereBetween('tf_date',[$from, $to])
                    ->join('transfer_details as tfd', function ($join)  {
                        $join->on('tfh.tf_code', '=', 'tfd.td_code');
                    })
                    ->selectRaw('sum(tf_prod_qty)')
                   ->whereRaw('`req_from` =  `tfh`.`to_branch`')->groupBy('to_branch');

            }, 'tf_prod_qty')
            ->groupBy('name','req_from')
            ->get();
        return $items;
    }
    function index()
    {
        return view($this->position().'.pl_perf.pl_perf');
    }
    function show(Request $request)
    {
        $items = $this->process($request);
        return view($this->position().'.pl_perf.pl_perf', compact('items','request'));
    }
}
