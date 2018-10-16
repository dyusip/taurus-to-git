<?php

namespace App\Http\Controllers\SrReport;

use App\Branch;
use App\SoHeader;
use App\SrHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

class SrReportController extends Controller
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
        }elseif (Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
    public function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();

        return view($this->position().'.salesreturn_report.salesreturn',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SrHeader::whereBetween('sr_date', [$from, $to])->get();
        }elseif($request->optCustType == "branch"){
            $sales = SrHeader::whereBetween('sr_date', [$from, $to])->whereHas('sr_so_header', function ($query) use ($request) {
                $query->where(['branch_code' => $request->branch]);
            })->get();
        }
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.salesreturn_report.salesreturn',compact('sales','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SrHeader::whereBetween('sr_date', [$from, $to])->get();
        }elseif($request->optCustType == "branch"){
            $sales = SrHeader::whereBetween('sr_date', [$from, $to])->whereHas('sr_so_header', function ($query) use ($request) {
                $query->where(['branch_code' => $request->branch]);
            })->get();
        }
        $data = array();
        $total =  0;
        foreach($sales as $sale){
            foreach($sale->sr_detail as $story) {
                $data[] = [
                    'BRANCH' => $sale->sr_so_header->so_branch->name,
                    'SR CODE' => $sale->sr_code,
                    'SR DATE' => $sale->sr_date,
                    'SO CODE' => $sale->so_code,
                    'SO DATE' => $sale->so_date,
                    'ITEM CODE' => $story->srd_prod_code,
                    'NAME' => $story->srd_prod_name,
                    'QTY' => $story->srd_prod_qty,
                    'PRICE' => $story->srd_prod_price,
                    'LESS' => $story->srd_less,
                    'AMOUNT' => $story->srd_prod_amount,
                ];
                $total += $story->srd_prod_amount;
            }
        }

        return Excel::create('Taurus Sales-Return Report', function($excel) use ($data, $total) {
            $excel->setTitle('Taurus Sales-Return Report');
            $excel->sheet('Sales-Return', function($sheet) use ($data, $total)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Sales-Return"]);
                $sheet->mergeCells("A1:K1");
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
                $sheet->mergeCells("A{$footerRow}:K{$footerRow}");
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
