<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
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
        if(Auth::user()->position==='Administrator'){
            return view('Admin/index');
        }elseif (Auth::user()->position==='PURCHASING'){
            return view('Purchasing/index');
        }elseif (Auth::user()->position==='CEO' || Auth::user()->position==='CFO'){
            return view('Management/index');
        }elseif (Auth::user()->position==='SALESMAN'){
            return view('Salesman/index');
        }
        Auth::logout();
        return view('auth.login');

    }
    public function pagenotfound()
    {
        return view('errors.404');
    }
}
