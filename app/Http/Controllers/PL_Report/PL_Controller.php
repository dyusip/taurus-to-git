<?php

namespace App\Http\Controllers\PL_Report;

use App\Branch;
use App\ReqHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class PL_Controller extends Controller
{
    //
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
    private function process($request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $pls = ReqHeader::whereBetween('req_date',[$from, $to])->where(['req_status' => 'SE'])
                ->join('req_details as rqd',function ($join){
                    $join->on('rqh_code','=','rqd.rqd_code')
                        ->where(['rqd_status' => 'SE']);
                })
                ->join('branches as br','req_from','=','br.code')
                ->select(DB::raw('rqh_code, req_date, br.name, rqd_prod_code, rqd_prod_name,rqd_prod_price,rqd_prod_qty,
                rqd_prod_srp,(rqd_prod_price * rqd_prod_qty) as rqd_amount'))
                ->get();
        }elseif($request->optCustType == "branch") {
            $pls = ReqHeader::whereBetween('req_date',[$from, $to])->where(['req_status' => 'SE','req_from' => $request->branch])
                ->join('req_details as rqd',function ($join){
                    $join->on('rqh_code','=','rqd.rqd_code')
                        ->where(['rqd_status' => 'SE']);
                })
                ->join('branches as br','req_from','=','br.code')
                ->select(DB::raw('rqh_code, req_date, br.name, rqd_prod_code, rqd_prod_name,rqd_prod_price,rqd_prod_qty,
                rqd_prod_srp,(rqd_prod_price * rqd_prod_qty) as rqd_amount'))
                ->get();
        }
        return $pls;
    }
    public function index()
    {
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.pl_reports.pl',compact('branches'));
    }
    public function show(Request $request)
    {
        $pls = $this->process($request);
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.pl_reports.pl',compact('pls','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $pls = $this->process($request);
        $data = array();
        $total =  0;
        $br = 'ALL BRANCH';
        if($request->optCustType != "all"){
            $br = Branch::where(['code' => @$request->branch])->first()->name." BRANCH";
        }
        foreach($pls as $pl){
            $data[] = [
                'PL CODE' => $pl->rqh_code,
                'BRANCH' => $pl->name,
                'DATE' => $pl->req_date,
                'ITEM CODE' => $pl->rqd_prod_code,
                'NAME' => $pl->rqd_prod_name,
                'COST' => $pl->rqd_prod_price,
                'QTY' => $pl->rqd_prod_qty,
                'SRP' => $pl->rqd_prod_srp ,
                'TOTAL COST' => $pl->rqd_amount,

            ];
            $total += $pl->rqd_amount;
        }
        return Excel::create('Taurus Picklist Report', function($excel) use ($data, $total, $br, $from, $to) {
            $excel->setTitle('Taurus Picklist IN Report');
            $excel->sheet('Picklist Report', function($sheet) use ($data, $total, $br, $from, $to)
            {
                $sheet->setColumnFormat(array(
                    'F' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'H' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Picklist Report $br $from - $to"]);
                $sheet->mergeCells("A1:I1");
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
                    'Total Amount: '.Number_Format($total,2)
                ]);
                $sheet->mergeCells("A{$footerRow}:I{$footerRow}");
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
