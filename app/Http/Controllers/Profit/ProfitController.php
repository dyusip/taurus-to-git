<?php

namespace App\Http\Controllers\Profit;

use App\Branch;
use App\Branch_Inventory;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;

class ProfitController extends Controller
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

        return view($this->position().'.profit.profit',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SoHeader::whereBetween('so_date', [$from, $to])->get();
            $inventories = Branch_Inventory::all();
        }elseif($request->optCustType == "branch"){
            $sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
            $inventories = Branch_Inventory::where(['branch_code' => $request->branch])->get();
        }
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.profit.profit',compact('sales','request', 'branches','inventories'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $sales = SoHeader::whereBetween('so_date', [$from, $to])->get();
            $inventories = Branch_Inventory::all();
        }elseif($request->optCustType == "branch"){
            $sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
            $inventories = Branch_Inventory::where(['branch_code' => $request->branch])->get();
        }
        $data = array();
        $total =  0;
        $tot_profit = 0;
        foreach($sales as $sale){
            foreach($sale->so_detail as $story) {
                $profit = 0;
                foreach($inventories as $inventory) {
                    if ($inventory->branch_code == $sale->branch_code) {
                        $cost = $inventory->cost;
                        $tot_cost = $inventory->cost * $story->sod_prod_qty;
                        $profit = $story->sod_prod_amount - $tot_cost;
                    }
                }


                $data[] = [
                    'BRANCH' => $sale->so_branch->name,
                    'ITEM CODE' => $story->sod_prod_code,
                    'NAME' => $story->sod_prod_name,
                    'COST' => $cost,
                    'QTY' => $story->sod_prod_qty,
                    'PRICE' => $story->sod_prod_price,
                    'LESS' => $story->sod_less,
                    'TOTAL COST' => $tot_cost,
                    'SALES' => $story->sod_prod_amount,
                    'PROFIT' => $profit,
                ];
                $total += $story->sod_prod_amount;
                $tot_profit += $profit;
            }
        }

        return Excel::create('Taurus Profit Margin Report', function($excel) use ($data, $total, $tot_profit) {
            $excel->setTitle('Taurus Profit Margin Report');
            $excel->sheet('Profit Margin Report', function($sheet) use ($data, $total, $tot_profit)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Profit Margin Report "]);
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
                    'Total Sales: '.Number_Format($total,2).' ---- Total Profit: '.Number_Format($tot_profit,2)
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
