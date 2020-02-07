<?php

namespace App\Http\Controllers;

use App\ReceivingHeader;
use App\ReqHeader;
use Carbon\Carbon;
use Codedge\Fpdf\Facades\PDF_MC_Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivingListController extends Controller
{
    //
    public function index()
    {
        return view('Purchasing.receiving_list.report');
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = ReceivingHeader::whereBetween('rh_date', [$from, $to])
            ->join('receiving_details as rd','rd.rd_code','=','rh_no')
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'rh_po_no');
                $join->on('pod.prod_code','=','rd.rd_prod_code');
            })
            ->with('re_po_header')
            ->select(DB::raw('rh_po_no, rh_si_no, rh_no, rh_date,
            SUM(pod.prod_price * rd.rd_prod_qty - ((pod.prod_price * rd.rd_prod_qty) * (pod.prod_less/100))) as amount
            '))
            ->groupBy('rh_po_no','rh_si_no', 'rh_no','rh_date')
            ->get();
        /*foreach($items as $item)
        {
            echo $item->rh_po_no." - ".$item->re_po_header->supplier->name." - ".$item->rh_si_no." - ".$item->amount."<br>";
        }*/

        return view('Purchasing.receiving_list.report',compact('items','request'));
    }
    public function print_pdf($id)
    {
        $header = ReceivingHeader::where(['rh_no' => $id])->first();
        $items = ReceivingHeader::where(['rh_no' => $id])
            ->join('receiving_details as rd','rd.rd_code','=','rh_no')
            ->join('po_details as pod', function ($join) {
                $join->on('pod.pod_code', '=', 'rh_po_no');
                $join->on('pod.prod_code','=','rd.rd_prod_code');
            })
            ->select(DB::raw('rd_prod_code, rd_prod_name, rd_prod_uom, rd_prod_qty, rd_status, pod.prod_price, prod_less,
            SUM(pod.prod_price * rd.rd_prod_qty - ((pod.prod_price * rd.rd_prod_qty) * (pod.prod_less/100))) as amount
            '))
            ->groupBy('rd_prod_code','rd_prod_name', 'rd_prod_uom','rd_prod_qty','prod_price','rd_status','prod_less')
            ->get();
        /*foreach ($items as $item)
        {
            echo $item->rd_prod_code." - ".$item->rd_prod_name." - ".$item->rd_prod_uom." - ".$item->rd_prod_qty." - ".$item->prod_price." - ".$item->amount." - ".$item->rd_status."<br>";
        }*/
        $fpdf = new PDF_MC_Table();
        $fpdf->AddPage();
        $path = public_path() . '/img/tinuod.gif';
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
        $fpdf->Cell(190, 7, 'RECEIVING REPORT',1,1,"C");

        $fpdf->Cell(95, 7, "SI #: $header->rh_si_no",1,0);
        $fpdf->Cell(25, 7, '',1,0);
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(35, 7, 'Receiving #:',1,0);
        $fpdf->Cell(35, 7, $header->rh_no,1,1);

        $fpdf->SetFont('Arial', '', 9);
        $fpdf->SetWidths(array(95,25,35,35));
        $rec_date = Carbon::createFromFormat('Y-m-d', $header->rh_date)->format('m/d/Y');
        $fpdf->Row(array("Supplier Name: {$header->re_po_header->supplier->name}","","Receiving Date","{$rec_date}"));
        $fpdf->Row(array("Contact No: {$header->re_po_header->supplier->contact}","","Term","{$header->re_po_header->term}"));
        $fpdf->Row(array("Fax No: ","","Reference PO #","{$header->rh_po_no}"));
        $fpdf->Row(array("Contact Person: ","","PO Date","{$header->re_po_header->po_date}"));
        $fpdf->SetWidths(array(95,25,70));
        //$fpdf->Row2(array("Contact Person: ","",""));

        $fpdf->Cell(190, 7, "","LR",1);
        $fpdf->Cell(190, 3, "","LBR",1);
        $header = array('CODE','PRODUCT NAME', 'UOM', 'QTY', 'PRICE','DISC', 'AMOUNT', 'STATUS');
        $w = array(23, 66, 15, 13, 20, 13, 20, 20);
        $i = 0;
        $fpdf->SetFont('Arial', 'B', 9);
        foreach ($header as $col) {
            $fpdf->Cell($w[$i], 7, $col, 1, 0, 'C');
            $i++;
        }
        $fpdf->Ln();
        $fpdf->SetFont('Arial', '', 9);
       /* foreach ($items as $item)
        {
            echo $item->rd_prod_code." - ".$item->rd_prod_name." - ".$item->rd_prod_uom." - ".$item->rd_prod_qty." - ".$item->prod_price." - ".$item->amount." - ".$item->rd_status."<br>";
        }*/
        $total = 0;
        foreach ($items as $product) {
            if ($product->rd_status=='EX'){
                $status = "EXCEED";
            }elseif ($product->rd_status == 'RE') {
                $status = "RECEIVED";
            }elseif ($product->rd_status=='LA'){
                $status = "LACKING";
            }
            $fpdf->SetWidths(array(23, 66, 15, 13, 20, 13, 20, 20));
            $fpdf->Row1(array($product->rd_prod_code,$product->rd_prod_name,$product->rd_prod_uom,$product->rd_prod_qty,Number_Format($product->prod_price,2),$product->prod_less."%",Number_Format($product->amount,2), $status));
            $total += $product->amount;
        }
        $fpdf->SetFont('Arial', 'B', 9);
        $fpdf->Cell(150, 7, 'TOTAL',1,0,'R');
        $fpdf->Cell(40, 7, Number_Format($total,2),1,1);


        $fpdf->Ln(10);

        $fpdf->Output('PurchaseOrder.pdf', 'I');
        exit();
    }
}
