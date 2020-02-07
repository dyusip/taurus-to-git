<?php

namespace App\Http\Controllers;

use App\Supplier;
use Illuminate\Http\Request;
use Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;

class SupplierController extends Controller
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
        if(Supplier::count()<1){
            $num = "TR-SUP00001";
        }else{
            $num = Supplier::max('code');
            ++$num;
        }
        return view('Purchasing.supplier.create',compact('num'));
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
        $this->validate($request, [
            'code' => 'required|string|unique:suppliers',
        ],['The supplier code has already been taken. Please refresh the page']);
        Supplier::create($request->all());
        Activity::log("Created supplier {$request['name']}", Auth::user()->id);
        return redirect('/supplier/create')->with('status', " ".strtoupper($request['name'])."'s account successfully created");
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
        $suppliers = Supplier::select('*');

        return Datatables::of($suppliers) ->addColumn('action', function ($suppliers) {
            return new HtmlString('<a href="#" class="text-success" id="btn-edit"  data-id='.$suppliers->id.'><i class="fa fa-edit"></i></a>
                                                    <a href="#modal-br-status" class="text-danger" id="btn-delete" data-id='.$suppliers->id.' data-toggle="modal"><i class="fa fa-remove"></i></a>');
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
        $suppliers = Supplier::FindOrFail($id);
        return $suppliers;

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
        $inventory = Supplier::findOrFail($id);
        $inventory->update($request->all());
        Activity::log("Updated supplier {$request['name']}", Auth::user()->id);
        return redirect('/supplier/create')->with('status', " ".strtoupper($request['name'])." successfully updated");
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
}
