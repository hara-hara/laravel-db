<?php

namespace App\Http\Controllers;

use App\Models\Bunbougu;
use Illuminate\Http\Request;
use App\Models\Bunrui;

class BunbouguController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$bunbougus = Bunbougu::latest()->paginate(5);
        $bunbougus = Bunbougu::select([
            'b.id',
            'b.name',
            'b.kakaku',
            'b.shosai',
            'r.str as bunrui',
        ])
        ->from('bunbougus as b')
        ->join('bunruis as r', function($join) {
            $join->on('b.bunrui', '=', 'r.id');
        })
        ->orderBy('b.id', 'DESC')
        ->paginate(5);
        
        return view('index',compact('bunbougus'))
            ->with('page_id',request()->page)
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bunruis = Bunrui::all();
        return view('create')
            ->with('bunruis',$bunruis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:20',
            'kakaku' => 'required|integer',
            'bunrui' => 'required|integer',
            'shosai' => 'required|max:140',
        ]);

        $input = $request->all();
        Bunbougu::create($input);
        return redirect()->route('bunbougus.index')
            ->with('success','文房具を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bunbougu $bunbougu)
    {
        $bunruis = Bunrui::all();
        return view('show',compact('bunbougu'))
            ->with('page_id',request()->page_id)
            ->with('bunruis',$bunruis);


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bunbougu $bunbougu)
    {
        $bunruis = Bunrui::all();
        return view('edit',compact('bunbougu'))
        ->with('page_id',request()->page_id)
        ->with('bunruis',$bunruis);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bunbougu $bunbougu)
    {
        $request->validate([
        'name' => 'required|max:20',
        'kakaku' => 'required|integer',
        'bunrui' => 'required|integer',
        'shosai' => 'required|max:140',
        ]);

        $bunbougu->name = $request->input(["name"]);
        $bunbougu->kakaku = $request->input(["kakaku"]);
        $bunbougu->bunrui = $request->input(["bunrui"]);
        $bunbougu->shosai = $request->input(["shosai"]);
        //$bunbougu->user_id = \Auth::user()->id;
        $bunbougu->save();

        return redirect()->route('bunbougus.index')
        ->with('page_id',request()->page_id)
        ->with('success','文房具を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bunbougu $bunbougu)
    {
        $bunbougu->delete();
        return redirect()->route('bunbougus.index')
            ->with('page_id',request()->page_id)
            ->with('success','文房具'.$bunbougu->name.'を削除しました');    
    }
}
