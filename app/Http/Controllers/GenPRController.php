<?php

namespace App\Http\Controllers;

use App\PrHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class GenPRController extends Controller
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
        return view($this->position().'.generate_pr.create',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $prs = PrHeader::whereBetween('pr_date',[$from, $to])
                ->where(['pr_status' => 'AP'])
                ->join('pr_details as prd','prd.prd_code','=','prh_no')
                ->select(DB::raw('prh_no, pr_date, prd_prod_code, prd_prod_name, prd_prod_uom, prd_prod_qty,prd_prod_price,
                SUM(prd_prod_price * prd_prod_qty) as prd_amount'))
            ->groupBy('prh_no','pr_date','prd_prod_code','prd_prod_name','prd_prod_uom','prd_prod_qty','prd_prod_price')
            ->get();

        return view($this->position().'.generate_pr.create',compact('prs','request'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $prs = PrHeader::whereBetween('pr_date',[$from, $to])
            ->where(['pr_status' => 'AP'])
            ->join('pr_details as prd','prd.prd_code','=','prh_no')
            ->join('inventories as inv','prd.prd_prod_code','=','inv.code')
            ->select(DB::raw('inv.desc, prd_prod_code, prd_prod_name, prd_prod_uom, sum(prd_prod_qty) as prd_prod_qty, prd_prod_price,
                SUM(prd_prod_price * prd_prod_qty) as prd_amount'))
            ->groupBy('desc','prd_prod_code','prd_prod_name','prd_prod_uom','prd_prod_price')
            ->orderBy('desc','asc')
            ->get();
        $desc = "";
        $data = array();
        foreach ($prs as $pr)
        {
            if($pr->desc != $desc)
            {
                $data[] = [$pr->desc];
                $desc = $pr->desc;
            }
            $data[] = [
                ''=>'',
                'PROD CODE' => $pr->prd_prod_code,
                'NAME'      => $pr->prd_prod_name,
                'UOM'       => $pr->prd_prod_uom,
                'PRICE'     => $pr->prd_prod_price,
                'QTY'       => $pr->prd_prod_qty,
                'AMOUNT'    => $pr->prd_amount
            ];
        }
        return Excel::create('Taurus Generate Purchase Request', function($excel) use ($data) {
            $excel->setTitle('Taurus Generate Purchase Request');
            $excel->sheet('Transfer Report', function($sheet) use ($data)
            {
                $sheet->setColumnFormat(array(
                    'E' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'G' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Generate Purchase Request "]);
                $sheet->mergeCells("A1:G1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
                $sheet->prependRow(3, ["","CODE","NAME","UOM","PRICE","QTY","AMOUNT"]);
            });
        })->download('xlsx');
    }
}
