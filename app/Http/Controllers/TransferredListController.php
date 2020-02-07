<?php

namespace App\Http\Controllers;

use App\TransferHeaders;
use Codedge\Fpdf\Facades\PDF_MC_Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;

class TransferredListController extends Controller
{
    //
    public function index()
    {
        return view('Salesman.transferred_list.transferred');
    }
    public function show($id)
    {
        /*$transfer = TransferHeaders::where('transfer_details','transfer_details.td_code','=','transfer_headers.tf_code')
            ->with('tf_fr_branch')->with('tf_to_branch')
            ->select('*');*/
        $transfer = TransferHeaders::where(['tf_status' => 'AP', 'to_branch' => $id]) ->with('tf_fr_branch')->with('tf_to_branch') //->orderBy('tf_date', 'desc')
            ->with('tf_prep_by')->with('tf_app_by')->selectRaw('distinct transfer_headers.*');

        /*if($inventories->count()==0){
            $inventories->firstOrFail();
        }*/
        return Datatables::of($transfer) ->addColumn('action', function ($transfer) {
            return new HtmlString('<a href="/transferred/list/pdf/'.$transfer->tf_code.'" data-toggle="tooltip" target="_blank" title="Print" class="text-success" id="btn-edit" data-id='.$transfer->tf_code.'><i class="fa fa-print"></i></a>');
        })->make();
    }
    public function print_transferred($id)
    {
        //guide: po_code='Value' and (status='AP' or status='OP' or status='CL')
        $tf = TransferHeaders::where(['tf_code'=> $id, 'tf_status' => 'AP'])->firstOrFail();
        $tf_details = TransferHeaders::where(['tf_code'=> $id, 'tf_status' => 'AP'])
            ->join('transfer_details as tf','tf.td_code','=','tf_code')
            ->join('branch__inventories as bri', function ($join) {
                $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                $join->on('bri.branch_code','=','from_branch');
            })
            ->select(DB::raw('tf_prod_code,tf_prod_name,tf_prod_qty,tf_prod_uom, bri.price as tf_prod_price, SUM(bri.price * tf_prod_qty) as tf_prod_amount'))
            ->groupBy('tf_prod_code','tf_prod_name','tf_prod_qty','tf_prod_uom','price')
            ->get();
        $fpdf = new PDF_MC_Table('P','mm','Legal');
        $fpdf->AddPage();
        $path = public_path() . '/img/TaurusLogopng.png';
        //$path = asset('/img/TaurusLogopng.png');
        $fpdf->Image($path, 10, 10, 80, 20);
        $fpdf->Ln(20);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(40, 10, 'Corporate Address: Room 204, 2nd floor, Tulip Center, A.S. Fortuna Street, Barangay Bakilid, Mandaue City');
        $fpdf->Ln(5);
        $fpdf->Cell(40, 10, "Site Address: ".Auth::user()->branch_user->address);
        $fpdf->Ln(5);
        $fpdf->Cell(40, 10, 'Tel No. 032-3439651 | Fax No. 032-3433897');

        $fpdf->Ln(10);
        $fpdf->SetFont('Arial', 'B', 11);
        $fpdf->Cell(196, 7, 'DELIVERY RECEIPT',1,1,"C");

        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(101, 7, 'Ref # '.$tf->rqh_code,1,0);
        $fpdf->Cell(25, 7, $tf->tf_date,1,0);
        $fpdf->Cell(35, 7, 'Transfer No.',1,0);
        $fpdf->Cell(35, 7, $id,1,1);

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetWidths(array(101,95));
        $fpdf->Row2(array("From: {$tf->tf_fr_branch->name}","To: {$tf->tf_to_branch->name}"));
        $fpdf->Row2(array("Address: {$tf->tf_fr_branch->address}","Address: {$tf->tf_to_branch->address}"));
        $fpdf->Row2(array("Contact: {$tf->tf_fr_branch->contact}","Contact: {$tf->tf_to_branch->contact}"));
        $fpdf->SetWidths(array(95,25,70));
        /* $fpdf->Row2(array("Contact Person: ","",""));*/

        /*$fpdf->Cell(190, 7, "Please Deliver this item/items on this Date: $po->req_date","LR",1);
        $fpdf->Cell(190, 3, "","LBR",1);*/
        $header = array('CODE','PRODUCT NAME', 'QTY', 'UOM', 'SRP', 'AMOUNT');
        $w = array(25, 76, 18, 25, 28, 24);
        $i = 0;
        $fpdf->SetFont('Arial', 'B', 9);
        foreach ($header as $col) {
            $fpdf->Cell($w[$i], 7, $col, 1, 0, 'C');
            $i++;
        }
        $fpdf->Ln();
        $fpdf->SetFont('Arial', '', 9);
        $amount = 0;
        foreach ($tf_details as $product) {
            $fpdf->SetWidths(array(25, 76, 18, 25, 28, 24));
            $fpdf->Row1(array($product->tf_prod_code,$product->tf_prod_name,$product->tf_prod_qty,$product->tf_prod_uom,$product->tf_prod_price,Number_Format($product->tf_prod_amount,2)));
            $amount+=$product->tf_prod_amount;
        }
        $fpdf->SetWidths(array(172,24));
        $fpdf->SetAligns('R');
        //$fpdf->Row1(array("TOTAL",Number_Format($tf->tf_amount)));
        $fpdf->Row1(array("TOTAL",Number_Format($amount)));
        //$fpdf->prodtable($header, $prodlist);
        $fpdf->Cell(196, 7, "","LR",1);
        if($tf->tf_appby == 'henry'){
            $signature = public_path() . '/img/signature.png';
            $fpdf->Cell(196,10,$fpdf->Image($signature, 150, $fpdf->GetY(),33.78),"LR",1,"R");
        }
        $app = "";
        if($tf->tf_app_by){
            $app = $tf->tf_app_by->name;
        }

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->Cell(21, 7, "Prepared By:","L",0);
        $fpdf->Cell(26, 7, $tf->tf_prep_by->name,"B",0);
        $fpdf->Cell(83, 7, "","0",0);
        $fpdf->Cell(20, 7, "Approved by: ".$app,"0",0);
        //$pdf->Image('../../signature.png', 150, $sig_height, 40, 20);
        //$pdf->Image('../../signature.png', 140, $pdf->GetY(), 33.78);

        $fpdf->Cell(36, 7, "","B",0);
        $fpdf->Cell(10, 7, "","R",1);

        $fpdf->Cell(65, 7, "","L",0);
        $fpdf->Cell(21, 7, "Received By: ",0,0);
        $fpdf->Cell(50, 7, "",'B',0);
        $fpdf->Cell(60, 7, "",'R',1);
        $fpdf->Cell(196, 7, "","LRB",0);


        $fpdf->Ln(10);

        $fpdf->Output('DeliveryReceipt.pdf', 'I');
        exit();
    }
}
