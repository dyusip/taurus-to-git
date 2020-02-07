<?php

namespace App\Http\Controllers\Payable;

use App\PoHeader;
use App\ReceivingHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class PayableController extends Controller
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
        //$branches = Branch::where(['status'=>'AC'])->get();

        return view($this->position().'.payable.payable');
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = PoHeader::whereHas('po_re_header',function ($query) use($from, $to){
            $query->whereBetween(DB::raw('ADDDATE(rh_date, INTERVAL term DAY)'), [$from, $to])->where(['rh_status' => 'OP'])->where('term','!=','Cash');
        })->get();
        return view($this->position().'.payable.payable',compact('items','request'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = PoHeader::whereHas('po_re_header',function ($query) use($from, $to){
            $query->whereBetween(DB::raw('ADDDATE(rh_date, INTERVAL term DAY)'), [$from, $to])->where(['rh_status' => 'OP'])->where('term','!=','Cash');
        })->get();
        $data = array();
        $total_amount = 0;
        $total_po_amnt = 0;
        foreach($items as $item){
            foreach($item->po_re_header as $receiving) {
                $total = 0;
                if(!is_null($receiving->pop_header)){
                    $amount = $receiving->pop_header->ph_rembal;
                    $total += $amount;
                }else{
                    foreach ($receiving->re_detail as $story) {
                        foreach ($item->po_detail as $key) {
                            if ($key->prod_code == $story->rd_prod_code) {
                                $price = $key->prod_price;
                                $less = $key->prod_less;
                                $amount = ($price * $story->rd_prod_qty - (($price * $story->rd_prod_qty) * $less / 100));
                                $total += $amount;
                            }
                        }
                    }
                }
                $rec_date = Carbon::createFromFormat('Y-m-d', $receiving->rh_date)->format('m/d/Y');
                $duedate = Carbon::parse($receiving->rh_date)->addDays($item->term);
                $duedate = Carbon::createFromFormat('Y-m-d H:i:s', $duedate)->format('m/d/Y');
                $total_amount += $total;
                $total_po_amnt += doubleval(str_replace(",", "", $receiving->re_po_header->total));
                $data[] = [
                    'PO #' => $item->po_code,
                    'PO DATE' => $item->po_date,
                    'REC DATE' => $rec_date,
                    'SI #' => $receiving->rh_si_no,
                    'VENDOR NAME' => $item->supplier->name,
                    'TERM' => $item->term,
                    'DUE DATE' => $duedate,
                    'P.O. AMOUNT' => $receiving->re_po_header->total,
                    'REC AMOUNT' => Number_Format($total,2),
                ];
            }
        }
        return Excel::create('Taurus Payable Details', function($excel) use ($data, $total_amount, $total_po_amnt) {
            $excel->sheet('Payable Details', function($sheet) use ($data, $total_amount, $total_po_amnt)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Payable Details "]);
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
                    'Total P.O. Amount: '.Number_Format($total_po_amnt,2).' - Total Rec Amount: '.Number_Format($total_amount,2)
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
        })->download('xlsx');
    }
}