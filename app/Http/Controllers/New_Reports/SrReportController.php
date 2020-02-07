<?php

namespace App\Http\Controllers\New_Reports;

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

        return view($this->position().'.new_reports.salesreturn',compact('branches'));
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
        return view($this->position().'.new_reports.salesreturn',compact('sales','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SrHeader::whereBetween('sr_date', [$from, $to])->get();
            $br = "ALL BRANCH";
        }elseif($request->optCustType == "branch"){
            $sales = SrHeader::whereBetween('sr_date', [$from, $to])->whereHas('sr_so_header', function ($query) use ($request) {
                $query->where(['branch_code' => $request->branch]);
            })->get();
            $br = Branch::where(['code' => @$request->branch])->first()->name." BRANCH";
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
                    'COST' => $story->srd_prod_cost,
                    'SRP' => $story->srd_prod_srp,
                    'QTY' => $story->srd_prod_qty,
                    'SALES RETURN PRICE' => $story->srd_prod_price,
                    'LESS' => $story->srd_less,
                    'TOTAL SALES RETURN' => $story->srd_prod_amount,
                ];
                $total += $story->srd_prod_amount;
            }
        }

        return Excel::create('Taurus Sales-Return Report', function($excel) use ($data, $total, $br , $from, $to) {
            $excel->setTitle('Taurus Sales-Return Report');
            $excel->sheet('Sales-Return', function($sheet) use ($data, $total, $br , $from, $to)
            {
                $sheet->setColumnFormat(array(
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'K' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'M' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Sales-Return $br $from - $to"]);
                $sheet->mergeCells("A1:M1");
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
                $sheet->mergeCells("A{$footerRow}:M{$footerRow}");
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
