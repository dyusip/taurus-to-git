<?php

namespace App\Http\Controllers;
use App\ReqHeader;
use App\RequestDetail;
use Codedge\Fpdf\Facades\PDF_MC_Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Yajra\Datatables\Datatables;

class PR_PDFController extends Controller
{
    //
    private function position()
    {
        if (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'PURCHASING' || Auth::user()->position == 'AUDIT-OFFICER'){
            $position = 'Purchasing';
        }elseif (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }
        return $position;
    }
    public function index()
    {
        return view($this->position().'.print_pl.pl');
    }
    public function show($id)
    {
        $transfer = ReqHeader::where(['req_status' => 'SE'])->with('req_from_branch')
        ->with('rqh_req_by')->with('rqh_check_by')->selectRaw('distinct req_headers.*');

        return Datatables::of($transfer) ->addColumn('action', function ($transfer) {
            return new HtmlString('<a onclick="javascript:post_link('.$transfer->rqh_code.');" data-toggle="tooltip" target="_blank" title="Print" class="text-success" id="btn-edit" data-id='.$transfer->rqh_code.'><i class="fa fa-print"></i></a>');
        })->make();//href="/transferred/print/pdf/'.$transfer->tf_code.'"
    }
    //Partsman print before DR created
    public function print_request(Request $request)
    {
        //guide: po_code='Value' and (status='AP' or status='OP' or status='CL')
        $tf = ReqHeader::where(['rqh_code'=> $request->rqh_code])->firstOrFail();
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
        $fpdf->Cell(196, 7, 'PICK LIST',1,1,"C");

        $fpdf->Cell(101, 7, '',1,0);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(25, 7, $tf->req_date,1,0);
        $fpdf->Cell(35, 7, 'Transfer No.',1,0);
        $fpdf->Cell(35, 7, $request->rqh_code,1,1);

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetWidths(array(101,95));
        $fpdf->Row2(array("From: {$tf->req_from_branch->name}","To: {$tf->req_to_branch->name}"));
        $fpdf->Row2(array("Address: {$tf->req_from_branch->address}","Address: {$tf->req_to_branch->address}"));
        $fpdf->Row2(array("Contact: {$tf->req_from_branch->contact}","Contact: {$tf->req_to_branch->contact}"));
        $fpdf->SetWidths(array(101,25,70));
        /* $fpdf->Row2(array("Contact Person: ","",""));*/

        /*$fpdf->Cell(190, 7, "Please Deliver this item/items on this Date: $po->req_date","LR",1);
        $fpdf->Cell(190, 3, "","LBR",1);*/
        $header = array('CODE','PRODUCT NAME', 'QTY', 'UOM', 'UNIT PRICE', 'AMOUNT');
        $w = array(25, 76, 18, 25, 28, 24);
        $i = 0;
        $fpdf->SetFont('Arial', 'B', 9);
        foreach ($header as $col) {
            $fpdf->Cell($w[$i], 7, $col, 1, 0, 'C');
            $i++;
        }
        $fpdf->Ln();
        $fpdf->SetFont('Arial', '', 9);

        foreach ($tf->req_detail as $product) {
            $fpdf->SetWidths(array(25, 76, 18, 25, 28, 24));
            $fpdf->Row1(array($product->rqd_prod_code,$product->rqd_prod_name,$product->rqd_prod_qty,$product->rqd_prod_uom,$product->rqd_prod_price,Number_Format($product->rqd_prod_amount,2)));
        }
        $fpdf->SetWidths(array(172,24));
        $fpdf->SetAligns('R');
        $fpdf->Row1(array("TOTAL",Number_Format($tf->req_amount)));
        //$fpdf->prodtable($header, $prodlist);
        $fpdf->Cell(196, 7, "","LR",1);
        if($tf->tf_appby == 'henry'){
            $signature = public_path() . '/img/signature.png';
            $fpdf->Cell(196,10,$fpdf->Image($signature, 150, $fpdf->GetY(),33.78),"LR",1,"R");
        }

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->Cell(20, 7, "Requested By: ","L",0);
        $fpdf->Cell(21, 7, $tf->rqh_req_by->name,"B",0);
        $fpdf->Cell(83, 7, "","0",0);
        $fpdf->Cell(26, 7, "Prepared by: ".@$tf->tf_app_by->rqh_check_by,"0",0);
        //$pdf->Image('../../signature.png', 150, $sig_height, 40, 20);
        //$pdf->Image('../../signature.png', 140, $pdf->GetY(), 33.78);

        $fpdf->Cell(36, 7, "","B",0);
        $fpdf->Cell(10, 7, "","R",1);
        $fpdf->Cell(196, 7, "","LRB",0);





        $fpdf->Ln(10);

        $fpdf->Output('Picklist.pdf', 'I');
        exit();
    }
}
