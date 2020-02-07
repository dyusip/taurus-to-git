<?php

namespace App\Http\Controllers\New_Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Branch;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class TransOutController extends Controller
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
        }elseif (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }
        return $position;
    }
    public function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.new_reports.trans_out',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '!=', 'TR-BR00001')->where('to_branch','!=','TR-BR00001')
                ->select(DB::raw('tf_prod_price, tf_prod_srp as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (tf_prod_price * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
        }elseif($request->optCustType == "branch") {

            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '=', $request->branch)->where('to_branch','!=', 'TR-BR00001')
                ->select(DB::raw('tf_prod_price, tf_prod_srp as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (tf_prod_price * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();

        }
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.new_reports.trans_out',compact('items','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            //$items = TransferHeaders::whereBetween('tf_date', [$from, $to])->get(); //Original
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '!=', 'TR-BR00001')->where('to_branch','!=','TR-BR00001')
                ->select(DB::raw('tf_prod_price, tf_prod_srp as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (tf_prod_price * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
            $br = "ALL BRANCH";
        }elseif($request->optCustType == "branch"){
            //$items = TransferHeaders::where(['to_branch' => $request->branch])->orWhere(['from_branch' => Auth::user()->branch])->whereBetween('tf_date', [$from, $to])->get(); //Original
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where('from_branch', '=', $request->branch)->where('to_branch','!=', 'TR-BR00001')
                ->select(DB::raw('tf_prod_price, tf_prod_srp as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (tf_prod_price * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
            $br = Branch::where(['code' => @$request->branch])->first()->name." BRANCH";
        }
        $data = array();
        $total =  0;
        foreach($items as $item){
            $data[] = [
                'TF CODE' => $item->tf_code,
                'FROM' => $item->from_branch,
                'TO' => $item->to_branch,
                'DATE' => $item->tf_date,
                'ITEM CODE' => $item->tf_prod_code,
                'NAME' => $item->tf_prod_name,
                'COST' => $item->tf_prod_price,
                'QTY' => $item->tf_prod_qty,
                'SRP' => $item->srp ,
                'TOTAL COST' => $item->tf_prod_amount,

            ];
            $total += $item->tf_prod_amount;
        }
        return Excel::create('Taurus Transfer OUT Report', function($excel) use ($data, $total, $br, $from, $to) {
            $excel->setTitle('Taurus Transfer OUT Report');
            $excel->sheet('Transfer OUT Report', function($sheet) use ($data, $total, $br, $from, $to)
            {
                $sheet->setColumnFormat(array(
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Transfer OUT Report $br $from - $to"]);
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
                $footerRow = count($data) + 3;
                $sheet->appendRow("$footerRow",[
                    'Total Amount: '.Number_Format($total,2)
                ]);
                $sheet->mergeCells("A{$footerRow}:J{$footerRow}");
                $sheet->cell("A{$footerRow}", function($cell) {
                    $cell->setBackground('#3ed1f2')
                        ->setAlignment('right')
                        ->setValignment('right')
                        ->setFontWeight('bold')
                        //->setFontColor('#666666')
                        ->setFontSize(11);
                });
            });
        })->download($type);
    }
}
