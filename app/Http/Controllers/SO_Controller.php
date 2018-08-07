<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\SoHeader;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SO_Controller extends Controller
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
        if(SoHeader::count()<1){
            $num = "TR-SO00001";
        }else{
            $num = SoHeader::max('so_code');
            ++$num;
        }
        $salesmans = User::where(['status' => 'AC', 'branch' => Auth::user()->branch, 'position' => 'SALESMAN'])->get();
        $mechanics = User::where(['status' => 'AC', 'branch' => Auth::user()->branch, 'position' => 'MECHANIC'])->get();
        $inventories = Branch_Inventory::where(['branch_code' => Auth::user()->branch])->get();
        return view('Salesman.so.create',compact('salesmans','mechanics','inventories','num'));
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
            'so_code' => 'required|string|unique:so_headers',
        ],['The so code has already been taken. Please refresh the page']);
        $create = SoHeader::create($request->all());
        foreach ($request->prod_code as $item => $value){
            $create->so_detail()->create([
                'sod_code' => $request->so_code,
                'sod_prod_code' => $request->prod_code[$item],
                'sod_prod_name' => $request->prod_name[$item],
                'sod_prod_uom' => $request->uom[$item],
                'sod_prod_qty' => $request->qty[$item],
                'sod_prod_price' => $request->price[$item],
                'sod_less' => $request->less[$item],
                'sod_prod_amount' => $request->amount[$item],
            ]);
            $inventory = Branch_Inventory::where(['prod_code'=> $request->prod_code[$item], 'branch_code'=>$request->branch_code]);
            $inventory->update(['quantity'=> DB::raw('quantity - '.$request->qty[$item])]);
        }
        return redirect('/so/create')->with('status', "SO# ".strtoupper($request->so_code)." successfully created.");
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
        $item = Branch_Inventory::with('inventory')->where(['prod_code' => $id, 'branch_code' => Auth::user()->branch])->firstOrFail();
        return $item;
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
