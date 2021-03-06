<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\SoDetail;
use App\SoHeader;
use App\SrHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Activity;

class SR_Controller extends Controller
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
        if(SrHeader::count()<1){
            $num = "TR-SR00001";
        }else{
            $num = SrHeader::max('sr_code');
            ++$num;
        }
        $sos = SoHeader::where(['branch_code' => Auth::user()->branch])->get();
        return view('Salesman.sr.create',compact('sos','num'));
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
            'sr_code' => 'required|string|unique:sr_headers',
        ],['The so code has already been taken. Please refresh the page']);
        $create = SrHeader::create($request->all());
        foreach ($request->prod_code as $item => $value){
            $create->sr_detail()->create([
                'srd_code' => $request->sr_code,
                'srd_prod_code' => $request->prod_code[$item],
                'srd_prod_name' => $request->prod_name[$item],
                'srd_prod_uom' => $request->uom[$item],
                'srd_prod_qty' => $request->qty[$item],
                'srd_prod_price' => $request->price[$item],
                'srd_less' => $request->less[$item],
                'srd_prod_amount' => $request->amount[$item],
                'status' => $request->status[$item],
                'srd_prod_cost' => $request->prod_cost[$item],
                'srd_prod_srp' => $request->prod_srp[$item],
            ]);
            $so_detail = SoDetail::where(['sod_prod_code' => $request->prod_code[$item], 'sod_code' => $request->so_code]);
            $so_detail->update(['sod_prod_qty' => DB::raw('sod_prod_qty - ' . $request->qty[$item])]);
            $new_qty = $so_detail->first()->sod_prod_qty;
            $sod_amount = ($new_qty * $request->price[$item] - (($new_qty * $request->price[$item]) * ($request->less[$item]/100)));
            SoDetail::where(['sod_prod_code' => $request->prod_code[$item], 'sod_code' => $request->so_code])
                ->update(['sod_prod_amount' => $sod_amount]);
            if($request->status[$item]=='SR') {
                $inventory = Branch_Inventory::where(['prod_code' => $request->prod_code[$item], 'branch_code' => $request->branch_code]);
                $inventory->update(['quantity' => DB::raw('quantity + ' . $request->qty[$item])]);
            }
        }
        $amount = SoDetail::where(['sod_code' => $request->so_code])
            ->select(DB::raw('SUM(sod_prod_price * sod_prod_qty) as total_amount'))
            ->groupBy('sod_code')->first();
        SoHeader::where(['so_code' => $request->so_code])->update(['so_amount' => $amount->total_amount]);
        Activity::log("Returned SO # $request->so_code", Auth::user()->id);
        return redirect('/sr/create')->with('status', "SO# ".strtoupper($request->so_code)." successfully returned.");
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
        $so = SoHeader::where(['so_code' => $id])->firstOrFail();
        $mechanic = null;
        if (!is_null($so->so_mechanic)) {
            // Load the relation
            $mechanic = $so->mechanic->name;
        }
        $salesman = (!is_null($so->salesman))?$so->salesman->name:null;
        return json_encode(['header' => $so, 'detail' => $so->so_detail, 'salesman' => $salesman, 'mechanic' => $mechanic]);
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