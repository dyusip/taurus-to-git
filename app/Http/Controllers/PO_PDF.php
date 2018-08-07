<?php

namespace App\Http\Controllers;

use App\PoHeader;
use Codedge\Fpdf\Facades\PDF_MC_Table;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PO_PDF extends Controller
{
    //
    public function printPO(Request $request)
    {
        //guide: po_code='Value' and (status='AP' or status='OP' or status='CL')
        $po = PoHeader::where(['po_code'=>$request->PO_No])->where(function ($query){
            $query->where(['status' => 'AP'])->orWhere(['status' => 'OP'])->orWhere(['status' => 'CL']);
        })->firstOrFail();
        $fpdf = new PDF_MC_Table();
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
        $fpdf->Cell(190, 7, 'PURCHASE ORDER',1,1,"C");

        $fpdf->Cell(95, 7, '',1,0);
        $fpdf->Cell(25, 7, '',1,0);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(35, 7, 'P.O. No',1,0);
        $fpdf->Cell(35, 7, $request->PO_No,1,1);

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetWidths(array(95,25,35,35));
        $fpdf->Row(array("Supplier Name: $po->sup_name","","Date","$po->po_date"));
        $fpdf->Row(array("Contact No: $po->sup_contact","","Term","$po->term"));
        $fpdf->Row(array("Fax No: ","","Reference",""));
        $fpdf->SetWidths(array(95,25,70));
        $fpdf->Row2(array("Contact Person: ","",""));

        $fpdf->Cell(190, 7, "Please Deliver this item/items on this Date: $po->req_date","LR",1);
        $fpdf->Cell(190, 3, "","LBR",1);
        $header = array('PRODUCT NAME', 'QUANTITY', 'UOM', 'UNIT PRICE', 'AMOUNT');
        $w = array(85, 21, 24, 30, 30);
        $i = 0;
        $fpdf->SetFont('Arial', 'B', 9);
        foreach ($header as $col) {
            $fpdf->Cell($w[$i], 7, $col, 1, 0, 'C');
            $i++;
        }
        $fpdf->Ln();
        $fpdf->SetFont('Arial', '', 9);

        foreach ($po->po_detail as $product) {
            $fpdf->SetWidths(array(85, 21, 24, 30, 30));
            $fpdf->Row1(array($product->prod_name,$product->prod_qty,$product->prod_uom,$product->prod_price,$product->prod_amount));
        }
        //$fpdf->prodtable($header, $prodlist);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(145, 7, "TERMS AND CONDITION:","L",0);
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->Cell(15, 7, "TOTAL: ",0,0);
        $fpdf->SetFont('Arial', 'U', 9);
        $fpdf->Cell(30, 7, " $po->total","R",1);
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->Cell(130, 5, "For failure of supplier to deliver within the above specified","L",0);
        $fpdf->Cell(22, 5, "Requested by:",0,0);
        $fpdf->SetFont('Arial', 'U', 9);


        $fpdf->Cell(38, 5,"" ,"R",1);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(42, 5, "TAURUS MERCHANDISING","L",0);
        $fpdf->SetFont('Arial', '', 9);
        $fpdf->Cell(148, 5, "reserves the right to cancel any or all items under ","R",1);
        $fpdf->Cell(190, 5, "this Purchase Order or impose penalties for delays. ","LR",1);
        $fpdf->Cell(20, 5, "Prepared by: ","L",0);
        $fpdf->SetFont('Arial', 'U', 9);
        $fpdf->Cell(170, 5,$po->prepared->name ,"R",1);
       /* if($app_by=="HENRY C. POBLADOR"){
            $pdf->Cell(190,10,$pdf->Image('../../signature.png', 150, $pdf->GetY(),33.78),"LR",1,"R");
        }elseif($app_by=="ILY-J B. VELASCO"){
            $pdf->Cell(190,10,$pdf->Image('../../sirjay.png', 155, $pdf->GetY(),28),"LR",1,"R");
        }*/

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->Cell(24, 7, "Terms/Conditions:","L",0);
        $fpdf->Cell(50, 7, "","B",0);
        $fpdf->Cell(50, 7, "","0",0);
        $fpdf->Cell(20, 7, "Approved by: ".$po->approved->name,"0",0);
        //$pdf->Image('../../signature.png', 150, $sig_height, 40, 20);
        //$pdf->Image('../../signature.png', 140, $pdf->GetY(), 33.78);

        $fpdf->Cell(36, 7, "","B",0);
        $fpdf->Cell(10, 7, "","R",1);
        $fpdf->Cell(50, 7, "","LB",0);
        $fpdf->Cell(140, 7, "Supplier's Authorized Representative","RB",0);




        $fpdf->Ln(10);

        $fpdf->Output('PurchaseOrder.pdf', 'I');
        exit();
    }
}

