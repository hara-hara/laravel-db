<?php

namespace App\Http\Controllers;

use App\Models\Worktime;
use Illuminate\Http\Request;
use App\Models\Worktype;
use Illuminate\Support\Facades\Log; //デバッグ用

class WorktimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        //$worktimes = Bunbougu::latest()->paginate(5);
        $worktimes = Worktime::select([
            'b.work_date',
            'b.work_start',
            'b.work_end',
            'r.str as work_type',
        ])
        ->from('worktimes as b')
        ->join('worktypes as r', function($join) {
            $join->on('b.work_type', '=', 'r.id');
        })
        ->orderBy('b.work_date', 'ASC')
        ->where('b.member_id','=','1001')
        ->paginate(34);
/*
        <th class=".w-10" style="text-align:center">労働時刻</th>
        =IF(OR(R17="",U17=""),"",IF(OR(R17>="13:00"*1,U17<="12:00"*1),HOUR(U17-R17)+(MINUTE(U17-R17)/60),(HOUR(U17-R17)-IF(AL$6<>"",AL$6,O17))+(MINUTE(U17-R17)/60)))
        <th class=".w-10" style="text-align:center">法定内残業</th>
        =IF($AG17="","",IF(AND($AG17<=1,$AG17>=0),$AG17,1))
        <th class=".w-10" style="text-align:center">法定外残業</th>
        =IFERROR($AG17-$AA17,"")
        <th class=".w-10" style="text-align:center">残業</th>
        =IFERROR(IF($X17+IFS($E17="有休",$AL$16,$E17="AM半休",$AL$17,$E17="PM半休",$AL$18,TRUE,0)-$AL$5<0,"",$X17+IFS($E17="有休",$AL$16,$E17="AM半休",$AL$17,$E17="PM半休",$AL$18,TRUE,0)-$AL$5),"")
*/

        $ii=0;
        foreach($worktimes as $worktime){
            $ii++;
            $w_time_results[$ii]['roudou_time']=(strtotime($worktime->work_end) - strtotime($worktime->work_start) )/ 3600 -1;
            $w_time_results[$ii]['houteiNai_time']=strtotime($worktime->work_end) - strtotime($worktime->work_start);;
            $w_time_results[$ii]['houteiGai_time']=strtotime($worktime->work_end) - strtotime($worktime->work_start);;
            $w_time_results[$ii]['zangyou_time']=strtotime($worktime->work_end) - strtotime($worktime->work_start);;
        }
     
        
        return view('worktime_index',compact('worktimes','w_time_results'))
            ->with('page_id',request()->page)
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $worktimes = Worktime::all();
        return view('worktime_create')
            ->with('worktypes',$worktypes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer|max:20',
            'work_date' => 'required',
            'work_start' => 'required',
            'work_end' => 'required',
            'work_leave_start',
            'work_leave_end',
            'work_type' => 'required',
            'work_times' => 'required',
            'created_at',
            'updated_at',        
        ]);

        $input = $request->all();
        Worktime::create($input);
        return redirect()->route('worktimes.index')
            ->with('success','文房具を登録しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(Worktime $worktime)
    {
        $worktypes = Worktype::all();
        return view('worktime_show',compact('worktime'))
            ->with('page_id',request()->page_id)
            ->with('worktypes',$worktypes);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $comm_worktimes = Worktime::select([
            'b.member_id',
            'b.work_date',
            'b.work_start',
            'b.work_end',
            'b.work_type',
        ])
        ->from('worktimes as b')
        ->orderBy('b.work_date', 'ASC')
        ->paginate(34);

        $worktypes = Worktype::all();

        return view('worktime_edit',compact('comm_worktimes'))
            ->with('page_id',request()->page_id) //★これを外すとeditページを開いたとたんUndefined $page_idになる
            ->with('worktypes',$worktypes);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Worktime $worktime)
    {
        //Log::debug(print_r($worktimes, true));
        //Log::debug($worktimes);
        //dd($request);

        $ii=0;
        $worktimes = Worktime::where('member_id','=','1001')
        ->where('work_date','<=','2023-9-30')->get();
        //Log::debug(print_r($worktimes, true));
        //dd($worktimes);

        foreach($worktimes as $worktime){
            $ii++;
            $w_start = "work_start-" . $ii;
            $w_end = "work_end-" . $ii;
            $w_type = "work_type-" . $ii;
            $w_date = "work_date-" . $ii;

            /*
            $request->validate([
                $w_start => 'required|max:20',
                $w_end => 'required|max:20',
                $w_type => 'required|max:20',
                //'kakaku' => 'required|integer',
                //'worktype' => 'required|integer',
                //'shosai' => 'required|max:140',
            ]);
            */
            $worktime->work_start = $request->input([$w_start]);
            $worktime->work_end = $request->input([$w_end]);
            $worktime->work_type = $request->input([$w_type]);
            //$worktime->user_id = \Auth::user()->id;
            $worktime->save();
        }
    

        $page = request()->input('page'); //★この行が無いとUndefined $pageになる

        return redirect()->route('worktime.index', ['page' => $page]) //★
        ->with('success','文房具を更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worktime $worktime)
    {
        $worktime->delete();
        return redirect()->route('worktime.index')
            ->with('page_id',request()->page_id)
            ->with('success','文房具'.$worktime->name.'を削除しました');    
    }
}
