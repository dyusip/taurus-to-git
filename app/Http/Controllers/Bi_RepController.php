<?php

namespace App\Http\Controllers;

use App\BiReplicate;
use App\Branch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;
use Excel;

class Bi_RepController extends Controller
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
        $branches = Branch::all();
        return view($this->position().'.bi_replicate.bi_replicate',compact('branches'));
    }
    public function show($date, $branch)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        $inventories = BiReplicate::join('inventories','inventories.code','=','bi_replicates.bir_prod_code')
            ->where(['bir_branch_code' => $branch, 'bir_date' => $date])->select('*');
        return Datatables::of($inventories) ->addColumn('action', function ($inventories) {
        })->make();
    }
    public function print_report(Request $request)
    {
        $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
        $items = BiReplicate::where(['bir_branch_code'=> $request->branch, 'bir_date' => $date])->whereHas('inventory', function ($query) {
            $query->where(['status' => 'AC'])->orderBy('name');
        })->get();
        $data = array();

        foreach ($items as $item) {
            /*foreach ($item->inventory as $key) {
                $price = $key->price;
                $cost = $key->cost;
                $quantity = $key->quantity;
            }*/
            $data[] = [
                'CODE' => $item->bir_prod_code,
                'NAME' => $item->inventory->name,
                'DESC' => $item->inventory->desc,
                'COST' => $item->bir_cost,
                'QUANTITY' =>$item->bir_quantity,
                'PRICE' => $item->bir_price,
            ];
            //$branch = $item->branch->name;
        }
        $branch = Branch::where(['code' => $request->branch])->first()->name;
        $dt = $request->date;
        return Excel::create("Taurus $branch Inventory Record", function($excel) use ($data, $branch, $dt) {
            $excel->setTitle("Taurus $branch Inventory Record");
            $excel->sheet('Branch-Inventory', function($sheet) use ($data, $branch, $dt)
            {
                $sheet->fromArray($data);
                $sheet->prependRow(1, ["Taurus $branch Inventory Report $dt"]);
                $sheet->mergeCells("A1:F1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
                //$sheet->prependRow(3, ["","CODE","NAME","DESC","QUANTITY","PRICE"]);
                //$sheet->prependRow(3, ["","CODE","NAME","DESC","COST","PRICE"]);

            });
        })->download('xlsx');
    }
}
