<?php

namespace App\Http\Controllers\InvAnalysis;

use App\Branch;
use App\Branch_Inventory;
use App\SoDetail;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class InvAnaylsisController extends Controller
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

        return view($this->position().'.inventory_analysis.inventory_analysis',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            //$sales = SoDetail::whereHas('so_header')->whereBetween('so_date', [$from, $to])->distinct()->get();
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to)  {
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')->orderBy('qty','desc')->get();
            $items = array();
            foreach ($sales as $sale){
                $items[] = $sale->sod_prod_code;
            }
            $inventories = Branch_Inventory::whereIn('prod_code' , $items)->select(DB::raw('sum(quantity) as totalqty, prod_code'))->groupBy('prod_code')->get();
        }elseif($request->optCustType == "branch"){
            $branch = $request->branch;
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')->orderBy('qty','desc')->get();
            $items = array();
            foreach ($sales as $sale){
                $items[] = $sale->sod_prod_code;
            }
            $inventories = Branch_Inventory::where(['branch_code' => $branch])->whereIn('prod_code' , $items)->select(DB::raw('sum(quantity) as totalqty, prod_code'))->groupBy('prod_code')->get();
        }
        $branches = Branch::where(['status'=>'AC'])->get();

        //return $sales;
        return view($this->position().'.inventory_analysis.inventory_analysis',compact('sales','request', 'branches','inventories'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            //$sales = SoDetail::whereHas('so_header')->whereBetween('so_date', [$from, $to])->distinct()->get();
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to)  {
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')->orderBy('qty','desc')->get();
            $items = array();
            foreach ($sales as $sale){
                $items[] = $sale->sod_prod_code;
            }
            $inventories = Branch_Inventory::whereIn('prod_code' , $items)->select(DB::raw('sum(quantity) as totalqty, prod_code'))->groupBy('prod_code')->get();
        }elseif($request->optCustType == "branch"){
            $branch = $request->branch;
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')->orderBy('qty','desc')->get();
            $items = array();
            foreach ($sales as $sale){
                $items[] = $sale->sod_prod_code;
            }
            $inventories = Branch_Inventory::where(['branch_code' => $branch])->whereIn('prod_code' , $items)->select(DB::raw('sum(quantity) as totalqty, prod_code'))->groupBy('prod_code')->get();
        }
        $data = array();
        foreach($sales as $sale){
            foreach( $inventories as $inventory){
                if($inventory->prod_code == $sale->sod_prod_code) {
                    $onhand = $inventory->totalqty;
                }
            }

            $data[] = [
                'ITEM CODE' => $sale->sod_prod_code,
                'NAME' => $sale->sod_prod_name,
                'UOM' => $sale->sod_prod_uom,
                'STOCKS' => $onhand,
                'SOLD' => $sale->qty,
            ];
        }
        return Excel::create('Taurus Inventory Analysis', function($excel) use ($data) {
            $excel->setTitle('Taurus Inventory Analysis');
            $excel->sheet('Inventory Analysis', function($sheet) use ($data)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Inventory Analysis"]);
                $sheet->mergeCells("A1:E1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
            });
        })->download('xlsx');
    }
}
