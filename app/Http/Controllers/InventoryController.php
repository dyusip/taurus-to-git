<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventory;
use App\Branch;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;
use Excel;

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
        Inventory::create($request->all());
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
        $inventory = Inventory::findOrFail($id);
        $inventory->update($request->all());
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
        $items = Inventory::where(['status' => 'AC'])->get();
        $data = array();
       foreach ($items as $item)
       {
           $data[] = [
               'name' => $item->name,
               'desc' => $item->desc,
               'cost' => '',
               'quantity' => '',
               'price' => ''
           ];
       }
        return Excel::create('Taurus Inventory', function($excel) use ($data) {
            $excel->setTitle('Taurus Inventory');
            $excel->sheet('Sales-Inventory', function($sheet) use ($data)
            {
                $sheet->fromArray($data);

            });
        })->download('xlsx');
    }
}
