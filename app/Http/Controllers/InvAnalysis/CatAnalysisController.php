<?php

namespace App\Http\Controllers\InvAnalysis;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use App\SoDetail;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class CatAnalysisController extends Controller
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
        $categories = Inventory::where(['status'=>'AC'])->select('desc')->groupBy('desc')->get();

        return view($this->position().'.inventory_analysis.category_analysis',compact('branches','categories'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $category = $request->category;
        if($request->optCustType == "all"){
            if($request->brand == 'ALL') {
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to) {
                    $query->whereBetween('so_date', [$from, $to]);
                })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom, inv.desc,
                bri.cost,bri.price, sum(bri.cost * sod_prod_qty) as total_cost, sum(bri.price * sod_prod_qty) as total_srp'))
                    ->groupBy('sod_prod_code', 'sod_prod_name', 'sod_prod_uom', 'desc', 'cost', 'price')
                    ->orderBy('qty', 'desc')
                    ->join('inventories as inv', function ($join) use ($category) {
                        $join->on('inv.code', '=', 'sod_prod_code');
                        $join->where('inv.desc', '=', $category);
                    });
            }else{
                $brand = $request->brand;
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to) {
                    $query->whereBetween('so_date', [$from, $to]);
                })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom, inv.desc,
                bri.cost,bri.price, sum(bri.cost * sod_prod_qty) as total_cost, sum(bri.price * sod_prod_qty) as total_srp'))
                    ->groupBy('sod_prod_code', 'sod_prod_name', 'sod_prod_uom', 'desc', 'cost', 'price')
                    ->orderBy('qty', 'desc')
                    ->join('inventories as inv', function ($join) use ($category, $brand) {
                        $join->on('inv.code', '=', 'sod_prod_code');
                        $join->where('inv.desc', '=', $category)
                            //->where('name', 'like','%('.$brand.")%");
                        ->where('name', 'like',DB::raw('IF(substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)="","%'.$brand.'%","%('.$brand.')%")'));
                    });
            }
            $sales = $sales->join('so_headers as soh','soh.so_code','=','sod_code')
                ->join('branch__inventories as bri',function ($join){
                    $join->on('bri.prod_code','=','sod_prod_code');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        //->where('id', '=', 5)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'totalqty')
                ->get();
        }elseif($request->optCustType == "branch"){
            $branch = $request->branch;

            if($request->brand == 'ALL'){
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                    $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
                })->join('inventories as inv', function ($join) use($category) {
                    $join->on('inv.code', '=', 'sod_prod_code');
                    $join->where('inv.desc','=',$category);
                });
            }else{
                $brand = $request->brand;
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                    $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
                })->join('inventories as inv', function ($join) use($category, $brand) {
                    $join->on('inv.code', '=', 'sod_prod_code');
                    $join->where('inv.desc','=',$category)
                    //->where('name', 'like','%('.$brand.")%");
                    ->where('name', 'like',DB::raw('IF(substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)="","%'.$brand.'%","%('.$brand.')%")'));
                });
            }
            $sales = $sales->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom, inv.desc,
            bri.cost,bri.price, sum(bri.cost * sod_prod_qty) as total_cost, sum(bri.price * sod_prod_qty) as total_srp'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom', 'desc','cost','price')
                ->orderBy('qty','desc')
                ->join('branch__inventories as bri',function ($join) use($branch){
                    $join->on('bri.prod_code','=','sod_prod_code')
                        ->where('branch_code','=',$branch);
                })
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
        }
        $branches = Branch::where(['status'=>'AC'])->get();
        $categories = Inventory::where(['status'=>'AC'])->select('desc')->groupBy('desc')->get();
        //return $sales;
        return view($this->position().'.inventory_analysis.category_analysis',compact('sales','request', 'branches','inventories','categories'));
    }
    public function brand_show($id)
    {
        $items = Inventory::where(['desc' => $id])
            ->where('status','=','AC')
            ->select(DB::raw('IF(substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)="",LEFT(name, INSTR(name, " ") - 1),substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)) as brand'))
            ->groupBy(DB::raw('IF(substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)="",LEFT(name, INSTR(name, " ") - 1),substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1))'))
            ->get();
        /*$items2 = Inventory::where(['desc' => $id])
            ->where('status','=','AC')
            ->select(DB::raw('substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1) as brand'))
            ->groupBy(DB::raw('substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)'))
            ->get(); LEFT(name, INSTR(name, ' ') - 1) */
        return $items;
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $category = $request->category;
        if($request->optCustType == "all"){
            if($request->brand == 'ALL') {
                $brand = 'ALL';
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to) {
                    $query->whereBetween('so_date', [$from, $to]);
                })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom, inv.desc,
                bri.cost,bri.price, sum(bri.cost * sod_prod_qty) as total_cost, sum(bri.price * sod_prod_qty) as total_srp'))
                    ->groupBy('sod_prod_code', 'sod_prod_name', 'sod_prod_uom', 'desc', 'cost', 'price')
                    ->orderBy('qty', 'desc')
                    ->join('inventories as inv', function ($join) use ($category) {
                        $join->on('inv.code', '=', 'sod_prod_code');
                        $join->where('inv.desc', '=', $category);
                    });
            }else{
                $brand = $request->brand;
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to) {
                    $query->whereBetween('so_date', [$from, $to]);
                })->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom, inv.desc,
                bri.cost,bri.price, sum(bri.cost * sod_prod_qty) as total_cost, sum(bri.price * sod_prod_qty) as total_srp'))
                    ->groupBy('sod_prod_code', 'sod_prod_name', 'sod_prod_uom', 'desc', 'cost', 'price')
                    ->orderBy('qty', 'desc')
                    ->join('inventories as inv', function ($join) use ($category, $brand) {
                        $join->on('inv.code', '=', 'sod_prod_code');
                        $join->where('inv.desc', '=', $category)
                            //->where('name', 'like','%('.$brand.")%");
                            ->where('name', 'like',DB::raw('IF(substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)="","%'.$brand.'%","%('.$brand.')%")'));
                    });
            }
            $sales = $sales->join('so_headers as soh','soh.so_code','=','sod_code')
                ->join('branch__inventories as bri',function ($join){
                    $join->on('bri.prod_code','=','sod_prod_code');
                    $join->on('bri.branch_code','=','soh.branch_code');
                })
                ->selectSub(function ($query) {

                    /** @var $query \Illuminate\Database\Query\Builder */
                    $query->from('branch__inventories as bri')
                        ->selectRaw('sum(quantity)')
                        //->where('id', '=', 5)
                        ->whereRaw('`bri`.`prod_code` = `sod_prod_code`')->groupBy('prod_code');

                }, 'totalqty')
                ->get();
            $br = 'ALL BRANCH';
        }elseif($request->optCustType == "branch"){
            $branch = $request->branch;
            if($request->brand == 'ALL'){
                $brand = 'ALL';
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                    $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
                })->join('inventories as inv', function ($join) use($category) {
                    $join->on('inv.code', '=', 'sod_prod_code');
                    $join->where('inv.desc','=',$category);
                });
            }else{
                $brand = $request->brand;
                $sales = SoDetail::whereHas('so_header', function ($query) use ($from, $to, $branch)  {
                    $query->where(['branch_code' => $branch])->whereBetween('so_date', [$from, $to]);
                })->join('inventories as inv', function ($join) use($category, $brand) {
                    $join->on('inv.code', '=', 'sod_prod_code');
                    $join->where('inv.desc','=',$category)
                        //->where('name', 'like','%('.$brand.")%");
                        ->where('name', 'like',DB::raw('IF(substr(name,instr(name,"(") + 1, instr(name,")") - instr(name,"(") - 1)="","%'.$brand.'%","%('.$brand.')%")'));
                });
            }
            $sales = $sales->select(DB::raw('sum(sod_prod_qty) as qty, sod_prod_code, sod_prod_name, sod_prod_uom, inv.desc,
            bri.cost,bri.price, sum(bri.cost * sod_prod_qty) as total_cost, sum(bri.price * sod_prod_qty) as total_srp'))
                ->groupBy('sod_prod_code','sod_prod_name','sod_prod_uom', 'desc','cost','price')
                ->orderBy('qty','desc')
                ->join('branch__inventories as bri',function ($join) use($branch){
                    $join->on('bri.prod_code','=','sod_prod_code')
                        ->where('branch_code','=',$branch);
                })
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
            $br = Branch::where(['code' => @$branch])->first()->name." BRANCH";
        }
        $data = array();
        $ttl_cost = 0;
        $ttl_srp = 0;
        foreach($sales as $sale){
            /*foreach( $inventories as $inventory){
                if($inventory->prod_code == $sale->sod_prod_code) {
                    $onhand = $inventory->totalqty;
                }
            }*/

            $data[] = [
                'CATEGORY' => $sale->desc,
                'BRAND' => $brand,
                'ITEM CODE' => $sale->sod_prod_code,
                'NAME' => $sale->sod_prod_name,
                'UOM' => $sale->sod_prod_uom,
                'COST' => $sale->cost,
                'SRP' => $sale->price,
                'STOCKS' => $sale->totalqty,
                'SOLD' => $sale->qty,
                'TOTAL COST' => $sale->total_cost,
                'TOTAL SRP' => $sale->total_srp,
            ];
            $ttl_cost += $sale->total_cost;
            $ttl_srp += $sale->total_srp;
        }

        return Excel::create('Taurus Category Analysis', function($excel) use ($data, $br,$ttl_cost,$ttl_srp) {
            $excel->setTitle('Taurus Category Analysis');
            $excel->sheet('Category Analysis', function($sheet) use ($data, $br,$ttl_cost,$ttl_srp)
            {
                $sheet->setColumnFormat(array(
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'J' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'K' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));

                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Category Analysis $br"]);
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
                $sheet->cell("I".$footerRow, function ($cell)  {
                    $cell->setValue("TOTAL AMOUNT");
                });
                $sheet->cell("J".$footerRow, function ($cell) use($ttl_cost) {
                    $cell->setValue($ttl_cost);
                });
                $sheet->cell("K".$footerRow, function ($cell) use($ttl_srp) {
                    $cell->setValue($ttl_srp);
                });
                //$sheet->mergeCells("A{$footerRow}:K{$footerRow}");
                $sheet->cell("A{$footerRow}:K{$footerRow}", function ($cell) {
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
    /*public function show_items($id)
    {
        $items = Branch_Inventory::where(['prod_code' => $id])->with('branch')->with('inventory')->get();
        //return json_encode($items);
        return $items;
    }*/
}
