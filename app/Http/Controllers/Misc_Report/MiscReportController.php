<?php

namespace App\Http\Controllers\Misc_Report;

use App\Branch;
use App\MiscHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class MiscReportController extends Controller
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
        $branches = Branch::where(['status'=>'AC'])->get();

        return view($this->position().'.misc_report.misc',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $miscs = MiscHeader::whereBetween('msh_date', [$from, $to])
                ->join('misc_details as msd', function ($join) use($from) {
                    $join->on('msd.msd_code', '=', 'msh_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->select(DB::raw('msh_code, msd_prod_code, msd_prod_name, msd_prod_qty, msd_prod_cost,
                msd_upd_qty, sum(msd_prod_qty * msd_prod_cost) as msd_amount, msh_date, msd_remarks'))
                ->groupBy('msh_code','msh_date','msd_prod_code','msd_prod_name','msd_prod_qty','msd_upd_qty','msd_prod_cost','msd_remarks')
                ->get();
        }elseif($request->optCustType == "branch"){
            $miscs = MiscHeader::whereBetween('msh_date', [$from, $to])->where(['msh_branch_code' => $request->branch])
                ->join('misc_details as msd', function ($join) use($from) {
                    $join->on('msd.msd_code', '=', 'msh_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->select(DB::raw('msh_code, msd_prod_code, msd_prod_name, msd_prod_qty, msd_prod_cost,
                msd_upd_qty, sum(msd_prod_qty * msd_prod_cost) as msd_amount, msh_date, msd_remarks'))
                ->groupBy('msh_code','msh_date','msd_prod_code','msd_prod_name','msd_prod_qty','msd_upd_qty','msd_prod_cost','msd_remarks')
                ->get();
        }
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.misc_report.misc',compact('miscs','request', 'branches'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        if($request->optCustType == "all"){
            $miscs = MiscHeader::whereBetween('msh_date', [$from, $to])
                ->join('misc_details as msd', function ($join) use($from) {
                    $join->on('msd.msd_code', '=', 'msh_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->join('branches as br','br.code','=','msh_branch_code')
                ->select(DB::raw('br.name as branch_name,msh_code, msd_prod_code, msd_prod_name, msd_prod_qty, msd_prod_cost,
                msd_upd_qty, sum(msd_prod_qty * msd_prod_cost) as msd_amount, msh_date, msd_remarks'))
                ->groupBy('msh_code','msh_date','br.name','msd_prod_code','msd_prod_name','msd_prod_qty','msd_upd_qty','msd_prod_cost','msd_remarks')
                ->get();
            $br = "ALL BRANCH";
        }elseif($request->optCustType == "branch"){
            $miscs = MiscHeader::whereBetween('msh_date', [$from, $to])->where(['msh_branch_code' => $request->branch])
                ->join('misc_details as msd', function ($join) use($from) {
                    $join->on('msd.msd_code', '=', 'msh_code')
                        ->where(['msd_remarks' => 'IN']);
                })
                ->join('branches as br','br.code','=','msh_branch_code')
                ->select(DB::raw('br.name as branch_name, msh_code, msd_prod_code, msd_prod_name, msd_prod_qty, msd_prod_cost,
                msd_upd_qty, sum(msd_prod_qty * msd_prod_cost) as msd_amount, msh_date, msd_remarks'))
                ->groupBy('msh_code','msh_date','br.name','msd_prod_code','msd_prod_name','msd_prod_qty','msd_upd_qty','msd_prod_cost','msd_remarks')
                ->get();
            $br = Branch::where(['code' => $request->branch])->first()->name." BRANCH";
        }
        $data = array();
        $total = 0;
        foreach ($miscs as $misc)
        {
            $count = $misc->msd_upd_qty - $misc->msd_prod_qty;
            $data[] = [
                'BRANCH' => $misc->branch_name,
                'MISC CODE' => $misc->msh_code,
                'DATE' => $misc->msh_date,
                'ITEM CODE' => $misc->msd_prod_code,
                'NAME' => $misc->msd_prod_name,
                'COUNT SHEET' => $count,
                'PHYS COUNT' => $misc->msd_upd_qty,
                'VARIANCE (-)' => $misc->msd_prod_qty,
                'COST' => $misc->msd_prod_cost,
                'REMARKS' => $misc->msd_remarks,
                'AMOUNT' => $misc->msd_amount,
            ];
            $total += $misc->msd_amount;
        }
        return Excel::create('Taurus Miscellaneous IN Report', function ($excel) use ($data, $total, $br, $from, $to) {
            $excel->setTitle('Taurus Miscellaneous IN Report');
            $excel->sheet('Miscellaneous IN Report', function ($sheet) use ($data, $total, $br, $from, $to) {
                $sheet->setColumnFormat(array(
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                    'K' => \PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_PHP_SIMPLE,
                ));
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Miscellaneous IN Report $br $from - $to"]);
                $sheet->mergeCells("A1:K1");
                $sheet->cell('A1', function ($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });
                $footerRow = count($data) + 3;
                $sheet->appendRow("$footerRow", [
                    'Total Amount: ₱' . Number_Format($total, 2)
                ]);
                $sheet->mergeCells("A{$footerRow}:J{$footerRow}");
                $sheet->cell("A{$footerRow}", function ($cell) {
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