<?php

namespace App\Http\Controllers\ERP_Report;

use App\BiReplicate;
use App\Branch;
use App\Inventory;
use App\InvPosition;
use App\MiscHeader;
use App\SoHeader;
use App\SrHeader;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use Spatie\Activitylog\Models\Activity;

class ERP_Controller extends Controller
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
    function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.erp_report.erp',compact('branches'));
    }
    function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == 'all'){
            //ALL BRANCH
            $last_day = date('Y-m-d',strtotime($from .' - 1 day'));
            $inv_beg = InvPosition::where(['ip_date' => $last_day])
                ->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();
            $cw = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('from_branch,SUM(bri.cost * tf_prod_qty) as total_cw_cost, SUM(bri.price * tf_prod_qty) as total_cw_srp'))
                ->groupBy('from_branch')
                ->first();
            $tf_in = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','!=','TR-BR00001')->where('to_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('SUM(bri.cost * tf_prod_qty) as total_tf_in_cost, SUM(bri.price * tf_prod_qty) as total_tf_in_srp'))
                //->groupBy('from_branch')
                ->first();
            $sales = SoHeader::whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.cost * sod_prod_qty) as total_so_cost, 
                SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_so_srp'))
                //->groupBy('branch_code')
                ->first();
            $tf_out = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','!=','TR-BR00001')->where('to_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('SUM(bri.cost * tf_prod_qty) as total_tf_out_cost, SUM(bri.price * tf_prod_qty) as total_tf_out_srp'))
                //->groupBy('from_branch')
                ->first();
            $return = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','!=','TR-BR00001')->where('to_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('SUM(bri.cost * tf_prod_qty) as total_return_cost, SUM(bri.price * tf_prod_qty) as total_return_srp'))
                //->groupBy('from_branch')
                ->first();
            $sales_return = SrHeader::whereBetween('sr_date', [$from, $to])
                ->join('so_headers as soh','soh.so_code','=','sr_headers.so_code')
                ->join('sr_details as srd','srd.srd_code','=','sr_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'srd.srd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                ->select(DB::raw('soh.branch_code, SUM(bri.cost * srd.srd_prod_qty) as total_so_return_cost, 
                SUM(srd_prod_price * srd_prod_qty - ((srd.srd_prod_price * srd_prod_qty) * (srd_less/100))) as total_so_return_srp'))
                ->groupBy('branch_code')
                ->first();
            /*$sales_disc = SoHeader::whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.price * sod_prod_qty) as total_sd_srp,
                (SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) / sum(bri.price * sod_prod_qty) * 100)  as total_so_disc_cost, 
                SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) as total_so_disc_srp'))
                //->groupBy('branch_code')
                ->first();*/
            $sales_disc = SoHeader::whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.price * sod_prod_qty) as total_sd_srp,
                (SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) / sum(bri.price * sod_prod_qty) * 100)  as total_so_disc_cost, 
                SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) as total_so_disc_srp,
                (SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) / sum(bri.price * sod_prod_qty) * 100) as pos_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) as pos_disc,
                (SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) / sum(bri.price * sod_prod_qty) * 100) as neg_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) as neg_disc'))
                //->groupBy('branch_code')
                ->first();
            $misc_in = MiscHeader::whereBetween('msh_date',[$from, $to])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                    ->where(['msd_remarks' => 'IN']);
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_in_cost, SUM(bri.price * msd.msd_prod_qty) as misc_in_srp'))
                ->first();
            $misc_out = MiscHeader::whereBetween('msh_date',[$from, $to])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'OUT']);
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_out_cost, SUM(bri.price * msd.msd_prod_qty) as misc_out_srp'))
                ->first();
            //echo $tf_out;
            $end_inv = InvPosition::where(['ip_date' => $to])
                ->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();


        }elseif($request->optCustType == 'branch'){
            //Branch
            $last_day = date('Y-m-d',strtotime($from .' - 1 day'));
            $inv_beg = InvPosition::where(['ip_date' => $last_day])->where(['ip_branch_code' => $request->branch])
                //->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();
            $cw = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('to_branch','=',$request->branch)->where('from_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bir.bir_branch_code','=','from_branch')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = tf_date');
                })
                ->select(DB::raw('to_branch, SUM(bri.cost * tf_prod_qty) as total_cw_cost, SUM(bri.price * tf_prod_qty) as total_cw_srp,
                SUM(IF((bir.bir_cost * tf_prod_qty) > (bri.cost * tf_prod_qty),(bir.bir_cost * tf_prod_qty) - (bri.cost * tf_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bir.bir_price * tf_prod_qty) > (bri.price * tf_prod_qty),(bir.bir_price * tf_prod_qty) - (bri.price * tf_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bri.cost * tf_prod_qty) > (bir.bir_cost * tf_prod_qty),(bri.cost * tf_prod_qty) - (bir.bir_cost * tf_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bri.price * tf_prod_qty) > (bir.bir_price * tf_prod_qty),(bri.price * tf_prod_qty) - (bir.bir_price * tf_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('to_branch')
                ->first();
            $tf_in = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('to_branch','=',$request->branch)->where('from_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('to_branch, SUM(bri.cost * tf_prod_qty) as total_tf_in_cost, SUM(bri.price * tf_prod_qty) as total_tf_in_srp'))
                ->groupBy('to_branch')
                ->first();
            $sales = SoHeader::where(['so_headers.branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'sod.sod_prod_code');
                    $join->on('bir.bir_branch_code','=','so_headers.branch_code')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = so_date');
                })
                ->select(DB::raw('so_headers.branch_code, SUM(bri.cost * sod_prod_qty) as total_so_cost, 
                SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_so_srp,
                SUM(IF((bri.cost * sod_prod_qty) > (bir.bir_cost * sod_prod_qty),(bri.cost * sod_prod_qty) - (bir.bir_cost * sod_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bri.price * sod_prod_qty) > (bir.bir_price * sod_prod_qty),(bri.price * sod_prod_qty) - (bir.bir_price * sod_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bir.bir_cost * sod_prod_qty) > (bri.cost * sod_prod_qty),(bir.bir_cost * sod_prod_qty) - (bri.cost * sod_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bir.bir_price * sod_prod_qty) > (bri.price * sod_prod_qty),(bir.bir_price * sod_prod_qty) - (bri.price * sod_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('branch_code')
                ->first();
            $tf_out = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','=',$request->branch)->where('to_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bir.bir_branch_code','=','from_branch')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = tf_date');
                })
                ->select(DB::raw('from_branch, SUM(bri.cost * tf_prod_qty) as total_tf_out_cost, SUM(bri.price * tf_prod_qty) as total_tf_out_srp,
                SUM(IF((bri.cost * tf_prod_qty) > (bir.bir_cost * tf_prod_qty),(bri.cost * tf_prod_qty) - (bir.bir_cost * tf_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bri.price * tf_prod_qty) > (bir.bir_price * tf_prod_qty),(bri.price * tf_prod_qty) - (bir.bir_price * tf_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bir.bir_cost * tf_prod_qty) > (bri.cost * tf_prod_qty),(bir.bir_cost * tf_prod_qty) - (bri.cost * tf_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bir.bir_price * tf_prod_qty) > (bri.price * tf_prod_qty),(bir.bir_price * tf_prod_qty) - (bri.price * tf_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('from_branch')
                ->first();
            $return = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','=',$request->branch)->where('to_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('from_branch, SUM(bri.cost * tf_prod_qty) as total_return_cost, SUM(bri.price * tf_prod_qty) as total_return_srp'))
                ->groupBy('from_branch')
                ->first();
            $branch = $request->branch;
            $sales_return = SrHeader::whereBetween('sr_date', [$from, $to])
                ->join('so_headers as soh', function ($join) use($branch) {
                    $join->on('soh.so_code', '=', 'sr_headers.so_code')
                        ->where('soh.branch_code','=',$branch);
                })
                ->join('sr_details as srd','srd.srd_code','=','sr_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'srd.srd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                ->select(DB::raw('soh.branch_code, SUM(bri.cost * srd.srd_prod_qty) as total_so_return_cost, 
                SUM(srd_prod_price * srd_prod_qty - ((srd.srd_prod_price * srd_prod_qty) * (srd_less/100))) as total_so_return_srp'))
                ->groupBy('branch_code')
                ->first();
            $sales_disc = SoHeader::where(['so_headers.branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.price * sod_prod_qty) as total_sd_srp,
                (SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) / sum(bri.price * sod_prod_qty) * 100)  as total_so_disc_cost, 
                SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) as total_so_disc_srp,
                (SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) / sum(bri.price * sod_prod_qty) * 100) as pos_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) as pos_disc,
                (SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) / sum(bri.price * sod_prod_qty) * 100) as neg_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) as neg_disc'))
                ->first();
            $misc_in = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => $request->branch])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'msd.msd_prod_code');
                    $join->on('bir.bir_branch_code','=','msh_branch_code')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = msh_date');
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_in_cost, SUM(bri.price * msd.msd_prod_qty) as misc_in_srp,
                SUM(IF((bir.bir_cost * msd_prod_qty) > (bri.cost * msd_prod_qty),(bir.bir_cost * msd_prod_qty) - (bri.cost * msd_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bir.bir_price * msd_prod_qty) > (bri.price * msd_prod_qty),(bir.bir_price * msd_prod_qty) - (bri.price * msd_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bri.cost * msd_prod_qty) > (bir.bir_cost * msd_prod_qty),(bri.cost * msd_prod_qty) - (bir.bir_cost * msd_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bri.price * msd_prod_qty) > (bir.bir_price * msd_prod_qty),(bri.price * msd_prod_qty) - (bir.bir_price * msd_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('msh_branch_code')
                ->first();
            $misc_out = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => $request->branch])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'OUT']);
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'msd.msd_prod_code');
                    $join->on('bir.bir_branch_code','=','msh_branch_code')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = msh_date');
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_out_cost, SUM(bri.price * msd.msd_prod_qty) as misc_out_srp,
                SUM(IF((bri.cost * msd_prod_qty) > (bir.bir_cost * msd_prod_qty),(bri.cost * msd_prod_qty) - (bir.bir_cost * msd_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bri.price * msd_prod_qty) > (bir.bir_price * msd_prod_qty),(bri.price * msd_prod_qty) - (bir.bir_price * msd_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bir.bir_cost * msd_prod_qty) > (bri.cost * msd_prod_qty),(bir.bir_cost * msd_prod_qty) - (bri.cost * msd_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bir.bir_price * msd_prod_qty) > (bri.price * msd_prod_qty),(bir.bir_price * msd_prod_qty) - (bri.price * msd_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('msh_branch_code')
                ->first();
            $price_adj_1 = Activity::whereBetween(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"), [$from, $to])
                ->where('text','like','%Updated Product%')
                ->join('bi_replicates as bir', function ($join) use ($request){
                    $join->on('bir.bir_prod_code','=',DB::raw("SUBSTR(text,17)")) // i want to join the users table with either of these columns
                    //$join->orOn('bir_prod_code','=','inv.code')
                    ->where(['bir.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(bir.created_at,'%Y-%m-%d'))"));
                })
                ->join('bi_replicates as bir2', function ($join) use ($request){
                    $join->on('bir2.bir_prod_code','=',DB::raw("SUBSTR(text,17)")) // i want to join the users table with either of these columns
                    //$join->orOn('bir_prod_code','=','inv.code')
                    ->where(['bir2.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(bir2.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(activity_log.created_at - INTERVAL 1 DAY,'%Y-%m-%d'))"));
                })
                ->select(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"), DB::raw('(SUM((bir.bir_cost - bir2.bir_cost) * bir2.bir_quantity) / count(text)) as adj_cost1,
                (SUM(IF(bir.bir_cost > bir2.bir_cost,(bir.bir_cost - bir2.bir_cost) * bir2.bir_quantity,0))/ count(text)) as pos_adj_cost,
                (SUM(IF(bir2.bir_cost > bir.bir_cost,(bir2.bir_cost - bir.bir_cost) * bir2.bir_quantity,0))/ count(text)) as neg_adj_cost,
                (SUM(IF(bir.bir_price > bir2.bir_price,(bir.bir_price - bir2.bir_price) * bir2.bir_quantity,0))/ count(text)) as pos_adj_srp,
                (SUM(IF(bir2.bir_price > bir.bir_price,(bir2.bir_price - bir.bir_price) * bir2.bir_quantity,0))/ count(text)) as neg_adj_srp'))
                ->groupBy('text',DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"))
                ->get();
            $price_adj_2 = Activity::whereBetween(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"), [$from, $to])
                ->where('text','like','%Updated Product%')
                ->join('inventories as inv', function ($join){
                    $join->on('activity_log.text','LIKE',DB::raw("CONCAT('%', inv.name , '%')"));
                })->select('activity_log.created_at','code')->get();
            $arr = array();
            $dt = array();
            foreach ($price_adj_2 as $key)
            {
                $arr[] = $key->code;
                $dt[] = Carbon::createFromFormat('Y-m-d H:i:s', $key->created_at)->format('Y-m-d');
            }
            $dd = DB::table('bi_replicates as bir')->whereIn('bir.bir_prod_code',$arr)
                ->where(['bir.bir_branch_code'=>$request->branch])
                ->whereIn(DB::raw("(DATE_FORMAT(bir.created_at,'%Y-%m-%d'))"),$dt)
                ->join('bi_replicates as bir2', function ($join) use ($request){
                    //$join->on('activity_log.text','like',DB::raw("CONCAT('%', bir2.bir_prod_code , '%')")); // i want to join the users table with either of these columns
                    $join->on('bir2.bir_prod_code','=','bir.bir_prod_code')
                        ->where(['bir2.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(bir2.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(bir.created_at - INTERVAL 1 DAY,'%Y-%m-%d'))"));
                })->select(DB::raw("(DATE_FORMAT(bir.created_at,'%Y-%m-%d'))"),DB::raw('bir.bir_prod_code, (SUM((bir.bir_cost - bir2.bir_cost) * bir2.bir_quantity) / count(bir.bir_prod_code)) as adj_cost2,
                (SUM(IF(bir.bir_cost > bir2.bir_cost,(bir.bir_cost - bir2.bir_cost) * bir2.bir_quantity,0))/ count(bir.bir_prod_code)) as pos_adj_cost,
                (SUM(IF(bir2.bir_cost > bir.bir_cost,(bir2.bir_cost - bir.bir_cost) * bir2.bir_quantity,0))/ count(bir.bir_prod_code)) as neg_adj_cost,
                (SUM(IF(bir.bir_price > bir2.bir_price,(bir.bir_price - bir2.bir_price) * bir2.bir_quantity,0))/ count(bir.bir_prod_code)) as pos_adj_srp,
                (SUM(IF(bir2.bir_price > bir.bir_price,(bir2.bir_price - bir.bir_price) * bir2.bir_quantity,0))/ count(bir.bir_prod_code)) as neg_adj_srp'))
                ->groupBy('bir_prod_code',DB::raw("(DATE_FORMAT(bir.created_at,'%Y-%m-%d'))"))
                ->get();


            $end_inv = InvPosition::where(['ip_date' => $to])->where(['ip_branch_code' => $request->branch])
                //->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();
            $pos_adj_cost = $price_adj_1->sum('pos_adj_cost') + $dd->sum('pos_adj_cost');
            $neg_adj_cost = $price_adj_1->sum('neg_adj_cost') + $dd->sum('neg_adj_cost');
            $pos_adj_srp = $price_adj_1->sum('pos_adj_srp') + $dd->sum('pos_adj_srp');
            $neg_adj_srp = $price_adj_1->sum('neg_adj_srp') + $dd->sum('neg_adj_srp');

        }

        $branches = Branch::where(['status'=>'AC'])->get(                                                                                      );
        return view($this->position().'.erp_report.erp',compact('branches','inv_beg','cw','request','tf_in','sales','tf_out','sales_disc','return','misc_in','misc_out','end_inv','sales_return','price_adj_1','price_adj_2',
            'pos_adj_cost','neg_adj_cost','pos_adj_srp','neg_adj_srp'));
    }
    function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == 'all'){
            //ALL BRANCH
            $last_day = date('Y-m-d',strtotime($from .' - 1 day'));
            $inv_beg = InvPosition::where(['ip_date' => $last_day])
                ->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();
            $cw = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('from_branch,SUM(bri.cost * tf_prod_qty) as total_cw_cost, SUM(bri.price * tf_prod_qty) as total_cw_srp'))
                ->groupBy('from_branch')
                ->first();
            $tf_in = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','!=','TR-BR00001')->where('to_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('SUM(bri.cost * tf_prod_qty) as total_tf_in_cost, SUM(bri.price * tf_prod_qty) as total_tf_in_srp'))
                //->groupBy('from_branch')
                ->first();
            $sales = SoHeader::whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.cost * sod_prod_qty) as total_so_cost, 
                SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_so_srp'))
                //->groupBy('branch_code')
                ->first();
            $tf_out = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','!=','TR-BR00001')->where('to_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('SUM(bri.cost * tf_prod_qty) as total_tf_out_cost, SUM(bri.price * tf_prod_qty) as total_tf_out_srp'))
                //->groupBy('from_branch')
                ->first();
            $return = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','!=','TR-BR00001')->where('to_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('SUM(bri.cost * tf_prod_qty) as total_return_cost, SUM(bri.price * tf_prod_qty) as total_return_srp'))
                //->groupBy('from_branch')
                ->first();
            $sales_return = SrHeader::whereBetween('sr_date', [$from, $to])
                ->join('so_headers as soh','soh.so_code','=','sr_headers.so_code')
                ->join('sr_details as srd','srd.srd_code','=','sr_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'srd.srd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                ->select(DB::raw('soh.branch_code, SUM(bri.cost * srd.srd_prod_qty) as total_so_return_cost, 
                SUM(srd_prod_price * srd_prod_qty - ((srd.srd_prod_price * srd_prod_qty) * (srd_less/100))) as total_so_return_srp'))
                ->groupBy('branch_code')
                ->first();
            $sales_disc = SoHeader::whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.price * sod_prod_qty) as total_sd_srp,
                (SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) / sum(bri.price * sod_prod_qty) * 100)  as total_so_disc_cost, 
                SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) as total_so_disc_srp,
                (SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) / sum(bri.price * sod_prod_qty) * 100) as pos_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) as pos_disc,
                (SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) / sum(bri.price * sod_prod_qty) * 100) as neg_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) as neg_disc'))
                //->groupBy('branch_code')
                ->first();
            //echo $tf_out;
            $misc_in = MiscHeader::whereBetween('msh_date',[$from, $to])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_in_cost, SUM(bri.price * msd.msd_prod_qty) as misc_in_srp'))
                ->first();
            $misc_out = MiscHeader::whereBetween('msh_date',[$from, $to])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'OUT']);
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_out_cost, SUM(bri.price * msd.msd_prod_qty) as misc_out_srp'))
                ->first();
            $end_inv = InvPosition::where(['ip_date' => $to])
                ->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();

            $branch = 'ALL BRANCH';
        }elseif($request->optCustType == 'branch'){
            //Branch
            $last_day = date('Y-m-d',strtotime($from .' - 1 day'));
            $inv_beg = InvPosition::where(['ip_date' => $last_day])->where(['ip_branch_code' => $request->branch])
                //->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();
            $cw = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('to_branch','=',$request->branch)->where('from_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bir.bir_branch_code','=','from_branch')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = tf_date');
                })
                ->select(DB::raw('to_branch, SUM(bri.cost * tf_prod_qty) as total_cw_cost, SUM(bri.price * tf_prod_qty) as total_cw_srp,
                SUM(IF((bir.bir_cost * tf_prod_qty) > (bri.cost * tf_prod_qty),(bir.bir_cost * tf_prod_qty) - (bri.cost * tf_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bir.bir_price * tf_prod_qty) > (bri.price * tf_prod_qty),(bir.bir_price * tf_prod_qty) - (bri.price * tf_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bri.cost * tf_prod_qty) > (bir.bir_cost * tf_prod_qty),(bri.cost * tf_prod_qty) - (bir.bir_cost * tf_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bri.price * tf_prod_qty) > (bir.bir_price * tf_prod_qty),(bri.price * tf_prod_qty) - (bir.bir_price * tf_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('to_branch')
                ->first();
            $tf_in = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('to_branch','=',$request->branch)->where('from_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('to_branch, SUM(bri.cost * tf_prod_qty) as total_tf_in_cost, SUM(bri.price * tf_prod_qty) as total_tf_in_srp'))
                ->groupBy('to_branch')
                ->first();
            $sales = SoHeader::where(['so_headers.branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'sod.sod_prod_code');
                    $join->on('bir.bir_branch_code','=','so_headers.branch_code')
                    ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = so_date');
                })
                ->select(DB::raw('so_headers.branch_code, SUM(bri.cost * sod_prod_qty) as total_so_cost, 
                SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_so_srp,
                SUM(IF((bri.cost * sod_prod_qty) > (bir.bir_cost * sod_prod_qty),(bri.cost * sod_prod_qty) - (bir.bir_cost * sod_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bri.price * sod_prod_qty) > (bir.bir_price * sod_prod_qty),(bri.price * sod_prod_qty) - (bir.bir_price * sod_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bir.bir_cost * sod_prod_qty) > (bri.cost * sod_prod_qty),(bir.bir_cost * sod_prod_qty) - (bri.cost * sod_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bir.bir_price * sod_prod_qty) > (bri.price * sod_prod_qty),(bir.bir_price * sod_prod_qty) - (bri.price * sod_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('branch_code')
                ->first();
            $tf_out = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','=',$request->branch)->where('to_branch','!=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bir.bir_branch_code','=','from_branch')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = tf_date');
                })
                ->select(DB::raw('from_branch, SUM(bri.cost * tf_prod_qty) as total_tf_out_cost, SUM(bri.price * tf_prod_qty) as total_tf_out_srp,
                SUM(IF((bri.cost * tf_prod_qty) > (bir.bir_cost * tf_prod_qty),(bri.cost * tf_prod_qty) - (bir.bir_cost * tf_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bri.price * tf_prod_qty) > (bir.bir_price * tf_prod_qty),(bri.price * tf_prod_qty) - (bir.bir_price * tf_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bir.bir_cost * tf_prod_qty) > (bri.cost * tf_prod_qty),(bir.bir_cost * tf_prod_qty) - (bri.cost * tf_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bir.bir_price * tf_prod_qty) > (bri.price * tf_prod_qty),(bir.bir_price * tf_prod_qty) - (bri.price * tf_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('from_branch')
                ->first();
            $return = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch','=',$request->branch)->where('to_branch','=','TR-BR00001')
                ->join('transfer_details as tf','tf.td_code','=','tf_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->select(DB::raw('from_branch, SUM(bri.cost * tf_prod_qty) as total_return_cost, SUM(bri.price * tf_prod_qty) as total_return_srp'))
                ->groupBy('from_branch')
                ->first();
            $branch = $request->branch;
            $sales_return = SrHeader::whereBetween('sr_date', [$from, $to])
                ->join('so_headers as soh', function ($join) use($branch, $from, $to) {
                    $join->on('soh.so_code', '=', 'sr_headers.so_code')
                        ->where('soh.branch_code','=',$branch)
                        ->whereNotBetween('so_date' ,[$from, $to]);
                })
                ->join('sr_details as srd','srd.srd_code','=','sr_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'srd.srd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                /*->select(DB::raw('soh.branch_code, SUM(bri.cost * srd.srd_prod_qty) as total_so_return_cost,
                SUM(srd_prod_price * srd_prod_qty - ((srd.srd_prod_price * srd_prod_qty) * (srd_less/100))) as total_so_return_srp'))*/
                ->select(DB::raw('soh.branch_code, SUM(bri.cost * srd.srd_prod_qty) as total_so_return_cost, 
                SUM(bri.price * srd.srd_prod_qty) as total_so_return_srp'))
                ->groupBy('branch_code')
                ->first();
            $sales_return_neg = SoHeader::where(['so_headers.branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])
                ->join('sr_headers as srh', function ($join) use($branch, $from, $to) {
                    $join->on('so_code', '=', 'sr_headers.so_code')
                        ->where('so_headers.branch_code','=',$branch)
                        ->whereNotBetween('so_date' ,[$from, $to]);
                })
                ->join('sr_details as srd','srd.srd_code','=','sr_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'srd.srd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                ->select(DB::raw('soh.branch_code, SUM(bri.cost * srd.srd_prod_qty) as total_so_return_neg_cost, 
                SUM(bri.price * srd.srd_prod_qty) as total_so_return_neg_srp'))
                ->groupBy('branch_code')
                ->first();
            $sales_disc = SoHeader::where(['so_headers.branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])
                ->join('so_details as sod','sod.sod_code','=','so_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','so_headers.branch_code');
                })
                ->select(DB::raw('SUM(bri.price * sod_prod_qty) as total_sd_srp,
                (SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) / sum(bri.price * sod_prod_qty) * 100)  as total_so_disc_cost, 
                SUM((bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100)))) as total_so_disc_srp,
                (SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) / sum(bri.price * sod_prod_qty) * 100) as pos_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) > (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(bri.price * sod_prod_qty) - (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),0)) as pos_disc,
                (SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) / sum(bri.price * sod_prod_qty) * 100) as neg_disc_perc,
                SUM(IF((bri.price * sod_prod_qty) < (sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))),(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) - (bri.price * sod_prod_qty),0)) as neg_disc'))
                ->first();
            $misc_in = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => $request->branch])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'msd.msd_prod_code');
                    $join->on('bir.bir_branch_code','=','msh_branch_code')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = msh_date');
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_in_cost, SUM(bri.price * msd.msd_prod_qty) as misc_in_srp,
                SUM(IF((bir.bir_cost * msd_prod_qty) > (bri.cost * msd_prod_qty),(bir.bir_cost * msd_prod_qty) - (bri.cost * msd_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bir.bir_price * msd_prod_qty) > (bri.price * msd_prod_qty),(bir.bir_price * msd_prod_qty) - (bri.price * msd_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bri.cost * msd_prod_qty) > (bir.bir_cost * msd_prod_qty),(bri.cost * msd_prod_qty) - (bir.bir_cost * msd_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bri.price * msd_prod_qty) > (bir.bir_price * msd_prod_qty),(bri.price * msd_prod_qty) - (bir.bir_price * msd_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('msh_branch_code')
                ->first();
            $misc_out = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => $request->branch])
                ->join('misc_details as msd','msd.msd_code','=','msh_code')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'msd.msd_prod_code');
                    //$join->on('bri.branch_code','=','to_branch');
                    $join->on('bri.branch_code','=','msh_branch_code')
                        ->where(['msd_remarks' => 'OUT']);
                })
                ->join('bi_replicates as bir', function ($join) {
                    $join->on('bir.bir_prod_code', '=', 'msd.msd_prod_code');
                    $join->on('bir.bir_branch_code','=','msh_branch_code')
                        ->whereRaw('DATE_FORMAT(bir.created_at,"%Y-%m-%d") = msh_date');
                })
                ->select(DB::raw('SUM(bri.cost * msd.msd_prod_qty) as misc_out_cost, SUM(bri.price * msd.msd_prod_qty) as misc_out_srp,
                SUM(IF((bri.cost * msd_prod_qty) > (bir.bir_cost * msd_prod_qty),(bri.cost * msd_prod_qty) - (bir.bir_cost * msd_prod_qty),0)) as total_pos_prch_cost,
                SUM(IF((bri.price * msd_prod_qty) > (bir.bir_price * msd_prod_qty),(bri.price * msd_prod_qty) - (bir.bir_price * msd_prod_qty),0)) as total_pos_prch_srp,
                SUM(IF((bir.bir_cost * msd_prod_qty) > (bri.cost * msd_prod_qty),(bir.bir_cost * msd_prod_qty) - (bri.cost * msd_prod_qty),0)) as total_neg_prch_cost,
                SUM(IF((bir.bir_price * msd_prod_qty) > (bri.price * msd_prod_qty),(bir.bir_price * msd_prod_qty) - (bri.price * msd_prod_qty),0)) as total_neg_prch_srp'))
                ->groupBy('msh_branch_code')
                ->first();
            $end_inv = InvPosition::where(['ip_date' => $to])->where(['ip_branch_code' => $request->branch])
                //->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
                ->first();
            $price_adj_1 = Activity::whereBetween(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"), [$from, $to])
                ->where('text','like','%Updated Product%')
                ->join('bi_replicates as bir', function ($join) use ($request){
                    $join->on('activity_log.text','like',DB::raw("CONCAT('%', bir.bir_prod_code , '%')")) // i want to join the users table with either of these columns
                    //$join->orOn('bir_prod_code','=','inv.code')
                    ->where(['bir.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(bir.created_at,'%Y-%m-%d'))"));
                })
                ->join('bi_replicates as bir2', function ($join) use ($request){
                    $join->on('activity_log.text','like',DB::raw("CONCAT('%', bir2.bir_prod_code , '%')")) // i want to join the users table with either of these columns
                    //$join->orOn('bir_prod_code','=','inv.code')
                    ->where(['bir2.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(bir2.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(activity_log.created_at - INTERVAL 1 DAY,'%Y-%m-%d'))"));
                })
                ->select(DB::raw('(SUM((bir.bir_cost - bir2.bir_cost) * bir.bir_quantity) / count(text)) as adj_cost2,
                (SUM(IF(bir.bir_cost > bir2.bir_cost,(bir.bir_cost - bir2.bir_cost) * bir.bir_quantity,0))/ count(text)) as pos_adj_cost2,
                (SUM(IF(bir2.bir_cost > bir.bir_cost,(bir2.bir_cost - bir.bir_cost) * bir.bir_quantity,0))/ count(text)) as neg_adj_cost2,
                (SUM(IF(bir.bir_price > bir2.bir_price,(bir.bir_price - bir2.bir_price) * bir.bir_quantity,0))/ count(text)) as pos_adj_srp2,
                (SUM(IF(bir2.bir_price > bir.bir_price,(bir2.bir_price - bir.bir_price) * bir.bir_quantity,0))/ count(text)) as neg_adj_srp2'))
                ->groupBy('text')
                ->get();
            $price_adj_2 = Activity::whereBetween(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"), [$from, $to])
                ->where('text','like','%Updated Product%')
                ->join('inventories as inv', function ($join){
                    $join->on('activity_log.text','like',DB::raw("CONCAT('%', name , '%')"));
                })//DB::raw('(Select name from inventories where name like CONCAT("%", activity_log.text, "%"))')*/
                ->join('bi_replicates as bir', function ($join) use ($request){
                    //$join->on('activity_log.text','like',DB::raw("CONCAT('%', bir.bir_prod_code , '%')")); // i want to join the users table with either of these columns
                    $join->on('bir.bir_prod_code','=','code')
                        ->where(['bir.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(activity_log.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(bir.created_at,'%Y-%m-%d'))"));
                })
                ->join('bi_replicates as bir2', function ($join) use ($request){
                    //$join->on('activity_log.text','like',DB::raw("CONCAT('%', bir2.bir_prod_code , '%')")); // i want to join the users table with either of these columns
                    $join->on('bir2.bir_prod_code','=','code')
                        ->where(['bir2.bir_branch_code' => $request->branch])
                        ->where(DB::raw("(DATE_FORMAT(bir2.created_at,'%Y-%m-%d'))"),'=',DB::raw("(DATE_FORMAT(activity_log.created_at - INTERVAL 1 DAY,'%Y-%m-%d'))"));
                })
                ->select(DB::raw('(SUM((bir.bir_cost - bir2.bir_cost) * bir.bir_quantity) / count(text)) as adj_cost2,
                (SUM(IF(bir.bir_cost > bir2.bir_cost,(bir.bir_cost - bir2.bir_cost) * bir.bir_quantity,0))/ count(text)) as pos_adj_cost2,
                (SUM(IF(bir2.bir_cost > bir.bir_cost,(bir2.bir_cost - bir.bir_cost) * bir.bir_quantity,0))/ count(text)) as neg_adj_cost2,
                (SUM(IF(bir.bir_price > bir2.bir_price,(bir.bir_price - bir2.bir_price) * bir.bir_quantity,0))/ count(text)) as pos_adj_srp2,
                (SUM(IF(bir2.bir_price > bir.bir_price,(bir2.bir_price - bir.bir_price) * bir.bir_quantity,0))/ count(text)) as neg_adj_srp2'))
                ->groupBy('text')
                ->get();
            $branch = Branch::where(['code' => $request->branch])->first()->name;
        }
        $data = array([
            $branch => 'Inv Beg',
            'COST' => @$inv_beg->ip_cost,
            'SRP' => @$inv_beg->ip_srp
        ],[
            $branch => 'CW Deliveries',
            'COST' => @$cw->total_cw_cost,
            'SRP' => @$cw->total_cw_srp
        ],[
            $branch => 'Transfer In',
            'COST' => @$tf_in->total_tf_in_cost,
            'SRP' => @$tf_in->total_tf_in_srp
        ],[
            $branch => 'Sales',
            'COST' => @$sales->total_so_cost,
            'SRP' => @$sales->total_so_srp
        ],[
            $branch => 'Positive Sales Return',
            'COST' => @$sales_return->total_so_return_cost,
            'SRP' => @$sales_return->total_so_return_srp
        ],[
            $branch => 'Negative Sales Return',
            'COST' => @$sales_return_neg->total_so_return_neg_cost,
            'SRP' => @$sales_return_neg->total_so_return_neg_srp
        ],[
            $branch => 'Transfer out',
            'COST' => @$tf_out->total_tf_out_cost,
            'SRP' => @$tf_out->total_tf_out_srp
        ],[
            $branch => 'Stock Returns',
            'COST' => @$return->total_return_cost,
            'SRP' => @$return->total_return_srp
        ]/*,[
            $branch => 'Discount',
            'COST' => Number_Format(@$sales_disc->total_so_disc_cost,2)."%",
            'SRP' => @$sales_disc->total_so_disc_srp
        ]*/,[
            $branch => 'Positive Discount',
            'COST' => Number_Format(@$sales_disc->pos_disc_perc,2)."%",
            'SRP' => @$sales_disc->pos_disc
        ],[
            $branch => 'Negative Discount',
            'COST' => Number_Format(@$sales_disc->neg_disc_perc,2)."%",
            'SRP' => @$sales_disc->neg_disc
        ],[
            $branch => 'Miscellaneous IN',
            'COST' => @$misc_in->misc_in_cost,
            'SRP' => @$misc_in->misc_in_srp
        ],[
            $branch => 'Miscellaneous OUT',
            'COST' => @$misc_out->misc_out_cost,
            'SRP' => @$misc_out->misc_out_srp
        ]
        ,[
            $branch => 'Positive Cost and Srp Adjustment',
                'COST' => @$price_adj_1->pos_adj_cost1 + @$price_adj_2->pos_adj_cost2,
                'SRP' => @$price_adj_1->pos_adj_srp1 + @$price_adj_2->pos_adj_srp2 + @$sales->total_pos_prch_srp
            ],[$branch => 'Negative Cost and Srp Adjustment',
            'COST' => @$price_adj_1->neg_adj_cost1 + @$price_adj_2->neg_adj_cost2,
            'SRP' => @$price_adj_1->neg_adj_srp1 + @$price_adj_2->neg_adj_srp2
            ],[
            $branch => 'End Inv',
            'COST' => @$end_inv->ip_cost,
            'SRP' => @$end_inv->ip_srp
        ]);

        return Excel::create('Taurus Enterprise Block Box Report', function($excel) use ($data) {
            $excel->setTitle('Taurus Enterprise Block Box Report');
            $excel->sheet('Enterprise Block Box Report', function($sheet) use ($data)
            {
                $sheet->setColumnFormat(array(
                    'B' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'C' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Enterprise Block Box Report"]);
                $sheet->mergeCells("A1:C1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
                $sheet->cell('A2:C2', function($cell) {
                    // change header color
                    $cell->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(12);;
                });
                /*$footerRow = count($data) + 3;
                $sheet->appendRow("$footerRow",[
                    'Total Amount: '.Number_Format($total,2)
                ]);
                $sheet->mergeCells("A{$footerRow}:I{$footerRow}");
                $sheet->cell("A{$footerRow}", function($cell) {
                    $cell->setBackground('#3ed1f2')
                        ->setAlignment('right')
                        ->setValignment('right')
                        ->setFontWeight('bold')
                        //->setFontColor('#666666')
                        ->setFontSize(11);
                });*/
            });
        })->download('xlsx');
    }
}