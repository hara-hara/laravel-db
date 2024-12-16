<?php

namespace App\Http\Controllers;

use App\Models\Timestamp;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class TimestampController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $work_type_no=1;
        // 現在の日時を取得
        $now = Carbon::now();
        $now_format = $now->format('Y-m-d H:i:s');
        $now_ymd = $now->format('Y-m-d');
        //dd($now_ymd);
        $now_year = $now->format('Y');
        $now_month = $now->format('m');
        $now_day = $now->format('d');
        //$now_year = "2023";
        //$now_month = "10";

        //クエリパラメーター取得
        if(isset($request->cur_user_id)){
            $cur_user_id=$request->cur_user_id;
        }
        else{
            $cur_user_id=1;
        }

        if(isset($request->cur_year)){
            $cur_year=$request->cur_year;
        }
        else{
            $cur_year=$now_year;
        }
        if(isset($request->cur_month)){
            $cur_month=$request->cur_month;
            if ($cur_month==13){
                $cur_year++;
                $cur_month=1;
            }
            elseif($cur_month==0){
                $cur_year--;
                $cur_month=12;
            }
        }
        else{
            $cur_month=$now_month;
        }

        //lastday求める
        $month_lastday = date('d',strtotime($cur_year.'-'.$cur_month.' last day of this month'));
        //$month_lastday=30;

        $timestamp = Timestamp::select([
            'user_id',
            'act_datetime',
            'in_out',
        ])
        ->from('timestamps')
        ->where('act_datetime','like', $now_year.'-'.sprintf('%02d', $now_month).'-'.sprintf('%02d', $now_day).' %')
        ->where('user_id','=',\Auth::user()->id)
        ->first();

        
        if(isset($timestamp)){
            $timestamp_datetime = $timestamp->act_datetime;
            $timestamp_in_out = $timestamp->in_out;
        }else{
            $timestamp_datetime = null;
            $timestamp_in_out = null;
        }        

        // usersテーブルを取得 　これが無いとsidebar表示する際エラーになる
        $users = User::all();
        return view('timestamp_index',compact('users'))
        ->with('timestamp_datetime',$timestamp_datetime)
        ->with('timestamp_in_out',$timestamp_in_out)
        ->with('user_name',\Auth::user()->name); //ログイン者名
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
    public function show(Timestamp $timestamp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timestamp $timestamp)
    {
        return redirect()
            ->route('timestamp.index'); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        if ($request->has('button1')) {
            $message = 'ボタン1が押されました';
            $in_out = 1;
        } elseif ($request->has('button2')) {
            $message = 'ボタン2が押されました';
            $in_out = 2;
        } else {
            $message = 'ボタンは押されませんでした';
            $in_out = 0;
        }


        // 現在の日時を取得
        $now = Carbon::now();
        $now_format = $now->format('Y-m-d H:i:s');

        echo rand();

        //dd($request->in_out);
        //indexメソッドで取得した当該月のworktimesテーブル(1)を取得する。
        //requestでWebの勤務表データのnameに対するvalue(2)を取得する。
        //$month_lastday = date('d',strtotime($cur_year.'-'.$cur_month.'last day of this month'));

        Timestamp::create([
            'user_id' => \Auth::user()->id,
            'act_datetime' => $now,
            //'in_out' => $request -> input('in_out'),
            'in_out' => $in_out,
            //'user_id' => $cur_user_id,
        ]);
        
        //Log::debug(print_r($worktimes, true));
        //Log::debug($worktimes);

        return redirect()
            ->route('timestamp.index'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Worktime $worktime)
    {
        //
    }
}
