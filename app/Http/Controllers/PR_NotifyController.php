<?php

namespace App\Http\Controllers;

use App\PrDetail;
use App\PrHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Excel;

class PR_NotifyController extends Controller
{
    //
    public function pr_index()
    {
        $prs = PrHeader::where(['pr_status' => 'PD'])->get();
        return view('Management.Approve.pr',compact('prs'));
    }
    public function show_item($id)
    {
        $pr = PrHeader::where(['prh_no' => $id])->first();
        return json_encode(['header' => $pr,'requested' => $pr->pr_reqby,'detail'=> $pr->pr_detail]);
    }
    public function store(Request $request)
    {
        if($request->status == 'AP'){
            PrHeader::where(['prh_no' => $request->PONo])->update(['pr_status' => 'AP', 'pr_total' => $request->pr_total,'prh_appby'=> Auth::user()->username]);
            foreach ($request->prd_prod_code as $item => $value)
            {
                PrDetail::where(['prd_prod_code' =>  $request->prd_prod_code[$item]])->where(['prd_code' => $request->PONo ])
                    ->update([
                        'prd_prod_qty'    => $request->prd_prod_qty[$item],
                        'prd_prod_price'  => $request->prd_prod_price[$item],
                        'prd_prod_amount' => $request->prd_prod_amount[$item],
                        'prd_status'      => 'AP',
                    ]);
            }
            PrDetail::whereNotIn('prd_prod_code',$request->prd_prod_code)->where(['prd_code' => $request->PONo ])
            ->update(['prd_status' => 'NA']);
            return redirect('/pr_approval')->with('status', "PR# ".strtoupper($request->PONo)." successfully approved.");
        }else if($request->status == 'NA'){
            PrHeader::where(['prh_no' => $request->PONo])->update(['pr_status' => 'NA', 'prh_appby'=> Auth::user()->username]);
            PrDetail::where(['prd_code' => $request->PONo])->update(['prd_status' => 'NA']);
            return redirect('/pr_approval')->with('status', "PR# ".strtoupper($request->PONo)." successfully cancelled.");
        }
    }
    public function pr_view()
    {
        $prs = PrHeader::where(['pr_status' => 'AP'])->get();
        return view('Purchasing.pr_view.pr',compact('prs'));
    }
    public function show_pr($id)
    {
        $pr = PrHeader::where(['prh_no' => $id])->first();
        $items = PrDetail::where(['prd_code' => $id])->where(['prd_status' => 'AP'])->get();
        return json_encode(['header' => $pr,'requested' => $pr->pr_reqby,'approved'=> $pr->prh_appby,'detail'=> $items]);
    }
    public function print_pr(Request $request)
    {
        $items = PrDetail::where(['prd_code' => $request->PONo])->where(['prd_status' => 'AP'])->get();
        $data = array();
        $total = 0;
        foreach ($items as $item)
        {
            $data [] = [
                'CODE' => $item->prd_prod_code,
                'NAME' => $item->prd_prod_name,
                'UOM' => $item->prd_prod_uom,
                'QTY' => $item->prd_prod_qty,
                'PRICE' => $item->prd_prod_price,
                'AMOUNT' => $item->prd_prod_amount,
            ];
            $total += $item->prd_prod_amount;
        }
        return Excel::create('Taurus Purchase Request', function($excel) use ($data, $total) {
            $excel->setTitle('Taurus Purchase Request');
            $excel->sheet('Purchase Request', function($sheet) use ($data, $total)
            {
                $sheet->setColumnFormat(array(
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Purchase Request"]);
                $sheet->mergeCells("A1:F1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
                $footerRow = count($data) + 3;
                $sheet->appendRow("$footerRow",[
                    'Total Amount: '.$total
                ]);
                $sheet->mergeCells("A{$footerRow}:F{$footerRow}");
                $sheet->cell("A{$footerRow}", function($cell) {
                    $cell->setBackground('#3ed1f2')
                        ->setAlignment('right')
                        ->setValignment('right')
                        ->setFontWeight('bold')
                        //->setFontColor('#666666')
                        ->setFontSize(11);
                });
            });
        })->download('xlsx');

    }
}
