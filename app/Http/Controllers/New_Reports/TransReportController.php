<?php

namespace App\Http\Controllers\TransReport;

use App\Branch;
use App\Branch_Inventory;
use App\SoHeader;
use App\TransferDetails;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

class TransReportController extends Controller
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
        return view($this->position().'.transfer_report.transfer',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            //$items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])->get();//Original
            /*$items = TransferDetails::whereHas('tf_header', function ($query) use ($from, $to) {
                $query->where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to]);
            })*/
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where(['from_branch' => 'TR-BR00001'])->where('to_branch','!=','TR-BR00001')
                ->select(DB::raw('bri.cost as tf_prod_price, bri.price as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (bri.cost * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
        }elseif($request->optCustType == "branch") {
            /*$items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where(function ($query) use($request) {
                    $query->where(['from_branch' => $request->branch])
                        ->orWhere(['to_branch' => $request->branch]);
                })->get();*/ //Original
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                /*->where(function ($query) use($request) {
                    $query->where(['from_branch' => $request->branch])
                        ->orWhere(['to_branch' => $request->branch]);
                })*/
                ->where(['from_branch' => 'TR-BR00001'])->where('to_branch','=',$request->branch)
                ->select(DB::raw('bri.cost as tf_prod_price, bri.price as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (bri.cost * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
            /*$tf = TransferHeaders::where(['to_branch' => $request->branch, 'tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->join('transfer_details as tf_dt', 'tf_dt.td_code', '=', 'tf_code')
                ->select(DB::raw('tf_date, SUM(tf_prod_price * tf_prod_qty) as total_tf'))
                ->groupBy('tf_date')
                // ->groupBy('td_code','tf_code','from_branch','to_branch')
                ->get();
            $tr = TransferHeaders::where(['from_branch' => $request->branch, 'tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->join('transfer_details as tf_dt', 'tf_dt.td_code', '=', 'tf_code')
                ->select(DB::raw('tf_date, SUM(tf_prod_price * tf_prod_qty) as total_tf'))
                ->groupBy('tf_date')
                // ->groupBy('td_code','tf_code','from_branch','to_branch')
                ->get();
            $so = SoHeader::where(['branch_code' => $request->branch, 'so_status' => 'PD'])->whereBetween('so_date', [$from, $to])
                ->join('so_details as so_dt', 'so_dt.sod_code', '=', 'so_code')
                ->select(DB::raw('so_date, SUM(sod_prod_price * sod_prod_qty) as total_so'))
                ->groupBy('so_date')
                // ->groupBy('td_code','tf_code','from_branch','to_branch')
                ->get();

            $br = Branch::where(['code' => $request->branch])->first();
            $data = array();
            $type = 'xlsx';
            while ($from <= $to) {
                $tf_found = 0; $tr_found = 0; $so_found = 0;
                $from = Carbon::parse($from)->format('m/d/Y');
                foreach ($tf as $key => $val) {
                    $tf_date = Carbon::parse($val->tf_date)->format('m/d/Y');
                    if ($tf_date == $from) {
                        $tf_found = $val->total_tf;
                        unset($tf[$key]);
                    }
                }
                foreach ($tr as $key => $val) {
                    $tf_date = Carbon::parse($val->tf_date)->format('m/d/Y');
                    if ($tf_date == $from) {
                        $tr_found = $val->total_tf;
                        unset($tr[$key]);
                    }
                }
                foreach ($so as $key_so => $value) {
                    $so_date = Carbon::parse($value->so_date)->format('m/d/Y');
                    if ($so_date == $from) {
                        $so_found = $value->total_so;
                        unset($so[$key_so]);
                    }
                }
                //echo "$br->name - $tf_found - $tr_found - $so_found <br>";

                //$from = Carbon::parse($from)->format('Y-m-d');
                $data[] = [
                    'BRANCH' => $br->name,
                    'DATE' => $from,
                    'INV BEG' => "",
                    'RECEIVED' => Number_Format($tf_found,2),
                    'TRANSFER' => Number_Format($tr_found,2),
                    'SALES' => Number_Format($so_found,2),
                    'MY COLUMN' => "",
                ];
                $from = Carbon::parse($from)->addDays(1)->format('Y-m-d');

            }
            return Excel::create('Taurus TransferReport', function($excel) use ($data) {
                $excel->setTitle('Taurus Transfer Report');
                $excel->sheet('Sales Report', function($sheet) use ($data)
                {
                    $sheet->fromArray($data);

                });
            })->download('xlsx');*/

        }
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.transfer_report.transfer',compact('items','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            //$items = TransferHeaders::whereBetween('tf_date', [$from, $to])->get(); //Original
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                ->where(['from_branch' => 'TR-BR00001'])->where('to_branch','!=','TR-BR00001')
                ->select(DB::raw('bri.cost as tf_prod_price, bri.price as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (bri.cost * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
        }elseif($request->optCustType == "branch"){
            //$items = TransferHeaders::where(['to_branch' => $request->branch])->orWhere(['from_branch' => Auth::user()->branch])->whereBetween('tf_date', [$from, $to])->get(); //Original
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
                /*->where(function ($query) use($request) {
                    $query->where(['from_branch' => $request->branch])
                        ->orWhere(['to_branch' => $request->branch]);
                })*/
                ->where(['from_branch' => 'TR-BR00001'])->where('to_branch','=',$request->branch)
                ->select(DB::raw('bri.cost as tf_prod_price, bri.price as srp, tf_code,tf_dt.tf_prod_code, tf_date,
                tf_dt.tf_prod_name,tf_dt.tf_prod_qty, fr_branch.name as from_branch, to_branch.name as to_branch,
                (bri.cost * tf_dt.tf_prod_qty) as tf_prod_amount'))
                ->join('transfer_details as tf_dt','tf_code','=','tf_dt.td_code')
                ->join('branches as fr_branch','fr_branch.code','=','from_branch')
                ->join('branches as to_branch','to_branch.code','=','to_branch')
                ->join('branch__inventories as bri', function ($join) use($from) {
                    $join->on('bri.prod_code', '=', 'tf_prod_code');
                    $join->on('bri.branch_code','=','from_branch');
                })
                ->get();
        }
        $data = array();
        $total =  0;
        /*foreach($items as $item){
            foreach($item->tf_detail as $story) {
                $data[] = [
                    'TF CODE' => $item->tf_code,
                    'FROM' => $item->tf_fr_branch->name,
                    'TO' => $item->tf_to_branch->name,
                    'DATE' => $item->tf_date,
                    'ITEM CODE' => $story->tf_prod_code,
                    'NAME' => $story->tf_prod_name,
                    'QTY' => $story->tf_prod_qty,
                    'PRICE' => $story->tf_prod_price,
                    'AMOUNT' => $story->tf_prod_amount,
                ];
                $total += $story->tf_prod_amount;
            }
        }
        return Excel::create('Taurus TransferReport', function($excel) use ($data, $total) {
            $excel->setTitle('Taurus Transfer Report');
            $excel->sheet('Transfer Report', function($sheet) use ($data, $total)
            {
                $sheet->setColumnFormat(array(
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Transfer Report "]);
                $sheet->mergeCells("A1:I1");
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
                $sheet->mergeCells("A{$footerRow}:I{$footerRow}");
                $sheet->cell("A{$footerRow}", function($cell) {
                    $cell->setBackground('#3ed1f2')
                        ->setAlignment('right')
                        ->setValignment('right')
                        ->setFontWeight('bold')
                        //->setFontColor('#666666')
                        ->setFontSize(11);
                });
            });
        })->download($type);*/ //Original
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
                    'COST AMOUNT' => $item->tf_prod_amount,

                ];
                $total += $item->tf_prod_amount;
        }
        return Excel::create('Taurus CW Deliveries Report', function($excel) use ($data, $total) {
            $excel->setTitle('Taurus CW Deliveries Report');
            $excel->sheet('CW Deliveries Report', function($sheet) use ($data, $total)
            {
                $sheet->setColumnFormat(array(
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus CW Deliveries Report "]);
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
