<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
class BranchsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Branch::count()<1){
            $num = "TR-BR00001";
        }else{
            $num = Branch::max('code');
            ++$num;
        }
        $branches = Branch::all();
        return view('Admin.branch.create',compact('num','branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        if(Branch::count()<1){
            $num = "TR-BR00001";
        }else{
            $num = Branch::max('code');
            ++$num;
        }
        $branches = Branch::all();
        return view('Admin.branch.create',compact('num','branches'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //
       Branch::create($request->all());
       return redirect('/branch/create')->with('status', " ".strtoupper($request['name'])."'s account successfully created");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        /*$branch = Branch::findOrFail($id);
        return $branch;*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $branch = Branch::findOrFail($id);
        return $branch;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $branch = Branch::findOrFail($id);
        /*if(!isset($request->nameoffield))
            $branch->update(array_merge($request->all(),['nameofield','value']));*/
        $branch->update($request->all());
        return redirect('/branch/create')->with('status', " ".strtoupper($request['name'])."'s account successfully updated");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
        //
        //$branch = Branch::findOrFail($id);
        //$branch->delete();
        Branch::destroy($id);
        return redirect('/branch/create')->with('status', "Account successfully deleted");
    }*/
    public function destroy(Request $request, $id)
    {
        //
        /*if(isset($request->activate)){
            $branch = Branch::findOrFail($id);
            $branch->update(['status'=> 'AC']);
            $status = 'activated';
        }else if(isset($request->deactivate)){
            $branch = Branch::findOrFail($id);
            $branch->update(['status'=> 'IN']);
            $status = 'deactivated';
        }
        return redirect('/branch/create')->with('status', "Account successfully $status");*/
        $branch = Branch::findOrFail($id);
        $branch->update($request->all());
        return redirect('/branch/create')->with('status', "Account successfully updated");
    }
}