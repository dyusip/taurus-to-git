<?php

namespace App\Http\Controllers\SalesReport;

use App\Branch;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if(Auth::user()->position == 'CEO' || Auth::user()->position == 'Purchasing') {
            if ($request->optCustType == "all") {
                $sales = SoHeader::whereBetween('so_date', [$from, $to])
                    ->join('so_details as sod', 'sod.sod_code', '=', 'so_code')
                    ->join('branch__inventories as bri', function ($join) use ($from) {
                        $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                        //$join->on('bri.branch_code','=','to_branch');
                        $join->on('bri.branch_code', '=', 'so_headers.branch_code');
                    })
                    ->select(DB::raw('so_headers.branch_code,bri.cost, bri.price,
                    so_code,jo_code,so_date,sod_prod_code,sod_prod_name,cost,sod_prod_qty,sod_prod_price,sod_less,sod_prod_amount'))
                    ->groupBy('branch_code', 'so_code', 'jo_code', 'so_date', 'sod_prod_code', 'sod_prod_name'
                        , 'cost', 'price', 'sod_prod_qty', 'sod_prod_price', 'sod_less', 'sod_prod_amount')
                    ->get();
            } elseif ($request->optCustType == "branch") {
                //$sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
                $sales = SoHeader::where(['so_headers.branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])
                    ->join('so_details as sod', 'sod.sod_code', '=', 'so_code')
                    ->join('branch__inventories as bri', function ($join) use ($from) {
                        $join->on('bri.prod_code', '=', 'sod.sod_prod_code');
                        //$join->on('bri.branch_code','=','to_branch');
                        $join->on('bri.branch_code', '=', 'so_headers.branch_code');
                    })
                    ->select(DB::raw('so_headers.branch_code,bri.cost, bri.price,
                    so_code,jo_code,so_date,sod_prod_code,sod_prod_name,cost,sod_prod_qty,sod_prod_price,sod_less,sod_prod_amount'))
                    ->groupBy('branch_code', 'so_code', 'jo_code', 'so_date', 'sod_prod_code', 'sod_prod_name'
                        , 'cost', 'price', 'sod_prod_qty', 'sod_prod_price', 'sod_less', 'sod_prod_amount')
                    ->get();
            }
            $data = array();
            $total = 0;
            foreach ($sales as $sale) {
                $data[] = [
                    'BRANCH' => $sale->so_branch->name,
                    'SO CODE' => $sale->so_code,
                    'JO CODE' => $sale->jo_code,
                    'DATE' => $sale->so_date,
                    'ITEM CODE' => $sale->sod_prod_code,
                    'NAME' => $sale->sod_prod_name,
                    'COST' => $sale->cost,
                    'SRP' => $sale->price,
                    'QTY' => $sale->sod_prod_qty,
                    'PRICE' => $sale->sod_prod_price,
                    'LESS' => $sale->sod_less,
                    'AMOUNT' => $sale->sod_prod_amount,
                ];
                $total += $sale->sod_prod_amount;
            }

            return Excel::create('Taurus SalesReport', function ($excel) use ($data, $total) {
                $excel->setTitle('Taurus Sales Report');
                $excel->sheet('Sales Report', function ($sheet) use ($data, $total) {
                    $sheet->setColumnFormat(array(
                        /*'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,*/
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    ));
                    $sheet->fromArray($data);

                    $sheet->prependRow(1, ["Taurus Sales Report "]);
                    $sheet->mergeCells("A1:L1");
                    $sheet->cell('A1', function ($cell) {
                        // change header color
                        $cell->setBackground('#3ed1f2')
                            ->setFontColor('#0a0a0a')
                            ->setFontWeight('bold')
                            ->setAlignment('center')
                            ->setValignment('center')
                            ->setFontSize(13);;
                    });
                    $footerRow = count($data) + 3;
                    $sheet->appendRow("$footerRow", [
                        'Total Amount: ₱' . Number_Format($total, 2)
                    ]);
                    $sheet->mergeCells("A{$footerRow}:L{$footerRow}");
                    $sheet->cell("A{$footerRow}", function ($cell) {
                        $cell->setBackground('#3ed1f2')
                            ->setAlignment('right')
                            ->setValignment('right')
                            ->setFontWeight('bold')
                            //->setFontColor('#666666')
                            ->setFontSize(11);
                    });
                });
            })->download($type);
        }else{ //Salesman Sales Report
            if ($request->optCustType == "all") {
                $sales = SoHeader::whereBetween('so_date', [$from, $to])->get();
            } elseif ($request->optCustType == "branch") {
                $sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
            }
            $data = array();
            $total = 0;
            foreach ($sales as $sale) {
                foreach ($sale->so_detail as $story) {
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

            return Excel::create('Taurus SalesReport', function ($excel) use ($data, $total) {
                $excel->setTitle('Taurus Sales Report');
                $excel->sheet('Sales Report', function ($sheet) use ($data, $total) {
                    $sheet->setColumnFormat(array(
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    ));
                    $sheet->fromArray($data);

                    $sheet->prependRow(1, ["Taurus Sales Report "]);
                    $sheet->mergeCells("A1:J1");
                    $sheet->cell('A1', function ($cell) {
                        // change header color
                        $cell->setBackground('#3ed1f2')
                            ->setFontColor('#0a0a0a')
                            ->setFontWeight('bold')
                            ->setAlignment('center')
                            ->setValignment('center')
                            ->setFontSize(13);;
                    });
                    $footerRow = count($data) + 3;
                    $sheet->appendRow("$footerRow", [
                        'Total Amount: ₱' . Number_Format($total, 2)
                    ]);
                    $sheet->mergeCells("A{$footerRow}:J{$footerRow}");
                    $sheet->cell("A{$footerRow}", function ($cell) {
                        $cell->setBackground('#3ed1f2')
                            ->setAlignment('right')
                            ->setValignment('right')
                            ->setFontWeight('bold')
                            //->setFontColor('#666666')
                            ->setFontSize(11);
                    });
                });
            })->download($type);
        }//end of Salesman Sales Report
    }
}
