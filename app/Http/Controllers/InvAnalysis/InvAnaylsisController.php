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
            /*$sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to)  {
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')->orderBy('qty','desc')->get();
            $items = array();
            foreach ($sales as $sale){
                $items[] = $sale->sod_prod_code;
            }
            $inventories = Branch_Inventory::whereIn('prod_code' , $items)->select(DB::raw('sum(quantity) as totalqty, prod_code'))->groupBy('prod_code')->get();*/
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to)  {
                $query->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')
                ->orderBy('qty','desc')
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        //->where('id', '=', 5)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'totalqty')
                ->get();
                if (Auth::user()->position == 'CEO') {
                $prod = array();
                foreach ($sales as $sale) {
                    $prod[] = $sale->sod_prod_code;
                }
                $not_sales = Branch_Inventory::whereNotIn('prod_code', $prod)
                    ->where('branch_code','!=','TR-BR00001')
                    ->join('inventories', 'code', '=', 'prod_code')
                    ->select(DB::raw('prod_code, name, uom, SUM(quantity) as qty'))
                    ->orderBy('qty','desc')
                    ->groupBy('prod_code', 'name', 'uom')->get();
            }
        }elseif($request->optCustType == "branch"){
            $branch = $request->branch;
            /*$sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')->orderBy('qty','desc')->get();
            $items = array();
            foreach ($sales as $sale){
                $items[] = $sale->sod_prod_code;
            }
            $inventories = Branch_Inventory::where(['branch_code' => $branch])->whereIn('prod_code' , $items)->select(DB::raw('sum(quantity) as totalqty, prod_code'))->groupBy('prod_code')->get();*/
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')
                ->orderBy('qty','desc')
                ->selectSub(function ($query) use ($branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        ->where('bri.branch_code', '=', $branch)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'totalqty')
                ->selectSub(function ($query) use ($branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri_1')
                        ->selectRaw('sum(quantity)')
                        ->where('bri_1.branch_code', '=', 'TR-BR00001')
                        ->whereRaw('`bri_1`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'cw_qty')
                ->selectSub(function ($query) use ($branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri_2')
                        ->selectRaw('sum(quantity)')
                        ->where('bri_2.branch_code', '!=', 'TR-BR00001')
                        ->where('bri_2.branch_code', '!=', $branch)
                        ->whereRaw('`bri_2`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'brs_qty')
                ->get();
                if (Auth::user()->position == 'CEO') {
                $prod = array();
                foreach ($sales as $sale) {
                    $prod[] = $sale->sod_prod_code;
                }
                $not_sales = Branch_Inventory::whereNotIn('prod_code', $prod)->where(['branch_code'=>$branch])
                    ->join('inventories', 'code', '=', 'prod_code')
                    ->select(DB::raw('prod_code, name, uom, SUM(quantity) as qty'))
                    ->orderBy('qty','desc')
                    ->groupBy('prod_code', 'name', 'uom')->get();
            }
        }
        $branches = Branch::where(['status'=>'AC'])->get();

        //return $sales;
        return view($this->position().'.inventory_analysis.inventory_analysis',compact('sales','request', 'branches','inventories','not_sales'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            //$sales = SoDetail::whereHas('so_header')->whereBetween('so_date', [$from, $to])->distinct()->get();
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to)  {
                $query->whereBetween('so_date', [$from, $to]);
            })
                ->join('so_headers as soh','soh.so_code','=','sod_code')
                ->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->orderBy('qty','desc')
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        //->where('id', '=', 5)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'totalqty')
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('bri.cost')
                       /* ->join('branches as br',function ($table){
                            $table->on('br.code','=','bri.branch_code');
                        })*/
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('cost')->take(1);

                }, 'sod_prod_cost')
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('bri.price')
                        /*->join('branches as br',function ($table){
                            $table->on('br.code','=','bri.branch_code');
                        })*/
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('price')->take(1);

                }, 'sod_prod_srp')
                ->get();
            $br = 'ALL BRANCH';
            if (Auth::user()->position == 'CEO' || Auth::user()->position == 'PURCHASING') {
                $prod = array();
                foreach ($sales as $sale) {
                    $prod[] = $sale->sod_prod_code;
                }
                $not_sales = Branch_Inventory::whereNotIn('prod_code', $prod)
                    ->where('branch_code','!=','TR-BR00001')
                    ->join('inventories', 'code', '=', 'prod_code')
                    ->select(DB::raw('prod_code, name, uom, SUM(quantity) as qty,cost,price'))
                    ->orderBy('qty','desc')
                    ->groupBy('prod_code', 'name', 'uom','cost','price')->get();
            }
        }elseif($request->optCustType == "branch"){
            $branch = $request->branch;
            $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
            })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom')
                ->orderBy('qty','desc')
                ->selectSub(function ($query) use ($branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        ->where('bri.branch_code', '=', $branch)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'totalqty')
                ->selectSub(function ($query) use ($branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('bri.cost')
                        ->where('bri.branch_code', '=', $branch)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('cost');

                }, 'sod_prod_cost')
                ->selectSub(function ($query) use ($branch) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('bri.price')
                        ->where('bri.branch_code', '=', $branch)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('price');

                }, 'sod_prod_srp')
                ->get();
            $br = Branch::where(['code' => @$branch])->first()->name." BRANCH";
            if (Auth::user()->position == 'CEO' || Auth::user()->position == 'PURCHASING') {
                $prod = array();
                foreach ($sales as $sale) {
                    $prod[] = $sale->sod_prod_code;
                }
                $not_sales = Branch_Inventory::whereNotIn('prod_code', $prod)->where(['branch_code'=>$branch])
                    ->join('inventories', 'code', '=', 'prod_code')
                    ->select(DB::raw('prod_code, name, uom, SUM(quantity) as qty,cost,price'))
                    ->orderBy('qty','desc')
                    ->groupBy('prod_code', 'name', 'uom','cost','price')->get();
            }
        }
        $data = array();
        foreach($sales as $sale){
            /*foreach( $inventories as $inventory){
                if($inventory->prod_code == $sale->sod_prod_code) {
                    $onhand = $inventory->totalqty;
                }
            }*/

            $data[] = [
                'ITEM CODE' => $sale->sod_prod_code,
                'NAME' => $sale->sod_prod_name,
                'UOM' => $sale->sod_prod_uom,
                'STOCKS' => $sale->totalqty,
                'SOLD' => $sale->qty,
                'COST' => $sale->sod_prod_cost,
                'SRP' => $sale->sod_prod_srp,
            ];
        }
        if (Auth::user()->position == 'CEO' || Auth::user()->position == 'PURCHASING') {
            foreach ($not_sales as $not_sale) {
                $data[] = [
                    'ITEM CODE' => $not_sale->prod_code,
                    'NAME' => $not_sale->name,
                    'UOM' => $not_sale->uom,
                    'STOCKS' => $not_sale->qty,
                    'SOLD' => "0",
                    'COST' => $not_sale->cost,
                    'SRP' => $not_sale->price,
                ];
            }
       }

        return Excel::create('Taurus Inventory Analysis', function($excel) use ($data, $br, $from, $to) {
            $excel->setTitle('Taurus Inventory Analysis');
            $excel->sheet('Inventory Analysis', function($sheet) use ($data, $br, $from, $to)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Inventory Analysis $br $from - $to"]);
                $sheet->mergeCells("A1:G1");
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
    public function show_items($id)
    {
         $items = Branch_Inventory::where(['prod_code' => $id])->with('branch')->with('inventory')->get();
         //return json_encode($items);
        return $items;
    }
}