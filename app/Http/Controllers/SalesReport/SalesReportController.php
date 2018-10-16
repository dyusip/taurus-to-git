<?php

namespace App\Http\Controllers\SalesReport;

use App\Branch;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use Illuminate\Support\Facades\Auth;

class SalesReportController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
    public function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();

        return view($this->position().'.sales_report.sales',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SoHeader::whereBetween('so_date', [$from, $to])->get();
        }elseif($request->optCustType == "branch"){
            $sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
        }
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.sales_report.sales',compact('sales','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SoHeader::whereBetween('so_date', [$from, $to])->get();
        }elseif($request->optCustType == "branch"){
            $sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
        }
        $data = array();
        $total =  0;
        foreach($sales as $sale){
            foreach($sale->so_detail as $story) {
                $data[] = [
                        'BRANCH' => $sale->so_branch->name,
                        'SO CODE' => $sale->so_code,
                        'JO CODE' => $sale->jo_code,
                        'DATE' => $sale->so_date,
                        'ITEM CODE' => $story->sod_prod_code,
                        'NAME' => $story->sod_prod_name,
                        'QTY' => $story->sod_prod_qty,
                        'PRICE' => $story->sod_prod_price,
                        'LESS' => $story->sod_less,
                        'AMOUNT' => $story->sod_prod_amount,
                ];
                $total += $story->sod_prod_amount;
            }
        }

        return Excel::create('Taurus SalesReport', function($excel) use ($data, $total) {
            $excel->setTitle('Taurus Sales Report');
            $excel->sheet('Sales Report', function($sheet) use ($data, $total)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Sales Report "]);
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
                    'Total Amount: '.$total
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
