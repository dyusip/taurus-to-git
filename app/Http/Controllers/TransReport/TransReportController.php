<?php

namespace App\Http\Controllers\TransReport;

use App\Branch;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

class TransReportController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'PURCHASING'){
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
            $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])->get();
        }elseif($request->optCustType == "branch"){
            $items = TransferHeaders::where(['to_branch' => $request->branch, 'tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])->get();
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
            $items = TransferHeaders::whereBetween('tf_date', [$from, $to])->get();
        }elseif($request->optCustType == "branch"){
            $items = TransferHeaders::where(['to_branch' => $request->branch])->whereBetween('tf_date', [$from, $to])->get();
        }
        $data = array();
        $total =  0;
        foreach($items as $item){
            foreach($item->tf_detail as $story) {
                $data[] = [
                    'TF CODE' => $item->tf_code,
                    'FROM' => $item->tf_fr_branch->name,
                    'TO' => $item->tf_to_branch->name,
                    'DATE' => $item->tf_date,
                    'ITEM CODE' => $story->tf_prod_code,
                    'NAME' => $story->tf_prod_name,
                    'QTY' => $story->tf_prod_qty,
                    'PRICE' => Number_Format($story->tf_prod_price,2),
                    'AMOUNT' => Number_Format($story->tf_prod_amount,2),
                ];
                $total += $story->tf_prod_amount;
            }
        }
        return Excel::create('Taurus TransferReport', function($excel) use ($data, $total) {
            $excel->setTitle('Taurus Transfer Report');
            $excel->sheet('Sales Report', function($sheet) use ($data, $total)
            {
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
        })->download($type);
    }
}
