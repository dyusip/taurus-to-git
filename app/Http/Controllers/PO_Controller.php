<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\Inventory;
use App\PoDetail;
use App\PoHeader;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Activity;

class PO_Controller extends Controller
{
    //
    public function index()
    {
        //
        if(PoHeader::count()<1){
            $num = "TR-PO00001";
        }else{
            $num = PoHeader::max('po_code');
            ++$num;
        }
        //$inventories = Inventory::where(['status' => 'AC'])->get();
        ///$inventories = Branch_Inventory::with('inventory')->where(['branch_code' => Auth::user()->branch])->get();
        $inventories = Branch_Inventory::whereHas('inventory', function ($query) {
            $query->where(['branch_code' => Auth::user()->branch]);
        })->get();
        return view('Purchasing.po.create',compact('inventories','num'));
    }
    public function show_item($id)
    {
        //
        //$item = Inventory::where(['code' => $id])->firstOrFail();
       /* $item = Inventory::with('branch_inventory')->whereHas('branch_inventory', function($query) use ($id){
            $query->where(['prod_code' => $id, 'branch_code' => Auth::user()->branch]);
        })->firstOrFail();*/
       /* $item =Inventory::whereHas('branch_inventory', function ($query) use ($id){
            $query->where(['prod_code' => $id, 'branch_code' => Auth::user()->branch]);
        })->firstOrFail();*/
        $item = Branch_Inventory::with('inventory')->where(['prod_code' => $id, 'branch_code' => Auth::user()->branch])->firstOrFail();
        return $item;
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'po_code' => 'required|string|unique:po_headers',
        ],['The po code has already been taken. Please refresh the page']);
        $create = PoHeader::create($request->all());
        foreach ($request->prod_code as $item => $value){
            $create->po_detail()->create([
                'pod_code' => $request->po_code,
                'prod_code' => $request->prod_code[$item],
                'prod_name' => $request->prod_name[$item],
                'prod_uom' => $request->uom[$item],
                'prod_qty' => $request->qty[$item],
                'prod_price' => $request->cost[$item],
                'prod_less' => $request->less[$item],
                'prod_amount' => $request->amount[$item],
            ]);
        }
        Activity::log("Created PO# $request->po_code", Auth::user()->id);
        return redirect('/po/create')->with('status', "PO# ".strtoupper($request->po_code)." successfully created.");
    }
    public function printPOindex()
    {
        return view('Purchasing.po.print');
    }
    public function searchPO()
    {
        $pos = PoHeader::where(['status' => 'AP'])->orWhere(['status' => 'OP'])->orWhere(['status' => 'CL'])->get();
        $row = array();
        foreach ($pos as $po){
            $row[] = $po->po_code ." - ". $po->sup_name;
        }
        $json = array('DRNO' => $row);
        return json_encode($json);
    }
   /* public function printPO(Request $request)
    {
        $fpdf = new Fpdf();
        $fpdf->AddPage();
        $fpdf->SetFont('Courier', 'B', 18);
        $fpdf->Cell(50, 25, $request->PO_No);
        $fpdf->Output();
        exit();
    }*/
}
