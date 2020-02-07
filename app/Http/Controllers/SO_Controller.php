<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\SoDetail;
use App\SoHeader;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Activity;

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
        /*foreach($inventories as $inventory){
            //echo $inventory->inventory->code." - ".$inventory->inventory->name."<br>";
            //echo $inventory->inventory->code."<br>";
            if(isset($inventory->inventory->code)){
                echo $inventory->inventory->code." - ".$inventory->inventory->name."<br>";
            }else{
                //echo "None<br>";
                echo $inventory->prod_code." - NONE<br>";
            }
        }*/
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
        /*$this->validate($request, [
            'so_code' => 'required|string|unique:so_headers',
        ],['The so code has already been taken. Please refresh the page']);*/
        if(SoHeader::count()<1){
            $num = "TR-SO00001";
        }else{
            $num = SoHeader::max('so_code');
            ++$num;
        }
        $request->merge(['so_code' => $num]);
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
                'sod_prod_cost' => $request->prod_cost[$item],
                'sod_prod_srp' => $request->prod_srp[$item],
            ]);
            $inventory = Branch_Inventory::where(['prod_code'=> $request->prod_code[$item], 'branch_code'=>$request->branch_code]);
            $inventory->update(['quantity'=> DB::raw('quantity - '.$request->qty[$item])]);
        }
        Activity::log("Created SO # $request->so_code", Auth::user()->id);
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
    public function show_item($id, $si)
    {
        //
        $item = Branch_Inventory::with('inventory')->where(['prod_code' => $id, 'branch_code' => Auth::user()->branch])->firstOrFail();
        $branch = Auth::user()->branch;
        $so = SoDetail::where(['sod_prod_code' => $id])->whereHas('so_header',function ($query) use($si, $branch){
            $query->where(['jo_code' => $si, 'branch_code' => $branch]);
        })->first();
        /*if($so->count() > 0){

        }else{
            $so ="";
        }*/
        return json_encode(['item' => $item,'inventory'=> $item->inventory,'so' => $so]);
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
