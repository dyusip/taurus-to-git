<?php

namespace App\Http\Controllers\New_Reports;

use App\Branch;
use App\PoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

class PurchaseReportController extends Controller
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

        return view($this->position().'.new_reports.purchase',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = PoHeader::whereBetween('po_date', [$from, $to])->where(function($query)  {
            $query->where('status','=','AP')->orWhere('status','=','CL');
        })->get();
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.new_reports.purchase',compact('items','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = PoHeader::whereBetween('po_date', [$from, $to])->where(function($query)  {
            $query->where('status','=','AP')->orWhere('status','=','CL');
        })->get();
        $data = array();
        $total =  0;
        foreach($items as $item){
            foreach($item->po_detail as $story) {
                $data[] = [
                    'PO CODE' => $item->po_code,
                    'DATE' => $item->po_date,
                    'ITEM CODE' => $story->prod_code,
                    'NAME' => $story->prod_name,
                    'COST' => $story->prod_cost,
                    'SRP' => $story->prod_srp,
                    'QTY' => $story->prod_qty,
                    'PO PRICE' => $story->prod_price,
                    'LESS' => $story->prod_less."%",
                    'PO AMOUNT' => $story->prod_amount,
                ];
                $total += $story->prod_amount;
            }
        }

        return Excel::create('Taurus Purchase Report', function($excel) use ($data, $total, $from, $to) {
            $excel->sheet('Purchase Report', function($sheet) use ($data, $total, $from, $to)
            {
                $sheet->setColumnFormat(array(
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));

                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Purchase Report $from - $to"]);
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
                    'Total Amount: ₱'.Number_Format($total,2)
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
        })->download('xlsx');
    }
}
