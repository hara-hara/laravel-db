<?php

namespace App\Http\Controllers;

use App\Models\Dakoku;
use Illuminate\Http\Request;

class DakokuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dakokus = Dakoku::latest()->paginate(5);


        //ログインしていない場合、エラーが出るので、以下のように処理を分けます。
        if(isset(\Auth::user()->name)){
            return view('dakoku_index',compact('dakokus'))
                ->with('page_id',request()->page)
                ->with('i', (request()->input('page', 1) - 1) * 5)
                ->with('user_name',\Auth::user()->name);
        }else{
            return view('dakoku_index',compact('dakokus'))
                ->with('page_id',request()->page)
                ->with('i', (request()->input('page', 1) - 1) * 5);
        }




    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dakoku $dakoku)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dakoku $dakoku)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dakoku $dakoku)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dakoku $dakoku)
    {
        //
    }
}
