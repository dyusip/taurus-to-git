<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\Datatables\Datatables;

class LogsController extends Controller
{
    //
    public function index()
    {
        return view('Admin.logs.logs');
    }
    public function show()
    {
        $logs = Activity::with('user')->with('user.branch_user')->select('*');//orderBy('activity_log.created_at','desc')
        return Datatables::of($logs)->make();
    }
}