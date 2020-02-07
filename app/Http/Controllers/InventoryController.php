<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\PriceChange;
use Illuminate\Http\Request;
use App\Inventory;
use App\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Excel;
use Activity;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('errors.404');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if(Inventory::count()<1){
            $num = "TR-ITM00001";
        }else{
            $num = Inventory::max('code');
            ++$num;
        }
        //$inventories = Inventory::all();
        return view('Purchasing.inventory.create',compact('num'));
        /*$items = Branch_Inventory::where(['branch_code'=>'TR-BR00001'])->where(['quantity' => 0])
            //->join('so_details as sod','sod.sod_prod_code','=','prod_code')
            //->select(DB::raw('SUM(sod.sod_prod_qty) as total_qty, prod_code, sod_prod_name, sod_prod_uom, quantity, cost, price'))
            //->groupBy('prod_code','sod_prod_name','sod_prod_uom','quantity','cost','price')
            ->select('*')->with('inventory')
            ->selectSub(function ($query)  {
                $query->from('so_details as sod')
                    ->selectRaw('sum(sod_prod_qty)')
                    //->where('bri.branch_code', '=', 'TR-BR00001')
                    ->whereRaw('`sod`.`sod_prod_code` = `prod_code`')->groupBy('prod_code');

            }, 'total_qty')
            ->get();
      
        $data = array();
        foreach ($items as $item) {
            $data []= [
                'Item #' => $item->prod_code,
                'Name' => $item->inventory->name,
                'UOM' => $item->inventory->uom,
                'QTY' => "$item->quantity",
                'MOVEMENT ANALYSIS (Jan - Jun)' => "$item->total_qty",
                'COST' => $item->cost,
                'SRP' => $item->price
            ];
        }

        return Excel::create('Taurus Inventory', function($excel) use ($data) {
            $excel->setTitle('Taurus Inventory');
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
                //$sheet->prependRow(3, ["","CODE","NAME","DESC","COST","PRICE"]);


            

            });
        })->download('xlsx');*/
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
        if(Inventory::count()<1){
            $num = "TR-ITM00001";
        }else{
            $num = Inventory::max('code');
            ++$num;
        }
        $request->merge(['code' => $num]);
        Inventory::create($request->all());
        $branches = Branch::where(['status' => 'AC'])->where('code', '!=' , 'TR-BR00012')->get();
        foreach($branches as $branch){
            Branch_Inventory::firstOrCreate([
                'branch_code' => $branch->code,
                'prod_code'   => $request->code,
                'cost'        => $request->cost,
                'quantity'    => 0,
                'price'       => $request->price,
            ]);
        }
        Activity::log("Created a product {$request['name']}", Auth::user()->id);
        return redirect('/inventory/create')->with('status', " ".strtoupper($request['name'])."'s account successfully created");
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
        $inventories = Inventory::where(['status' => 'AC'])->select('*');

        return Datatables::of($inventories) ->addColumn('action', function ($inventories) {
            return new HtmlString('<a href="#" class="text-success" id="btn-edit"  data-id='.$inventories->id.'><i class="fa fa-edit"></i></a>
                                                    <a href="#modal-br-status" class="text-danger" id="btn-delete" data-id='.$inventories->id.' data-toggle="modal"><i class="fa fa-remove"></i></a>');
        })->make();
    }
    public function inactive_list()
    {
        //
        $inventories = Inventory::where(['status' => 'IN'])->select('*');

        return Datatables::of($inventories) ->addColumn('action', function ($inventories) {
            return new HtmlString('<a href="#" class="text-success" id="btn-edit"  data-id='.$inventories->id.'><i class="fa fa-edit"></i></a>
                                                    <a href="#modal-br-status" class="text-danger" id="btn-delete" data-id='.$inventories->id.' data-toggle="modal"><i class="fa fa-remove"></i></a>');
        })->make();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $inventory = Inventory::findOrFail($id);

        return ['inventory' => $inventory, 'branch_inv' => $inventory->branch_inventory()->first()];
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
        $inventory = Inventory::findOrFail($id);
        $inventory->update($request->all());
        if(isset($request->status)){
            $remarks = ($request->status == 'AC')?"Successfully Activated product {$inventory->code}":"Successfully Deactivated product {$inventory->code}";
        }else{
            $br_inv = Branch_Inventory::where(['prod_code' => $inventory->code]);
            $dt = date('Y-m-d');
            if($request->cost > $br_inv->first()->cost)
            {
                PriceChange::create([
                    'pc_branch_code' => Auth::user()->branch,
                    'pc_prod_code' => $inventory->code,
                    'pc_cost' => $request->cost,
                    'pc_srp' => 0,
                    'pc_date' => $dt]);
            }
            if($request->price > $br_inv->first()->price)
            {
                PriceChange::create([
                    'pc_branch_code' => Auth::user()->branch,
                    'pc_prod_code' => $inventory->code,
                    'pc_cost' => 0,
                    'pc_srp' => $request->price,
                    'pc_date' => $dt]);
            }
            // ->update(['cost' => $request->cost, 'price' => $request->price]);
            $remarks = "Updated product {$request['name']}";
        }
        Activity::log($remarks, Auth::user()->id);
        return redirect('/inventory/create')->with('status', " ".strtoupper($request['name'])." successfully updated");
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
        $items = Inventory::where(['status' => 'AC'])
            ->join('branch__inventories as bri','bri.prod_code','=','code')
            ->select('code','name','desc','bri.cost','bri.price')
            ->groupBy('code','name','desc','cost','price')
            ->orderBy('name','asc')->get();
        $cats = Inventory::select('desc')->distinct()->groupBy('desc')->where(['status' => 'AC'])->get();
        $data = array();
        foreach ($cats as $key){
            $data[] = [$key->desc];
            foreach ($items as $item)
            {
                if($key->desc == $item->desc){
                    array_push($data,[
                        "" => "",
                        'code' => $item->code,
                        'name' => $item->name,
                        'desc' => $item->desc,
                        'cost' => $item->cost,
                        //'quantity' => '',
                        'price' => $item->price
                    ]);
                }

            }
        }
        //$items = Inventory::where(['status' => 'AC'])->with('branch_inventory')->first()->orderBy('name','asc')->get();
        /*$items = Inventory::where(['status' => 'AC'])->orderBy('name','asc')->whereHas('branch_inventory' , function($query) {
            //$query->where('cost','=','0')->orWhere('price','=','0');
            $query->where(function ($query)  {
                $query->where(['cost' => 0])
                    ->orWhere(['price' => 0]);
            });
        })->get();
        //$cats = Inventory::select('desc')->distinct()->groupBy('desc')->where(['status' => 'AC'])->get();
        $cats = Inventory::select('desc')->distinct()->groupBy('desc')->where(['status' => 'AC'])->whereHas('branch_inventory' , function($query) {
            $query->where(function ($query)  {
                $query->where(['cost' => 0])
                    ->orWhere(['price' => 0]);
            });
        })->get();*/
        /*$data = array();
        foreach ($cats as $cat) {
            $data[] = [$cat->desc];
            foreach ($items as $item) {
                if($cat->desc == $item->desc) {
                    foreach ($item->branch_inventory as $key) {
                        $price = $key->price;
                        $cost = $key->cost;
                    }
                    array_push($data,[
                        "" => "",
                        'code' => $item->code,
                        'name' => $item->name,
                        'desc' => $item->desc,
                        'cost' => $cost,
                        //'quantity' => '',
                        'price' => $price
                    ]);
                }
            }
        }*/

        return Excel::create('Taurus Inventory', function($excel) use ($data) {
            $excel->setTitle('Taurus Inventory');
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
                $sheet->prependRow(3, ["","CODE","NAME","DESC","COST","PRICE"]);


                /*$sheet->setMergeColumn(array(
                    'columns' => array('A','B','C','D'),
                    'rows' => array(
                        array(2,3),
                        array(5,11),
                    )
                ));*/

            });
        })->download('xlsx');
    }
}