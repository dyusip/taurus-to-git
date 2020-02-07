<?php

namespace App\Http\Controllers\PerfMeasure;

use App\Branch;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Excel;

class PerfMeasureController extends Controller
{
    //
    function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();
        return view('Management.perf_measure.perf',compact('branches'));
    }
    function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all") {
            $perfs = TransferHeaders::whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '=', 'TR-BR00001')
                ->join('transfer_details as tf', 'tf.td_code', '=', 'tf_code')
                ->join('branch__inventories as bri', function ($join) use ($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bri.branch_code', '=', 'from_branch');
                })
                ->select(DB::raw('tf_prod_code, tf_prod_name,   SUM(tf_prod_qty) as total_dr_qty, SUM(bri.cost * tf_prod_qty) as cost_dr_amount, SUM(bri.price * tf_prod_qty) as srp_dr_amount'))
                ->groupBy('from_branch', 'tf_prod_code', 'tf_prod_name')
                ->selectSub(function ($query) use ($from, $to) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->join('so_details as sod', function ($join)  {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.cost * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'cost_so_amount')
                ->selectSub(function ($query) use ($from, $to) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->join('so_details as sod', function ($join)  {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->selectRaw('sum(sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('sod_prod_code');

                }, 'total_so_qty')
                ->selectSub(function ($query) use ($from, $to) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->join('so_details as sod', function ($join) {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.price * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'srp_so_amount')
                ->get();
        }else if($request->optCustType == "branch"){
            $branch = $request->branch;
            $perfs = TransferHeaders::whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '=', 'TR-BR00001')
                ->where('to_branch','=',$request->branch)
                ->join('transfer_details as tf', 'tf.td_code', '=', 'tf_code')
                ->join('branch__inventories as bri', function ($join) use ($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bri.branch_code', '=', 'from_branch');
                })
                ->select(DB::raw('tf_prod_code, tf_prod_name,SUM(tf_prod_qty) as total_dr_qty, SUM(bri.cost * tf_prod_qty) as cost_dr_amount, SUM(bri.price * tf_prod_qty) as srp_dr_amount'))
                ->groupBy('from_branch', 'tf_prod_code', 'tf_prod_name')
                ->selectSub(function ($query) use ($from, $to, $branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->where('soh.branch_code','=',$branch)
                        ->join('so_details as sod', function ($join) use ($from) {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) use ($from) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.cost * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'cost_so_amount')
                ->selectSub(function ($query) use ($from, $to, $branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->where('soh.branch_code','=',$branch)
                        ->join('so_details as sod', function ($join)  {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->selectRaw('sum(sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('sod_prod_code');

                }, 'total_so_qty')
                ->selectSub(function ($query) use ($from, $to,$branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->where('soh.branch_code','=',$branch)
                        ->join('so_details as sod', function ($join) use ($from) {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) use ($from) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.price * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'srp_so_amount')
                ->get();
        }

        $branches = Branch::where(['status'=>'AC'])->get();
        return view('Management.perf_measure.perf',compact('perfs','request', 'branches'));

    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all") {
            $perfs = TransferHeaders::whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '=', 'TR-BR00001')
                ->join('transfer_details as tf', 'tf.td_code', '=', 'tf_code')
                ->join('branch__inventories as bri', function ($join) use ($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bri.branch_code', '=', 'from_branch');
                })
                ->select(DB::raw('tf_prod_code, tf_prod_name,   SUM(tf_prod_qty) as total_dr_qty, SUM(bri.cost * tf_prod_qty) as cost_dr_amount, SUM(bri.price * tf_prod_qty) as srp_dr_amount'))
                ->groupBy('from_branch', 'tf_prod_code', 'tf_prod_name')
                ->selectSub(function ($query) use ($from, $to) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->join('so_details as sod', function ($join)  {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.cost * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'cost_so_amount')
                ->selectSub(function ($query) use ($from, $to) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->join('so_details as sod', function ($join)  {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->selectRaw('sum(sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('sod_prod_code');

                }, 'total_so_qty')
                ->selectSub(function ($query) use ($from, $to) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->join('so_details as sod', function ($join) {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.price * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'srp_so_amount')
                ->get();
            $br = 'ALL';
        }else if($request->optCustType == "branch"){
            $branch = $request->branch;
            $perfs = TransferHeaders::whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '=', 'TR-BR00001')
                ->where('to_branch','=',$request->branch)
                ->join('transfer_details as tf', 'tf.td_code', '=', 'tf_code')
                ->join('branch__inventories as bri', function ($join) use ($from) {
                    $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                    $join->on('bri.branch_code', '=', 'from_branch');
                })
                ->select(DB::raw('tf_prod_code, tf_prod_name,SUM(tf_prod_qty) as total_dr_qty, SUM(bri.cost * tf_prod_qty) as cost_dr_amount, SUM(bri.price * tf_prod_qty) as srp_dr_amount'))
                ->groupBy('from_branch', 'tf_prod_code', 'tf_prod_name')
                ->selectSub(function ($query) use ($from, $to, $branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->where('soh.branch_code','=',$branch)
                        ->join('so_details as sod', function ($join) use ($from) {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) use ($from) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.cost * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'cost_so_amount')
                ->selectSub(function ($query) use ($from, $to, $branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->where('soh.branch_code','=',$branch)
                        ->join('so_details as sod', function ($join)  {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->selectRaw('sum(sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('sod_prod_code');

                }, 'total_so_qty')
                ->selectSub(function ($query) use ($from, $to,$branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('so_headers as soh')
                        ->whereBetween('so_date', [$from, $to])
                        ->where('soh.branch_code','=',$branch)
                        ->join('so_details as sod', function ($join) use ($from) {
                            //$join->on('sod.sod_prod_code', '=', 'tf.tf_prod_code');
                            $join->on('soh.so_code', '=', 'sod.sod_code');
                        })
                        ->join('branch__inventories as bri2', function ($join) use ($from) {
                            $join->on('bri2.prod_code', '=', 'sod.sod_prod_code');
                            $join->on('bri2.branch_code', '=', 'soh.branch_code');
                        })
                        ->selectRaw('sum(bri2.price * sod.sod_prod_qty)')
                        ->whereRaw('`sod`.`sod_prod_code` = `tf`.`tf_prod_code`')->groupBy('prod_code');

                }, 'srp_so_amount')
                ->get();
            $br = Branch::where(['code' => $request->branch])->first()->name;
        }
        $data = array();
        foreach($perfs as $perf){
            $delta = $perf->total_dr_qty -  $perf->total_so_qty;
            $data[] = [
                'BRANCH' => $br,
                'ITEM CODE' => $perf->tf_prod_code,
                'NAME' => $perf->tf_prod_name,
                'DR QTY' => $perf->total_dr_qty,
                'DR COST' => $perf->cost_dr_amount,
                'DR SRP' => $perf->srp_dr_amount,
                'SO QTY' => $perf->total_so_qty,
                'SO COST' => $perf->cost_so_amount,
                'SO SRP' => $perf->srp_so_amount,
                'DELTA' => $delta,
            ];
        }
        return Excel::create('Taurus Performance Measure Report', function($excel) use ($data) {
            $excel->setTitle('Taurus Performance Measure Report');
            $excel->sheet('Performance Measure Report', function($sheet) use ($data)
            {
                $sheet->setColumnFormat(array(
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Performance Measure Report"]);
                $sheet->mergeCells("A1:J1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
            });
        })->download('xlsx');
    }
}