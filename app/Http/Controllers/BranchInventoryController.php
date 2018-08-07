<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Validator;
use Yajra\Datatables\Datatables;

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
        $inventories = Branch_Inventory::all();
        $products = Inventory::where(['status' => 'AC'])->get();
        $branches = Branch::where(['status' => 'AC'])->get();
        return view('Purchasing.branch_inventory.create',compact('branches','products','inventories'));

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

        Branch_Inventory::create($request->all());
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
            return new HtmlString('<a href="#" class="text-success" id="btn-edit" data-id='.$inventories->id.' data-branch='.$inventories->branch_code.' data-prod='.$inventories->prod_code.'><i class="fa fa-edit"></i></a>');
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
        $inventory = Branch_Inventory::where(['prod_code' => $request->prod_code, 'branch_code' => $request->branch_code]);
        $inventory->update(['cost' => $request->cost, 'price' => $request->price]);
        return redirect('/branch_inventory/create')->with('status', "Product successfully updated");

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
            Branch_Inventory::firstOrCreate([
                'branch_code' => $request->branch_to,
                'prod_code'   => $to->prod_code],
                ['price'       => $to->price,
                'cost'        => $to->cost
            ]);
        }
        //return $request->all();
        $branch =Branch::where(['code' => $request->branch_to])->firstOrFail();
        return redirect('/branch_inventory/create')->with('status_', "Product successfully replicated to $branch->name");
    }
}
