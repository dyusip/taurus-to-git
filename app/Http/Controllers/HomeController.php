<?php

namespace App\Http\Controllers;

use App\Branch_Inventory;
use App\Inventory;
use App\SoHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private function position()
    {
        if(Auth::user()->position==='Administrator'){
            $position = 'Admin';
        }elseif (Auth::user()->position==='PURCHASING' || Auth::user()->position==='AUDIT-OFFICER'){
            $position = 'Purchasing';
        }elseif (Auth::user()->position == 'CEO' || Auth::user()->position == 'CFO'){
            $position = 'Management';
        }elseif (Auth::user()->position == 'PARTS-MAN'){
            $position = 'Partsman';
        }elseif (Auth::user()->position == 'SALESMAN'){
            $position = 'Salesman';
        }elseif (Auth::user()->position==='AUDIT-OFFICER'){
            $position = 'Auditor';
        }
        return $position;
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$inventories = Branch_Inventory::paginate('10');
        if($this->position()){
            return view($this->position().'/index');
        }
        Auth::logout();
        return view('auth.login');

    }
    public function pagenotfound()
    {
        return view('errors.404');
    }
    public function search(Request $request)
    {
        $key = $request->item;
        $inventories = Branch_Inventory::whereHas('inventory', function ($query) use ($key) {
            $query->where('name', 'like', '%' . $key . '%');
        })->paginate('10');
        return view($this->position().'.index', compact('inventories', 'key'));
    }
    public function suggest()
    {
        $items = Inventory::where(['status' => 'AC'])->get();
        $row = array();
        foreach ($items as $item){
            $row[] = $item->name;
        }
        $json = array('ITEM' => $row);
        return json_encode($json);
    }
    public function show_mgt($from, $to)
    {
        $from = Carbon::createFromFormat('m-d-Y', $from)->format('Y-m-d');
        $to = Carbon::createFromFormat('m-d-Y', $to)->format('Y-m-d');
        $sales = SoHeader::whereBetween('so_date', [$from, $to])->select(DB::raw('sum(so_amount) as total, branch_code'))->groupBy('branch_code')->with('so_branch')->get();
        return $sales;
    }
}
