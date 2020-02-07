<?php

namespace App\Http\Controllers\TransHistory;

use App\Branch;
use App\TransferDetails;
use App\TransferHeaders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;

class TransHistoryController extends Controller
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
        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.transfer_history.trans_history',compact('branches'));
    }
    public function show(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        //$items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])->get();
        /*$items = TransferDetails::whereHas('tf_header', function ($query) use($request, $from, $to){
            $query->whereBetween('tf_date', [$from, $to])->where(['from_branch' => $request->branch]);
        })->select(DB::raw('td_code, SUM(tf_prod_qty) as total_qty'))
            ->groupBy('td_code')
            ->get();*/
        $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
           ->where(function ($query) use($request) {
                $query->where(['from_branch' => $request->branch])
                    ->orWhere(['to_branch' => $request->branch]);
            })
            ->join('transfer_details as tf', 'tf.td_code', '=', 'tf_code')
            ->join('branch__inventories as bri', function ($join) use($from) {
                $join->on('bri.prod_code', '=', 'tf.tf_prod_code');
                //$join->on('bri.branch_code','=','to_branch');
                $join->on('bri.branch_code','=','from_branch');
            })
            ->select(DB::raw('from_branch, to_branch, SUM(tf_prod_qty) as total_qty, SUM(bri.cost * tf_prod_qty) as total_amount'))
            ->groupBy('from_branch','to_branch')
            ->get();
        /*foreach ($items as $item)
        {
            echo $item->tf_fr_branch->name." - ".$item->total_qty." - ".$item->total_amount." - ".$item->tf_to_branch->name."<br>";
        }*/

        $branches = Branch::where(['status'=>'AC'])->get();
        return view($this->position().'.transfer_history.trans_history',compact('branches','items','request'));
    }
    public function print_report(Request $request)
    {
        $from = Carbon::createFromFormat('m/d/Y', $request->start)->format('Y-m-d');
        $to = Carbon::createFromFormat('m/d/Y', $request->end)->format('Y-m-d');
        $items = TransferHeaders::where(['tf_status' => 'AP'])->whereBetween('tf_date', [$from, $to])
            ->where(function ($query) use($request) {
                $query->where(['from_branch' => $request->branch])
                    ->orWhere(['to_branch' => $request->branch]);
            })
            ->join('transfer_details as tf', 'tf.td_code', '=', 'tf_code')
            ->select(DB::raw('from_branch, to_branch, SUM(tf_prod_qty) as total_qty, SUM(tf_prod_price * tf_prod_qty) as total_amount'))
            ->groupBy('from_branch','to_branch')
            ->get();
        $data = array();
        $ctr = 0;
        foreach($items as $item)
        {
            if($item->from_branch == $request->branch) {
                $total_qty = Number_Format($item->total_qty);
                $total_amnt = Number_Format($item->total_amount, 2);
                $data[] = [
                    'SOURCE' => $item->tf_fr_branch->name,
                    'QTY' => $total_qty,
                    'VALUE' => $total_amnt,
                    'RECIPIENT' => $item->tf_to_branch->name,

                ];
                $ctr++;
                $branch = $item->tf_fr_branch->name;
            }
        }
        foreach($items as $item)
        {
            if($item->to_branch == $request->branch) {
                $total_qty = Number_Format($item->total_qty);
                $total_amnt = Number_Format($item->total_amount, 2);
                $data[] = [
                    'SOURCE' => $item->tf_fr_branch->name,
                    'QTY' => $total_qty,
                    'VALUE' => $total_amnt,
                    'RECIPIENT' => $item->tf_to_branch->name,

                ];
            }

        }
        $ctr +=4;
        return Excel::create('Taurus TransferReport', function($excel) use ($data, $ctr) {
            $excel->setTitle('Taurus Transfer Report');
            $excel->sheet('Tranfer Report', function($sheet) use ($data, $ctr)
            {
                $sheet->fromArray($data);

                $sheet->prependRow(1, ["Taurus Transfer History "]);
                $sheet->prependRow(2, [""]);

                $sheet->mergeCells("A1:D1");
                $sheet->cell('A1', function($cell) {
                    // change header color
                    $cell->setBackground('#3ed1f2')
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('center')
                        ->setValignment('center')
                        ->setFontSize(13);;
                });


                $sheet->prependRow($ctr, [""]);
                $sheet->cell("A3:D3", function($cell) {
                    // change header color
                    $cell
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('left')
                        ->setFontSize(11);;
                });
                for ($x = 0; $x<(sizeof($data) +2);$x++){
                    $row = $x+3;
                    $sheet->setBorder("A$row:D$row", 'thin');
                }
                $sheet->cell("A$ctr:D$ctr", function($cell) {
                    // change header color
                    $cell
                        ->setFontColor('#0a0a0a')
                        ->setFontWeight('bold')
                        ->setAlignment('left')
                        ->setFontSize(11);;
                });
                $sheet->prependRow($ctr+1, ["SOURCE","QTY","VALUE","RECIPIENT"]);
            });
        })->download('xlsx');

    }
}
