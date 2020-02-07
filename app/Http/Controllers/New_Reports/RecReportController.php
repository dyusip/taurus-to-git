<?php

namespace App\Http\Controllers\RecReport;

use App\Branch;
use App\ReceivingHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

class RecReportController extends Controller
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
    public function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.receiving_report.receiving',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = ReceivingHeader::whereBetween('rh_date', [$from, $to])->get();
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.receiving_report.receiving',compact('items','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = ReceivingHeader::whereBetween('rh_date', [$from, $to])->get();
        $data = array();
        $total =  0;
        foreach($items as $item){
            foreach($item->re_detail as $story) {
                foreach($item->pod_detail as $key) {
                    if ($key->prod_code == $story->rd_prod_code) {
                        $price = $key->prod_price;
                        $less = $key->prod_less;
                        $amount = ($price * $story->rd_prod_qty - (($price * $story->rd_prod_qty) * $less / 100));
                        $total += $amount;
                    }
                }
                $data[] = [
                    'REC CODE' => $item->rh_no,
                    'PO CODE' => $item->rh_po_no,
                    'SI #' => $item->rh_si_no,
                    'DATE' => $item->rh_date,
                    'ITEM CODE' => $story->rd_prod_code,
                    'NAME' => $story->rd_prod_name,
                    'QTY' => $story->rd_prod_qty,
                    'STATUS' => $story->rd_status,
                    'PRICE' => Number_Format($price,2),
                    'LESS' => $less."%",
                    'AMOUNT' => Number_Format($amount,2),
                ];
            }
        }

        return Excel::create('Taurus RecevingReport', function($excel) use ($data, $total) {
            $excel->sheet('Receiving Report', function($sheet) use ($data, $total)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Receiving Report "]);
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
                    'Total Amount: '.Number_Format($total,2)
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
        })->download('xlsx');
    }
}
