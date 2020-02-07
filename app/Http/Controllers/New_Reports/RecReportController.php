<?php

namespace App\Http\Controllers\New_Reports;

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
        $tot_gross = 0;
        $tot_disc = 0;
        foreach($items as $item){
            foreach($item->re_detail as $story) {
                foreach($item->pod_detail as $key) {
                    if ($key->prod_code == $story->rd_prod_code) {
                        $price = $key->prod_price;
                        $less = $key->prod_less;
                        $amount = ($price * $story->rd_prod_qty - (($price * $story->rd_prod_qty) * $less / 100));
                        $total += $amount;
                        $gross_purch = $price * $story->rd_prod_qty;
                        $disc = (($price * $story->rd_prod_qty) - $amount);
                        $tot_gross += $gross_purch;
                        $tot_disc += $disc;
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
                    'PRICE' => $price,
                    'LESS' => $less."%",
                    //'AMOUNT' => Number_Format($amount,2),
                    'GROSS PURCH' => $gross_purch,
                    'DISCOUNT' => $disc,
                    'NET PAYABLE' => $amount,
                ];
            }
        }

        return Excel::create('Taurus RecevingReport', function($excel) use ($data, $total, $tot_gross, $tot_disc) {
            $excel->sheet('Receiving Report', function($sheet) use ($data, $total, $tot_gross, $tot_disc)
            {
                $sheet->setColumnFormat(array(
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'K' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'L' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'M' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Receiving Report "]);
                $sheet->mergeCells("A1:M1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);
                });
                $footerRow = count($data) + 3;
                $sheet->cell("A{$footerRow}:M{$footerRow}", function($cell) {
                    $cell->setBackground('#3ed1f2')
                        ->setAlignment('right')
                        ->setValignment('right')
                        ->setFontWeight('bold')
                        //->setFontColor('#666666')
                        ->setFontSize(11);
                });
                $sheet->cell("K{$footerRow}", function($cell) use($tot_gross) {
                    $cell->setValue($tot_gross);
                });
                $sheet->cell("L{$footerRow}", function($cell)use ($tot_disc) {
                    $cell->setValue($tot_disc);
                });
                $sheet->cell("M{$footerRow}", function($cell) use($total) {
                    $cell->setValue($total);
                });

            });
        })->download('xlsx');
    }
}
