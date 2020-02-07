<?php

namespace App\Http\Controllers\New_Reports;

use App\InvPosition;
use App\MiscHeader;
use App\PoHeader;
use App\ReceivingHeader;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class CWERP_Controller extends Controller
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
        return view($this->position().'.new_reports.cw_erp');
    }
    function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $last_day = date('Y-m-d',strtotime($from .' - 1 day'));
        $inv_beg = InvPosition::where(['ip_branch_code' => 'TR-BR00001','ip_date' => $last_day])
            ->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
            ->first();
        $po = PoHeader::where(function($query)  {
            $query->where('status','=','AP')->orWhere('status','=','CL');
        })->whereBetween('po_date',[$from, $to])
            ->join('po_details as pod', 'pod.pod_code', '=', 'po_code')
            ->select(DB::raw('SUM(prod_price * prod_qty -(prod_price * prod_qty) *(prod_less / 100)) as po_cost, 
            SUM(prod_srp * prod_qty) as po_srp'))
            ->first();
        $rec = ReceivingHeader::whereBetween('rh_date',[$from, $to])
            ->join('receiving_details as rd', 'rd.rd_code', '=', 'rh_no')
            ->join('po_headers as poh','poh.po_code','=','rh_po_no')
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'poh.po_code');
                $join->on('pod.prod_code', '=', 'rd.rd_prod_code');
            })
            ->select(DB::raw('SUM(pod.prod_price * rd_prod_qty -(pod.prod_price * rd_prod_qty) *(prod_less / 100)) as rec_cost, SUM(pod.prod_srp * rd.rd_prod_qty) as rec_srp'))
            ->first();
        $po_disc = ReceivingHeader::whereBetween('rh_date',[$from, $to])
            ->join('receiving_details as rd', 'rd.rd_code', '=', 'rh_no')
            ->join('po_headers as poh','poh.po_code','=','rh_po_no')
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'poh.po_code');
                $join->on('pod.prod_code', '=', 'rd.rd_prod_code');
            })
            ->select(DB::raw('SUM(pod.prod_price * prod_qty - (pod.prod_price * prod_qty -(pod.prod_price * prod_qty) *(prod_less / 100))) as po_disc_cost, SUM(pod.prod_srp * rd.rd_prod_qty) as po_disc_srp,
            SUM(pod.prod_price * prod_qty) as pur_inc'))
            ->first();
        $tf_in = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
            ->where('to_branch','=','TR-BR00001')
            ->join('transfer_details as tf','tf.td_code','=','tf_code')
            ->select(DB::raw('to_branch, SUM(tf_prod_price * tf_prod_qty) as total_tf_in_cost, SUM(tf_prod_srp * tf_prod_qty) as total_tf_in_srp'))
            ->groupBy('to_branch')
            ->first();
        $cw = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
            ->where('from_branch','=','TR-BR00001')
            ->join('transfer_details as tf','tf.td_code','=','tf_code')
            ->select(DB::raw('SUM(tf_prod_price * tf_prod_qty) as total_cw_cost, SUM(tf_prod_srp * tf_prod_qty) as total_cw_srp'))
            ->first();
        $misc_in = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => 'TR-BR00001'])
            ->join('misc_details as msd', function ($join) use($from) {
                $join->on('msd.msd_code','=','msh_code')
                    ->where(['msd_remarks' => 'IN']);
            })
            ->select(DB::raw('SUM(msd_prod_cost* msd.msd_prod_qty) as misc_in_cost, SUM(msd_prod_price * msd.msd_prod_qty) as misc_in_srp'))
            ->groupBy('msh_branch_code')
            ->first();
        $misc_out = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => 'TR-BR00001'])
            ->join('misc_details as msd', function ($join) use($from) {
                $join->on('msd.msd_code','=','msh_code')
                    ->where(['msd_remarks' => 'OUT']);
            })
            ->select(DB::raw('SUM(msd_prod_cost* msd.msd_prod_qty) as misc_out_cost, SUM(msd_prod_price * msd.msd_prod_qty) as misc_out_srp'))
            ->groupBy('msh_branch_code')
            ->first();
        $end_inv = InvPosition::where(['ip_date' => $to])->where(['ip_branch_code' => 'TR-BR00001'])
            //->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
            ->first();
        $unrec = ReceivingHeader::whereBetween('rh_date',[$from, $to])
            ->join('receiving_details as rd', 'rd.rd_code', '=', 'rh_no')
            ->join('po_headers as poh', function ($join) use($from, $to) {
                $join->on('poh.po_code', '=', 'rh_po_no')
                ->whereBetween('po_date',[$from, $to]);
            })
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'poh.po_code');
                $join->on('pod.prod_code', '=', 'rd.rd_prod_code');
            })
            ->select(DB::raw('(SUM((pod.prod_price * pod.prod_qty -(pod.prod_price * pod.prod_qty) *(pod.prod_less / 100))) - SUM((pod.prod_price * rd_prod_qty -(pod.prod_price * rd_prod_qty) *(pod.prod_less / 100)))) as unrec_cost, 
            SUM((pod.prod_srp * prod_qty) - (pod.prod_srp * rd_prod_qty)) as unrec_srp'))
            ->first();

        return view($this->position().'.new_reports.cw_erp',compact('request','inv_beg', 'po', 'rec','tf_in','cw','misc_in','misc_out','end_inv','po_disc','unrec'));
    }
    function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $last_day = date('Y-m-d',strtotime($from .' - 1 day'));
        $inv_beg = InvPosition::where(['ip_branch_code' => 'TR-BR00001','ip_date' => $last_day])
            ->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
            ->first();
        $po = PoHeader::where(function($query)  {
            $query->where('status','=','AP')->orWhere('status','=','CL');
        })->whereBetween('po_date',[$from, $to])
            ->join('po_details as pod', 'pod.pod_code', '=', 'po_code')
            ->select(DB::raw('SUM(prod_price * prod_qty -(prod_price * prod_qty) *(prod_less / 100)) as po_cost, 
            SUM(prod_srp * prod_qty) as po_srp'))
            ->first();
        $rec = ReceivingHeader::whereBetween('rh_date',[$from, $to])
            ->join('receiving_details as rd', 'rd.rd_code', '=', 'rh_no')
            ->join('po_headers as poh','poh.po_code','=','rh_po_no')
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'poh.po_code');
                $join->on('pod.prod_code', '=', 'rd.rd_prod_code');
            })
            ->select(DB::raw('SUM(pod.prod_price * rd_prod_qty -(pod.prod_price * rd_prod_qty) *(prod_less / 100)) as rec_cost, SUM(pod.prod_srp * rd.rd_prod_qty) as rec_srp'))
            ->first();
        $po_disc = ReceivingHeader::whereBetween('rh_date',[$from, $to])
            ->join('receiving_details as rd', 'rd.rd_code', '=', 'rh_no')
            ->join('po_headers as poh','poh.po_code','=','rh_po_no')
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'poh.po_code');
                $join->on('pod.prod_code', '=', 'rd.rd_prod_code');
            })
            ->select(DB::raw('SUM(pod.prod_price * prod_qty - (pod.prod_price * prod_qty -(pod.prod_price * prod_qty) *(prod_less / 100))) as po_disc_cost, SUM(pod.prod_srp * rd.rd_prod_qty) as po_disc_srp,
            SUM(pod.prod_price * prod_qty) as pur_inc'))
            ->first();
        $tf_in = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
            ->where('to_branch','=','TR-BR00001')
            ->join('transfer_details as tf','tf.td_code','=','tf_code')
            ->select(DB::raw('to_branch, SUM(tf_prod_price * tf_prod_qty) as total_tf_in_cost, SUM(tf_prod_srp * tf_prod_qty) as total_tf_in_srp'))
            ->groupBy('to_branch')
            ->first();
        $cw = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
            ->where('from_branch','=','TR-BR00001')
            ->join('transfer_details as tf','tf.td_code','=','tf_code')
            ->select(DB::raw('SUM(tf_prod_price * tf_prod_qty) as total_cw_cost, SUM(tf_prod_srp * tf_prod_qty) as total_cw_srp'))
            ->first();
        $misc_in = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => 'TR-BR00001'])
            ->join('misc_details as msd', function ($join) use($from) {
                $join->on('msd.msd_code','=','msh_code')
                    ->where(['msd_remarks' => 'IN']);
            })
            ->select(DB::raw('SUM(msd_prod_cost* msd.msd_prod_qty) as misc_in_cost, SUM(msd_prod_price * msd.msd_prod_qty) as misc_in_srp'))
            ->groupBy('msh_branch_code')
            ->first();
        $misc_out = MiscHeader::whereBetween('msh_date',[$from, $to])->where(['msh_branch_code' => 'TR-BR00001'])
            ->join('misc_details as msd', function ($join) use($from) {
                $join->on('msd.msd_code','=','msh_code')
                    ->where(['msd_remarks' => 'OUT']);
            })
            ->select(DB::raw('SUM(msd_prod_cost* msd.msd_prod_qty) as misc_out_cost, SUM(msd_prod_price * msd.msd_prod_qty) as misc_out_srp'))
            ->groupBy('msh_branch_code')
            ->first();
        $end_inv = InvPosition::where(['ip_date' => $to])->where(['ip_branch_code' => 'TR-BR00001'])
            //->select(DB::raw('SUM(ip_cost) as ip_cost, SUM(ip_srp) as ip_srp'))
            ->first();
        $unrec = ReceivingHeader::whereBetween('rh_date',[$from, $to])
            ->join('receiving_details as rd', 'rd.rd_code', '=', 'rh_no')
            ->join('po_headers as poh', function ($join) use($from, $to) {
                $join->on('poh.po_code', '=', 'rh_po_no')
                    ->whereBetween('po_date',[$from, $to]);
            })
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'poh.po_code');
                $join->on('pod.prod_code', '=', 'rd.rd_prod_code');
            })
            ->select(DB::raw('(SUM((pod.prod_price * pod.prod_qty -(pod.prod_price * pod.prod_qty) *(pod.prod_less / 100))) - SUM((pod.prod_price * rd_prod_qty -(pod.prod_price * rd_prod_qty) *(pod.prod_less / 100)))) as unrec_cost, 
            SUM((pod.prod_srp * prod_qty) - (pod.prod_srp * rd_prod_qty)) as unrec_srp'))
            ->first();
        $unr_cost = @$po->po_cost - @$rec->rec_cost;
        $unr_srp = @$po->po_srp - @$rec->rec_srp;

        $data = array([
            'ACCOUNT TITLE' => 'Inv Beg',
            'COST' => @$inv_beg->ip_cost,
            'SRP' => @$inv_beg->ip_srp
        ],[
            'ACCOUNT TITLE' => 'Purchases',
            'COST' => @$po->po_cost,
            'SRP' => @$po->po_srp
        ],[
            'ACCOUNT TITLE' => 'Receiving',
            'COST' => @$rec->rec_cost,
            'SRP' => @$rec->rec_srp
        ],[
            'ACCOUNT TITLE' => 'Purchase Disc',
            'COST' => @$po_disc->po_disc_cost,
            'SRP' => @$po_disc->po_disc_srp
        ],[
            'ACCOUNT TITLE' => 'Purchase Increase',
            'COST' => @$po_disc->pur_inc,
            'SRP' => @$po_disc->po_disc_srp
        ],[
            'ACCOUNT TITLE' => 'Transfer In',
            'COST' => @$tf_in->total_tf_in_cost,
            'SRP' => @$tf_in->total_tf_in_srp
        ],[
            'ACCOUNT TITLE' => 'CW Deliveries',
            'COST' => @$cw->total_cw_cost,
            'SRP' => @$cw->total_cw_srp
        ],[
            'ACCOUNT TITLE' => 'RTV',
            'COST' => @$rtv->cost,
            'SRP' => @$rtv->srp
        ],[
            'ACCOUNT TITLE' => 'Miscellaneous IN',
            'COST' => @$misc_in->misc_in_cost,
            'SRP' => @$misc_in->misc_in_srp
        ],[
            'ACCOUNT TITLE' => 'Miscellaneous OUT',
            'COST' => @$misc_out->misc_out_cost,
            'SRP' => @$misc_out->misc_out_srp
        ],[
            'ACCOUNT TITLE' => 'End Inv',
            'COST' => @$end_inv->ip_cost,
            'SRP' => @$end_inv->ip_srp
        ],[
            'ACCOUNT TITLE' => 'UNRECEIVED INV',
            'COST' => @$unrec->unrec_cost,
            'SRP' => @$unrec->unrec_srp
        ]);

        return Excel::create('Taurus CW Enterprise Block Box Report', function($excel) use ($data) {
            $excel->setTitle('Taurus CW Enterprise Block Box Report');
            $excel->sheet('CW Enterprise Block Box Report', function($sheet) use ($data)
            {
                $sheet->setColumnFormat(array(
                    'B' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'C' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["CW Enterprise Block Box Report"]);
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
            });
        })->download('xlsx');
    }
}