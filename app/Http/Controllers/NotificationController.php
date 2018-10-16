<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\PoHeader;
use App\TransferHeaders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Activity;
class NotificationController extends Controller
{
    //
    public function po_index(){
        $pos = PoHeader::where(['status' => 'PD'])->get();
        return view('Management.Approve.po',compact('pos'));
    }
    public function show_po($id){
        $po = PoHeader::where(['po_code' => $id])->firstOrFail();
        //return view('Management.Approve.approve',compact('po'));
        return json_encode(['header'=>$po, 'detail'=>$po->po_detail]);
    }
    public function save_po(Request $request){
        $app_po = PoHeader::where(['po_code' => $request->PONo]);
        if($app_po->count()>1){
            $app_po->firstOrFail();
        }
        $app_po->update(['status' => $request->status, 'po_appby' => Auth::user()->username]);
        $status = ($request->status=='AP')?'Approved':'Disapproved';
        Activity::log("$status PO # $request->PONo", Auth::user()->id);
        return redirect('/notification/po')->with('status', "PO# $request->PONo successfully $status");
    }
    //Transfer
    public function tf_index(){
        $tfs = TransferHeaders::where(['tf_status' => 'PD'])->get();
        return view('Management.Approve.transfer',compact('tfs'));
    }

    public function show_tf($id){
        $tf = TransferHeaders::where(['tf_code' => $id])->firstOrFail();
        return json_encode(['to_branch'=>$tf->tf_to_branch,'fr_branch'=>$tf->tf_fr_branch->name,'header'=>$tf, 'detail'=>$tf->tf_detail]);
    }
    public function save_tf(Request $request){
        $app_tf = TransferHeaders::where(['tf_code' => $request->tf_code]);
        if($app_tf->count()>1){
            $app_tf->firstOrFail();
        }
        $app_tf->update(['tf_status' => $request->status, 'tf_appby' => Auth::user()->username]);
        if($request->status=='AP'){
            foreach ($app_tf->firstOrFail()->tf_detail as $item) {
                $to_inv = Branch_Inventory::where(['prod_code'=> $item->tf_prod_code, 'branch_code'=>$app_tf->firstOrFail()->to_branch]);
                $to_inv->update(['quantity'=> DB::raw('quantity + '.$item->tf_prod_qty)]);;

                $fr_inv = Branch_Inventory::where(['prod_code'=> $item->tf_prod_code, 'branch_code'=>$app_tf->firstOrFail()->from_branch]);
                $fr_inv->update(['quantity'=> DB::raw('quantity - '.$item->tf_prod_qty)]);;
            }
        }
        $status = ($request->status=='AP')?'Approved':'Disapproved';
        Activity::log("$status PO # $request->PONo", Auth::user()->id);
        return redirect('/notification/transfer')->with('status', "Transfer# $request->tf_code successfully $status");
    }
}
