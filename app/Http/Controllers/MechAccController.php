<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class MechAccController extends Controller
{
    //
    public function index(){
        $mechanics = User::where(['position' => 'MECHANIC'])->get();
        return view('Salesman.account.create',compact('mechanics'));
    }
    public function store(Request $request){
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
            return redirect('mechanic')->with('status', " ".strtoupper($request['name'])."'s account successfully created");
        }
        return back();

    }
    public function edit($id){

        $emp = User::findOrFail($id);
        return $emp;
    }
    public function update(Request $request, $id){

        $emp = User::findOrFail($id);
        $emp->update($request->all());
        if(!isset($request['name']))
            return redirect('mechanic')->with('status', "Mechanic account successfully updated");
        return redirect('mechanic')->with('status', " ".strtoupper($request['name'])."'s account successfully updated");
    }
}
