<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
use App\User;
use App\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use Activity;

class EmployeeController extends Controller
{
    //
    public function index(){
        $users = User::all();
        //return $users;
        $branches = Branch::where(['status' => 'AC'])->get();
        return view('Admin/employee/employee',compact('users','branches'));
    }
    public function registerEmp(Request $request){
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'gender' => 'required',
            'position' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:25|unique:users',
        ]);
        $userCreate = User::create([
            'name'           => $request['name'],
            'email'          => $request['email'],
            'username'       => $request['username'],
            'position'       => $request['position'],
            'password'       => '12345',
            'status'         => 'AC',
            'branch'         => $request['branch'],
            'gender'         => $request['gender'],
            'contact'        => $request['contact']
        ]);
        if($userCreate){
            Activity::log("Created {$request['name']}'s user account", Auth::user()->id);
            return redirect('employee')->with('status', " ".strtoupper($request['name'])."'s account successfully created");
        }
        return back();

    }
    public function edit($id){

        $emp = User::findOrFail($id);
        return $emp;
    }
    public function update(Request $request,$id){

        $emp = User::findOrFail($id);
        $emp->update($request->all());
        Activity::log("Updated {$request['name']}'s user account", Auth::user()->id);
        if(!isset($request['name']))
            return redirect('employee')->with('status', "Employee account successfully updated");
        return redirect('employee')->with('status', " ".strtoupper($request['name'])."'s account successfully updated");
    }
    public function update_password(Request $request){
        Validator::extend('check_current', function ($attribute, $value, $parameters, $validator) {
            if( ! Hash::check( $parameters[0], Auth::user()->getAuthPassword()) )
            {
                return false;
            }
            return true;
        },"Current password doesn't match.");

        $this->validate($request, [
            'current_pass' => "check_current:{$request->current_pass}",
            'new_pass'     => 'required|min:5|different:current_pass',
            'confirm_pass' => 'required|same:new_pass',
        ]);
        $user = User::findOrFail(Auth::user()->id);
        $user->update(['password' => $request->new_pass]);
        Activity::log(Auth::user()->name." changed his/her password", Auth::user()->id);
        return redirect($request->route)->with('password_status', "Password successfully updated.");
    }
}
