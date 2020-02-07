<?php

namespace App\Http\Controllers;

use App\PoHeader;
use App\PopaymentDetail;
use App\PopaymentHeader;
use App\ReceivingDetail;
use App\ReceivingHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Activity;

class PaymentController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }
        return $position;
    }
    public function index()
    {
        $pos = PoHeader::where('term','!=','Cash')->whereHas('po_re_header',function ($query){
            $query->where(['rh_status' => 'OP']);
        })->get();
        return view($this->position().'.payment.create',compact('pos'));
    }
    public function show($id)
    {
        /**/
        $payment = PopaymentHeader::where(['ph_rh_no' => $id]);
        if($payment->count() > 0){
            $amount = ['total_amount' => $payment->firstOrFail()->ph_amount];
            $detail = $payment->firstOrFail()->ph_detail;
            $rem_bal = $payment->firstOrFail()->ph_rembal;
        }else{
            $amount = ReceivingHeader::where(['rh_no' => $id])
                ->select(DB::raw('sum(rd_prod_qty * pod.prod_price - ((rd_prod_qty * pod.prod_price) * pod.prod_less/100)) as total_amount'))
                ->groupBy('pod.prod_code')
                ->join('receiving_details as rd','rd_code','=','rh_no')
                /*->join('po_details as pod1','rh_po_no','=','pod1.pod_code')
                ->join('po_details as pod','rd.rd_prod_code','=','pod1.prod_code')*/
                ->join('po_details as pod', function ($join)  {
                    $join->on('rh_po_no', '=', 'pod.pod_code');
                    $join->on('rd.rd_prod_code','=','pod.prod_code');
                })
                ->firstOrFail();
            $detail= "";
            $rem_bal = $amount->total_amount;
        }
        $items = ReceivingHeader::where(['rh_no' => $id])->firstOrFail();
        /*$items = ReceivingHeader::where(['rh_no' => $id])->whereHas('pod_detail',function ($query) use($id){
                 $query->select(DB::raw('sum(prod_price * receiving_details.rd_prod_qty) as total'))->leftjoin('receiving_details','rd_prod_code','=','prod_code','AND','rd_code','=','rh_no')->groupBy('rd_code');
         })->firstOrFail();*/
        //with('po_detail') ->leftjoin('po_details','rd_prod_code','=','prod_code','AND','pod_code','=','po_code')
        $duedate = Carbon::parse($items->rh_date)->addDays($items->re_po_header->term);
        $duedate = Carbon::createFromFormat('Y-m-d H:i:s', $duedate)->format('m/d/Y');
        return json_encode(['header' => $items,'supplier'=> $items->re_po_header->supplier, 'amount' => $amount,'due_date'=> $duedate,'detail'=>$detail,'rem_bal' => $rem_bal]);
    }
    public function save_payment(Request $request)
    {
        /**/
        if(PopaymentHeader::count()<1){
            $num = "TR-PM00001";
        }else{
            $num = PopaymentHeader::max('ph_no');
            ++$num;
        }
        $check = PopaymentHeader::where(['ph_rh_no' => $request->ph_rh_no]);
        if($check->count() > 0){
            $balance = $check->firstOrFail()->ph_rembal - $request->payment_amount;
            $check->update(['ph_rembal' => $balance]);
            $payment_no = PopaymentDetail::where(['pd_no' => $check->firstOrFail()->ph_no])->max('pd_paymentno');
            ++$payment_no;
            $payment_no = (strlen($payment_no)==1)?'00'.$payment_no:'0'.$payment_no;
            $check_no = isset($request->check_no)?$request->check_no:'';
            $bank_name = isset($request->bank_name)?$request->bank_name:'';
            $check_date = isset($request->check_date)?$request->check_date:'';
            PopaymentDetail::create([
                'pd_no'        => $check->firstOrFail()->ph_no,
                'pd_paymentno' => $payment_no,
                'pd_date'      => $request->payment_date,
                'pd_type'      => $request->payment_type,
                'pd_amount'    => $request->payment_amount,
                'pd_checkno'   => $check_no,
                'pd_bank'      => $bank_name,
                'pd_checkdate' => $check_date,
            ]);
        }else{
            $balance = $request->ph_amount - $request->payment_amount;
            $create = PopaymentHeader::create([
                'ph_no'     => $num,
                'ph_rh_no'  => $request->ph_rh_no,
                'ph_rembal' => $balance,
                'ph_amount' => $request->ph_amount,
                'ph_status' => 'OP'
            ]);
            $check_no = isset($request->check_no)?$request->check_no:'';
            $bank_name = isset($request->bank_name)?$request->bank_name:'';
            $check_date = isset($request->check_date)?$request->check_date:'';
            $create->ph_detail()->create([
                'pd_no'        => $num,
                'pd_paymentno' => '001',
                'pd_date'      => $request->payment_date,
                'pd_type'      => $request->payment_type,
                'pd_amount'    => $request->payment_amount,
                'pd_checkno'   => $check_no,
                'pd_bank'      => $bank_name,
                'pd_checkdate' => $check_date,
            ]);
        }
        $payment = PopaymentHeader::where(['ph_rh_no' => $request->ph_rh_no])->firstOrFail();
        if($payment->ph_rembal <= 0){
            ReceivingHeader::where(['rh_no' => $request->ph_rh_no])->update(['rh_status'=>'CL']);
        }
        Activity::log("Received PO# $request->rh_po_no", Auth::user()->id);
        return json_encode(['header' => $payment, 'detail'=> $payment->ph_detail]);
    }
}