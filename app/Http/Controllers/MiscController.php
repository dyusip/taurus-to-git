<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Branch_Inventory;
use App\MiscHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Activity;

class MiscController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
    //
    public function index()
    {
        if(MiscHeader::count()<1){
            $num = "TR-MS00001";
        }else{
            $num = MiscHeader::max('msh_code');
            ++$num;
        }
        $branches = Branch::where(['status' => 'AC'])->get();
        return view($this->position().'.misc.create',compact('branches','num'));
    }
    public function show_branch($id)
    {
        $info = Branch::where(['code' => $id])->firstOrFail();
        return $info;
    }
    public function show_item($id)
    {
        $info = Branch_Inventory::with('inventory')->where(['branch_code' =>  $id]);
        return $info->get();
    }
    public function item_data($id,$code)
    {
        $item = Branch_Inventory::with('inventory')->where(['prod_code' => $id, 'branch_code' => $code])->firstOrFail();
        return $item;
    }
    public function store(Request $request)
    {
        if(MiscHeader::count()<1){
            $num = "TR-MS00001";
        }else{
            $num = MiscHeader::max('msh_code');
            ++$num;
        }
        $request->merge(['msh_code' => $num]);
        $create = MiscHeader::create($request->all());
        foreach ($request->prod_code as $item => $value){
            $create->misc_detail()->create([
                'msd_code' => $request->msh_code,
                'msd_prod_code' => $request->prod_code[$item],
                'msd_prod_name' => $request->prod_name[$item],
                'msd_prod_uom' => $request->uom[$item],
                'msd_prod_cost' => $request->cost[$item],
                'msd_prod_qty' => $request->misc_qty[$item],
                'msd_upd_qty' => $request->qty[$item],
                'msd_prod_price' => $request->price[$item],
                'msd_prod_amount' => $request->amount[$item],
                'msd_remarks' => $request->remarks[$item],
            ]);
            Branch_Inventory::where(['prod_code' => $request->prod_code[$item], 'branch_code' => $request->msh_branch_code])
            ->update(['quantity' => $request->qty[$item]]);
        }
        Activity::log("Created Miscellaneous Transaction # $request->msh_code", Auth::user()->id);
        return redirect('/miscellaneous')->with('status', "MS# ".strtoupper($request->msh_code)." successfully created.");
    }
}