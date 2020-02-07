<?php

namespace App\Http\Controllers\New_Reports;

use App\Branch;
use App\Branch_Inventory;
use App\SoDetail;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\DB;

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

        return view($this->position().'.new_reports.profit',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            /*$total_cost = SoHeader::whereBetween('so_date', [$from, $to])
                ->select(DB::raw('sum(bi.cost * sod.sod_prod_qty) as total_cost, sod.sod_code,bri.branch_code, bri.prod_code'))->groupBy('sod.sod_code','bri.branch_code','bri.prod_code')
            ->leftjoin('so_details as sod','sod_code','=','so_headers.so_code')->leftjoin('branch__inventories as bi','bi.branch_code','=','so_headers.branch_code')
            ->rightjoin('branch__inventories as bri', 'sod.sod_prod_code','=','bi.prod_code')->get();*/
            /* $query->withCount([
                 'activity AS paid_sum' => function ($query) {
                     $query->select(DB::raw("SUM(amount_total) as paidsum"))->where('status', 'paid');
                 }
             ]);*/
            //$inventories = Branch_Inventory::all();
            /*$sales = SoDetail::whereHas('so_header',function ($query) use($from, $to){
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sod_prod_code, SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_amount'))
                ->groupBy('sod_prod_code')
                //->join('branch__inventories as bri','sod_prod_code','=','bri.prod_code')
                ->get();*/
            /* $count = SoDetail::whereHas('so_header',function ($query) use($from, $to){
                 $query->whereBetween('so_date', [$from, $to]);
             }) ->select('sod_prod_code')
                 ->groupBy('sod_prod_code')
                 ->get();*/
            $sales = SoDetail::whereHas('so_header',function ($query) use($from, $to){
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sod_prod_code,sod_prod_name, sum(sod_prod_qty) as total_qty, 
                    AVG(sod_less) as less, AVG(sod_prod_cost) as cost, AVG(sod_prod_srp) as srp,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) / sum(sod_prod_qty) as price, 
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_amount, SUM(sod_prod_cost * sod_prod_qty) as total_cost'))
                ->groupBy('sod_prod_code','sod_prod_name')
                //->join('branch__inventories as bri','sod_prod_code','=','bri.prod_code')
                ->join('so_headers as soh','soh.so_code','=','sod_code')
                /*->join('branch__inventories as bri', function ($join) {
                    $join->on('bri.prod_code', '=', 'sod_prod_code');
                    $join->on('bri.branch_code','=','soh.branch_code');

                })*/
                ->get();

            /*->count([
            'so_detail as cost' => function ($query) {
                $query->select(DB::raw('sum(sod_prod_qty) as total_cost'))->groupBy('sod_prod_code');
            }
        ])->get();*/
        }elseif($request->optCustType == "branch"){
            //$sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
            //$inventories = Branch_Inventory::where(['branch_code' => $request->branch])->get();
            $branch = $request->branch;
            $sales = SoDetail::whereHas('so_header',function ($query) use($from, $to, $branch){
                $query->whereBetween('so_date', [$from, $to])->where(['branch_code' => $branch]);
            })->select(DB::raw('sod_prod_code,sod_prod_name, sum(sod_prod_qty) as total_qty, br.name as branch,
                    AVG(sod_less) as less, AVG(sod_prod_cost) as cost, AVG(sod_prod_srp) as srp,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) / sum(sod_prod_qty) as price,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_amount, 
                    SUM(sod_prod_cost * sod_prod_qty) as total_cost'))
                ->groupBy('sod_prod_code','sod_prod_name','br.name')
                //->join('branch__inventories as bri','sod_prod_code','=','bri.prod_code')
                ->join('so_headers as soh','soh.so_code','=','sod_code')
                ->join('branches as br','soh.branch_code','=','br.code')
                /*->join('branch__inventories as bri', function ($join) {
                    $join->on('bri.prod_code', '=', 'sod_prod_code');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })*/
                ->get();

        }
        /*foreach ($sales as $sale){
            echo $sale->sod_code.' - '. $sale->total_cost.'<br>';
        }*/
        //$merged = $sales->merge($costs);
        //echo ($count;
        /* $ctr = 1;
         foreach ($costs as $sale){
             echo $ctr++;
             echo $sale->branch_code.' - '.$sale->sod_prod_code. ' - '.$sale->total_amount.' - '.$sale->total_cost.'<br>';
         }*/
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.new_reports.profit',compact('sales','request', 'branches','inventories','total_cost'));
    }
    public function print_report(Request $request)
    {
        $type = 'xlsx';
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            /*$sales = SoHeader::whereBetween('so_date', [$from, $to])->get();
            $inventories = Branch_Inventory::all();*/
            $sales = SoDetail::whereHas('so_header',function ($query) use($from, $to){
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sod_prod_code,sod_prod_name, sum(sod_prod_qty) as total_qty, 
                    AVG(sod_less) as less, AVG(sod_prod_cost) as cost, AVG(sod_prod_srp) as srp,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) / sum(sod_prod_qty) as price,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_amount, SUM(sod_prod_cost * sod_prod_qty) as total_cost'))
                ->groupBy('sod_prod_code','sod_prod_name')
                //->join('branch__inventories as bri','sod_prod_code','=','bri.prod_code')
                ->join('so_headers as soh','soh.so_code','=','sod_code')
                /*->join('branch__inventories as bri', function ($join) {
                    $join->on('bri.prod_code', '=', 'sod_prod_code');
                    $join->on('bri.branch_code','=','soh.branch_code');

                })*/
                ->get();
            $br = "ALL BRANCH";
        }elseif($request->optCustType == "branch"){
            /*$sales = SoHeader::where(['branch_code' => $request->branch])->whereBetween('so_date', [$from, $to])->get();
            $inventories = Branch_Inventory::where(['branch_code' => $request->branch])->get();*/
            $branch = $request->branch;
            $sales = SoDetail::whereHas('so_header',function ($query) use($from, $to, $branch){
                $query->whereBetween('so_date', [$from, $to])->where(['branch_code' => $branch]);
            })->select(DB::raw('sod_prod_code,sod_prod_name, sum(sod_prod_qty) as total_qty, br.name as branch,
                    AVG(sod_less) as less, AVG(sod_prod_cost) as cost, AVG(sod_prod_srp) as srp,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) / sum(sod_prod_qty) as price,
                    SUM(sod_prod_price * sod_prod_qty - ((sod_prod_price * sod_prod_qty) * (sod_less/100))) as total_amount, 
                    SUM(sod_prod_cost * sod_prod_qty) as total_cost'))
                ->groupBy('sod_prod_code','sod_prod_name','br.name')
                //->join('branch__inventories as bri','sod_prod_code','=','bri.prod_code')
                ->join('so_headers as soh','soh.so_code','=','sod_code')
                ->join('branches as br','soh.branch_code','=','br.code')
                /*->join('branch__inventories as bri', function ($join) {
                    $join->on('bri.prod_code', '=', 'sod_prod_code');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })*/
                ->get();
            $br = Branch::where(['code' => @$request->branch])->first()->name." BRANCH";
        }
        $data = array();
        $total =  0;
        $tot_profit = 0;
        $total_cost = 0;
        foreach($sales as $sale){
            $profit = $sale->total_amount - $sale->total_cost;
            $less = Number_Format($sale->less, 0);
            $branch = ($sale->branch=="")?'All Branch':$sale->branch;

            $data[] = [
                'BRANCH' => $branch,
                'ITEM CODE' => $sale->sod_prod_code,
                'NAME' => $sale->sod_prod_name,
                'COST' => $sale->cost,
                'SRP' => $sale->srp,
                'QTY' => $sale->total_qty,
                'SALES PRICE' => $sale->price,
                'LESS' => $less,
                'TOTAL COST' => $sale->total_cost,
                'SALES' => $sale->total_amount,
                'PROFIT' => $profit,
            ];
            $total_cost += $sale->total_cost;
            $total += $sale->total_amount;
            $tot_profit += $profit;
        }

        return Excel::create('Taurus Profit Margin Report', function($excel) use ($data, $total, $tot_profit, $total_cost, $br, $from, $to) {
            $excel->setTitle('Taurus Profit Margin Report');
            $excel->sheet('Profit Margin Report', function($sheet) use ($data, $total, $tot_profit, $total_cost, $br, $from, $to)
            {
                $sheet->setColumnFormat(array(
                    'D' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'K' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Profit Margin Report $br $from - $to"]);
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
                    'Total Cost: '.Number_Format($total_cost,2).' ----'.'Total Sales: '.Number_Format($total,2).' ---- Total Profit: '.Number_Format($tot_profit,2)
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