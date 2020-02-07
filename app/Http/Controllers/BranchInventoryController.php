<?php

namespace App\Http\Controllers;

use App\BiReplicate;
use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use App\PriceChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Validator;
use Yajra\Datatables\Datatables;
use Activity;
use Excel;

class BranchInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        /*$branch = Branch::findOrFail($id);
        return $branch->inventory;*/
        //$inventories = Branch_Inventory::all();
        $products = Inventory::where(['status' => 'AC'])->get();
        $branches = Branch::where(['status' => 'AC'])->get();
        return view('Purchasing.branch_inventory.create',compact('branches','products'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        Validator::extend('uniqueBranchAndProduct', function ($attribute, $value, $parameters, $validator) {
            $count = Branch_Inventory::where('prod_code', $value)
                ->where('branch_code', $parameters[0])
                ->count();

            return $count === 0;
        },'This branch already have this product.');

        $this->validate($request,[
            'prod_code' => "uniqueBranchAndProduct:{$request->branch_code}"
        ]);
        $branch = Branch::where(['code'=>$request->branch_code])->first();
        $prod = Inventory::where(['code'=>$request->prod_code])->first();
        $item = Branch_Inventory::where(['prod_code'=>$request->prod_code])->first();

        //Branch_Inventory::create($request->all());
        Branch_Inventory::create([
            'branch_code' => $request->branch_code,
            'prod_code'   => $request->prod_code,
            'cost'        => $item->cost,
            'price'       => $item->price,
        ]);
        Activity::log("Created a product $prod->name for $branch->name", Auth::user()->id);
        return redirect('/branch_inventory/create')->with('status', "Product $prod->name successfully created for $branch->name");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        //$inventories = Branch_Inventory::where(['branch_code' => $id])->select(['prod_code','cost','quantity','price']);
       /* $inventories = Inventory::whereHas('branch_inventory', function ($query) {
             $query->where(['branch_code' => 'TR-BR00001']);

        });*/
       //['branch__inventories.prod_code','inventories.name','branch__inventories.cost','branch__inventories.quantity','branch__inventories.price','branch__inventories.branch_code','inventories.id']
        $inventories = Branch_Inventory::join('inventories','inventories.code','=','branch__inventories.prod_code')->where(['branch_code' => $id])->select('*');

        /*if($inventories->count()==0){
            $inventories->firstOrFail();
        }*/
        return Datatables::of($inventories) ->addColumn('action', function ($inventories) {
            return new HtmlString('<!--<a href="#" class="text-success" id="btn-edit" data-id='.$inventories->id.' data-branch='.$inventories->branch_code.' data-prod='.$inventories->prod_code.'><i class="fa fa-edit"></i></a>*/-->
            <a href="#modal-br-status" data-toggle="modal" class="text-danger" id="btn-delete" data-id='.$inventories->id.' data-branch='.$inventories->branch_code.' data-prod='.$inventories->prod_code.' data-name="'.$inventories->name.'"><i class="fa fa-remove"></i></a>');
        })->make();


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        //
        $inventory = Branch_Inventory::where(['prod_code' => $request->product, 'branch_code' => $request->branch])->firstOrFail();
        return $inventory;


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        if($request->status=="DELETE"){
            $inventory = Branch_Inventory::where(['prod_code' => $request->prod_code, 'branch_code' => $request->branch_code]);
            $inventory->delete();
            Activity::log("Deleted a product $request->prod_code for $request->branch_code", Auth::user()->id);
            return redirect('/branch_inventory/create')->with('status', "Product $request->prod_name successfully deleted");
        }else{
            $inventory = Branch_Inventory::where(['prod_code' => $request->prod_code, 'branch_code' => $request->branch_code]);
            $inventory->update(['cost' => $request->cost, 'price' => $request->price, 'quantity' => $request->quantity]);
            Activity::log("Updated a product $request->prod_code for $request->branch_code", Auth::user()->id);
            return redirect('/branch_inventory/create')->with('status', "Product successfully updated");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        //$item = Branch_Inventory::where(['branch_code'=> $id])->with('inventory')->orderBy('name')->get();
        /*$items = Branch_Inventory::where(['branch_code'=> $id])->whereHas('inventory', function ($query) {
            $query->where(['status' => 'AC'])->orderBy('name');
        })->get();
         $cats = Inventory::select('desc')->distinct()->groupBy('desc')->where(['status' => 'AC'])
            ->whereHas('branch_inventory', function ($query) use($id){
                $query->where(['branch_code'=> $id]);
            })->get();
        $data = array();
        foreach ($cats as $cat) {
            $data[] = [$cat->desc];
            foreach ($items as $item) {
               if($cat->desc == $item->inventory->desc) {
                    array_push($data,[
                        "" => "",
                        'code' => $item->prod_code,
                        'name' => $item->inventory->name,
                        'desc' => $item->inventory->desc,
                        //'cost' => $item->cost,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        //'actual' => ''
                    ]);
                }
                $branch = $item->branch->name;
            }
        }
        return Excel::create("Taurus $branch Inventory", function($excel) use ($data, $branch) {
            $excel->setTitle("Taurus $branch Inventory");
            $excel->sheet('Sales-Inventory', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                $sheet->prependRow(1, ["Taurus Inventory Report"]);
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
                $sheet->prependRow(3, ["","CODE","NAME","DESC","QUANTITY","PRICE"]);
                //$sheet->prependRow(3, ["","CODE","NAME","DESC","COST","PRICE"]);
                //$sheet->prependRow(3, ["","CODE","NAME","DESC","QUANTITY","ACTUAL"]);

            });
        })->download('xlsx');*/
        /*$items = Branch_Inventory::where(['branch_code'=> $id])->select(DB::raw('branch_code,SUM(cost) as total_cost, SUM(price) as total_price'))->groupBy( 'branch_code')->get();
        foreach ($items as $key){
            echo $key->branch_code." - ".Number_Format($key->total_cost, 2)." - ".Number_Format($key->total_price,2)."<br>";
        }*/

        //No cost and price
        $items = Branch_Inventory::where(['branch_code'=> $id])->whereHas('inventory', function ($query) {
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
                'code' => $item->prod_code,
                'name' => $item->inventory->name,
                'desc' => $item->inventory->desc,
                'cost' => $item->cost,
                'quantity' =>$item->quantity,
                'price' => $item->price,
            ];
            //$branch = $item->branch->name;
        }
        $branch = "";
        return Excel::create("Taurus $branch Inventory", function($excel) use ($data, $branch) {
            $excel->setTitle("Taurus $branch Inventory");
            $excel->sheet('Sales-Inventory', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
                $sheet->prependRow(1, ["Taurus Inventory Report"]);
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
    public function replicate(Request $request)
    {
        //
        Validator::make($request->all(), [
            'branch_code' => 'required|string',
            'branch_to' => 'required|string|different:branch_code',
        ],['You cannot transfer item to the same branch.'])->validate();
        $from = Branch_Inventory::where(['branch_code'=>$request->branch_code])->get();

        foreach ($from as $to){
            /*Branch_Inventory::firstOrCreate([
                'branch_code' => $request->branch_to,
                'prod_code'   => $to->prod_code],
                ['price'       => $to->price,
                'cost'        => $to->cost
            ]);*/
            Branch_Inventory::where([
                'prod_code'   => $to->prod_code])
                //->where('cost','!=', $to->cost)
                ->where(function ($query) use ($to) {
                    $query->where('cost', '!=', $to->cost)
                        ->orWhere('price', '!=', $to->price);
                })
                ->update([
                    'price'       => $to->price,
                    'cost'        => $to->cost
                ]);
        }
        //return $request->all();
        $branch = Branch::where(['code' => $request->branch_to])->firstOrFail();
        Activity::log("Replicated inventory from $request->branch_code to $request->branch_to", Auth::user()->id);
        return redirect('/branch_inventory/create')->with('status_', "Product successfully replicated to $branch->name");
    }
}