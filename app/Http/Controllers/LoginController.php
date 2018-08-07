<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
//use DB;
use Validator;
use Auth;
class LoginController extends Controller
{
    function index(){
        return view('login');
    }
    function postLogin(Request $request){
        /*$username = $request->username;
        $password = $request->password;
        $login = DB::table('user_file')->where(['username' => $username, 'password' => $password])->get();
        if($login->count() > 0){
            echo('Success');
            print_r($request->input());
        }else{
            echo('Failed');
        }*/
        /*$this->validate($request,[
            'username' =>  'required|username',
            'password' =>  'required|alphaNum|min:5'
        ]);*/

        $user_data = array(
            'username' => $request->get('username'),
            'password' => $request->get('password')
        );
        if(Auth::attempt($user_data)){
            return redirect('successlogin');
        }else{
            return back()->with('error','Username and password is incorrect');
        }
    }
    function successLogin(){
        return view('Admin/index');
    }
    function logout(){
        Auth::logout();
        return redirect('login');
    }

}
