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
}
