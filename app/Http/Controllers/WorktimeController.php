<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; //デバッグ用
use App\Models\Worktime;
use App\Models\Worktype;
use App\Models\User;
use App\Models\Master_worktime_type;
use App\Models\User_group_type;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorktimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $work_type_no=1;
        // 現在の日時を取得
        $now = Carbon::now();
        $now_format = $now->format('Y-m-d H:i:s');
        $now_year = $now->format('Y');
        $now_month = $now->format('m');
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

        // usersテーブルを取得
        $users = User::all();

        // worktypesテーブルを取得
        $worktypes = Worktype::all();
 
 
        // ユーザのmasterテーブルidを取得
        $user_group_type = User_group_type::select([
            'user_id',
            'group_id',
            'master_id',
        ])
        ->from('user_group_types as b')
        ->where('user_id','=',$cur_user_id)
        ->first();
                
        $worktimes = Worktime::select([
            'b.work_date',
            'b.real_work_start',
            'b.real_work_end',
            'b.result_work_start',
            'b.result_work_end',
            'b.member_id',
            'b.work_type',
            //'r.str as work_type_ch', // work_typeがNULLだとテーブル取得しない
        ])
        ->from('worktimes as b')
        //->join('worktypes as r', function($join) {
        //    $join->on('b.work_type', '=', 'r.id');
        //})
        ->orderBy('b.work_date', 'ASC')
        ->where('b.member_id','=',$cur_user_id)
        ->where('b.work_date','like',$cur_year.'-'.sprintf('%02d', $cur_month).'-%')
        ->paginate(31);// bladeで$worktimes->links('pagination::bootstrap-5')があるからこれ無いとエラー。

        
        $master_worktime_type = Master_worktime_type::select([
            'm.id',
            'm.able_worktime_start',
            'm.able_worktime_end',
            'm.basic_worktime_start',
            'm.basic_worktime_end',
            'm.lunch_break_times',
            'm.dayoff_times',
            'm.morningoff_times',
            'm.aftenoonoff_times',
        ])
        ->from('master_worktime_types as m')
        ->where('m.id','=',$user_group_type->master_id)
        ->first();

        $kinType=0;


        //基本就業時間 =  基本就業時間帯(終了) - 基本就業時間帯(開始) - 休憩時間 
        $basic_work_times = (strtotime($master_worktime_type->basic_worktime_end) - strtotime($master_worktime_type->basic_worktime_start)) / 3600 - $master_worktime_type->dayoff_times; 
        //休暇の工数
        $dayoff_times = $master_worktime_type->dayoff_times;
        //午前半休の工数
        $morningoff_times = $master_worktime_type->morningoff_times;
        //午後半休の工数
        $aftenoonoff_times = $master_worktime_type->aftenoonoff_times;


        /* 日付毎に各種時間を計算して配列に格納 */
        $ii=0;
        foreach($worktimes as $worktime){
            $ii++;

            //労働時間
            if($worktime->result_work_start=="" || $worktime->result_work_end==""){
                $w_time_results[$ii]['roudou_time']=0;
            }elseif(strtotime($worktime->result_work_start) >= strtotime("13:00") || strtotime($worktime->result_work_end) <= strtotime("12:00")){
                $w_time_results[$ii]['roudou_time']=(strtotime($worktime->result_work_end) - strtotime($worktime->result_work_start) )/ 3600 -1;
            }else{
                $w_time_results[$ii]['roudou_time']=(strtotime($worktime->result_work_end) - strtotime($worktime->result_work_start) )/ 3600 -1;
            }

            //残業時間
            if($worktime->work_type == 2){ //休暇
                $w_time_results[$ii]['zangyou_time'] = $dayoff_times + $w_time_results[$ii]['roudou_time'] - $basic_work_times;
            }elseif($worktime->work_type == 3){ //午前半休
                $w_time_results[$ii]['zangyou_time'] = $morningoff_times + $w_time_results[$ii]['roudou_time'] - $basic_work_times;
            }elseif($worktime->work_type == 4){ //午後半休
                $w_time_results[$ii]['zangyou_time'] = $aftenoonoff_times + $w_time_results[$ii]['roudou_time'] - $basic_work_times;
            }else{
                $w_time_results[$ii]['zangyou_time'] = 0;
            }
            
            //法定内残業時間
            if($w_time_results[$ii]['zangyou_time'] == 0){
                $w_time_results[$ii]['houteinai_time'] = 0;
            }elseif($w_time_results[$ii]['zangyou_time'] <=1 && $w_time_results[$ii]['zangyou_time'] >= 0){
                $w_time_results[$ii]['houteinai_time'] = $w_time_results[$ii]['zangyou_time'];
            }else{
                $w_time_results[$ii]['houteinai_time'] = 1;
            }

            //法定外残業時間
            if($w_time_results[$ii]['zangyou_time'] <= 0){
                $w_time_results[$ii]['houteigai_time'] = 0;
            }else{
                $w_time_results[$ii]['houteigai_time'] = $w_time_results[$ii]['zangyou_time'] - $w_time_results[$ii]['houteinai_time'];
            }
 
        }

        /* カレント月の回数、時間を集計し変数に格納 */
        $workday_counts=0;              //出勤日数
        $v_dayoff_counts=0;             //欠勤日数
        $use_dayoff_counts=0;           //有休取得日数
        $use_dayoff_hours=0;            //有休取得時間(H)
        foreach($worktimes as $worktime){
            if($worktime->work_type==1 or $worktime->work_type==2){
                $workday_counts++;
                $v_dayoff_counts++;
                $use_dayoff_counts++;
                $use_dayoff_hours++;
            }
        }
        $need_total_worktimes_hours = $workday_counts * $basic_work_times; //必要総就業時間(H)

        /* カレント月の回数、時間を集計し変数に格納(既設配列から計算) */
        $total_work_hours=0;            //総労働時間(H)
        $total_overtime_hours=0;        //総残業時間(H)
        $total_law_time_hours=0;        //総法定内残業時間(H)
        $total_law_time_outer_hours=0;  //総法定外内残業時間(H)
        for($i=1;$i<=$ii;$i++){
            $total_work_hours = $total_work_hours + $w_time_results[$i]['roudou_time'];
            $total_overtime_hours = $total_overtime_hours + $w_time_results[$i]['zangyou_time']; 
            $total_law_time_hours = $total_law_time_hours + $w_time_results[$i]['houteinai_time'];
            $total_law_time_outer_hours = $total_law_time_outer_hours + $w_time_results[$i]['houteigai_time'];
        }
        $total_worktime_hours = $total_work_hours + $use_dayoff_hours; //総就業時間(H)

        //初めて入力する月はDBにデータなしのため、データ空のコレクション型になる。
        //compactで空のコレクション型を渡すとエラーになる為その場合はnullを入れる。
        if($worktimes->isEmpty()){
            $worktimes = null;
        }
        if(empty($w_time_results)){
            $w_time_results = null;
        }

        //ログインしていない場合、エラーが出ますので、以下のように処理を分けます。
        //compact内空だったらエラーになるので後で対処を。
        if(isset(\Auth::user()->name)){
            return view('worktime_index',compact('worktimes','w_time_results','master_worktime_type','worktypes','users'))
                ->with('page_id',request()->page)
                ->with('i', (request()->input('page', 1) - 1) * 5)
                ->with('cur_user_id',$cur_user_id)
                ->with('cur_year',$cur_year)
                ->with('cur_month',$cur_month)
                ->with('month_lastday',$month_lastday)
                ->with('workday_counts',$workday_counts)
                ->with('v_dayoff_counts',$v_dayoff_counts)
                ->with('use_dayoff_counts',$use_dayoff_counts)
                ->with('use_dayoff_hours',$use_dayoff_hours)
                ->with('need_total_worktimes_hours',$need_total_worktimes_hours)
                ->with('total_work_hours',$total_work_hours)
                ->with('total_overtime_hours',$total_overtime_hours)
                ->with('total_law_time_hours',$total_law_time_hours)
                ->with('total_law_time_outer_hours',$total_law_time_outer_hours)
                ->with('total_worktime_hours',$total_worktime_hours)
                ->with('user_name',\Auth::user()->name); //ログイン者名
        }else{
            return view('worktime_index',compact('worktimes','w_time_results','master_worktime_type','worktypes','users'))
                ->with('page_id',request()->page)
                ->with('i', (request()->input('page', 1) - 1) * 5)
                ->with('cur_user_id',$cur_user_id)
                ->with('cur_year',$cur_year)
                ->with('cur_month',$cur_month)
                ->with('month_lastday',$month_lastday)
                ->with('workday_counts',$workday_counts)
                ->with('v_dayoff_counts',$v_dayoff_counts)
                ->with('use_dayoff_counts',$use_dayoff_counts)
                ->with('use_dayoff_hours',$use_dayoff_hours)
                ->with('need_total_worktimes_hours',$need_total_worktimes_hours)
                ->with('total_work_hours',$total_work_hours)
                ->with('total_overtime_hours',$total_overtime_hours)
                ->with('total_law_time_hours',$total_law_time_hours)
                ->with('total_law_time_outer_hours',$total_law_time_outer_hours)
                ->with('total_worktime_hours',$total_worktime_hours);
        }

    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //URLパラメータuser_idを取得
        if(isset($request->user_id)){
            $member_no=$request->user_id;
        }
        else{
            $member_no=1;
        }

       //URLパラメータymを取得
       if(isset($request->ym)){
            $ym=$request->ym;
        }
        else{
            $ym="202311";
        }

        //テスト用　のちにユーザ情報に設ける
        $work_type_no=1;

        $worktypes = Worktype::all();



        //$worktimes = Bunbougu::latest()->paginate(5);
        $worktimes = Worktime::select([
            'b.work_date',
            'b.real_work_start',
            'b.real_work_end',
            'b.result_work_start',
            'b.result_work_end',
            'b.member_id',
            'b.work_type',
            'r.str as work_type_ch',
        ])
        ->from('worktimes as b')
        ->join('worktypes as r', function($join) {
            $join->on('b.work_type', '=', 'r.id');
        })
        ->orderBy('b.work_date', 'ASC')
        ->where('b.member_id','=',$member_no)
        ->where('b.work_date','like',$ym . "%")
        ->paginate(34); //この行ないと読めない

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
    public function update(Request $request)
    {
        //target_year,target_monthを取得する
        $cur_year = $request->input('cur_year');
        $cur_month = $request->input('cur_month');
        $cur_user_id = $request->input('cur_user_id');

        //indexメソッドで取得した当該月のworktimesテーブル(1)を取得する。
        //requestでWebの勤務表データのnameに対するvalue(2)を取得する。
        $month_lastday = date('d',strtotime($cur_year.'-'.$cur_month.'last day of this month'));

        //当該月の日数だけ回して、(1)と(2)を比較し、違っている場合はバリデーションして
        /*
        for($i=1;$i<=$month_lastday;$i++){
            $w_start = "result_work_start-" . $i;
            $w_end = "result_work_end-" . $i;
            $w_type = "work_type-" . $i;
            $w_date = "work_date-" . $i;

            
            $request->validate([
                $w_start => 'required|max:20',
                $w_end => 'required|max:20',
                $w_type => 'required|max:20',
                $w_date => 'required|max:20',
            ]);
        }
        */

 



        //worktimesテーブルのデータをupdateし、メッセージBOXを出して結果を表示後、indexメソッドにより勤務表を表示する

        for($i=0;$i<$month_lastday;$i++){
            $before_worktype[$i] = $request -> input('hidden_work_type-' . $i+1);
            $before_result_work_start[$i] = $request -> input('hidden_result_work_start-' . $i+1);
            $before_result_work_end[$i] = $request -> input('hidden_result_work_end-' . $i+1);
            $after_worktype[$i] = $request -> input('work_type-' . $i+1);
            $after_result_work_start[$i] = $request -> input('result_work_start-' . $i+1);
            $after_result_work_end[$i] = $request -> input('result_work_end-' . $i+1);
            \Debugbar::addMessage("before_worktype[$i]=".$before_worktype[$i]);
            \Debugbar::debug("before_result_work_start[$i]=".$before_result_work_start[$i]);
            \Debugbar::debug("before_result_work_end[$i]=".$before_result_work_end[$i]);
            \Debugbar::debug("after_worktype[$i]=".$after_worktype[$i]);
            \Debugbar::debug("after_result_work_start[$i]=".$after_result_work_start[$i]);
            \Debugbar::debug("after_result_work_end[$i]=".$after_result_work_end[$i]);

            //beforeとafterを比較し違えばDB登録(worktype)
            if($before_worktype[$i] != $after_worktype[$i]){
                if(DB::table('worktimes')
                ->where('work_date', $cur_year.'-'.$cur_month.'-'.$i+1)
                ->where('member_id',$cur_user_id)
                ->exists()){
                    Worktime::where('work_date', $cur_year.'-'.$cur_month.'-'.$i+1)
                    ->where('member_id',$cur_user_id)
                    ->update(['work_type'=>$after_worktype[$i]]);
                }else{
                    for ($j=0;$j<$month_lastday;$j++){
                        Worktime::create([
                            'member_id' => $request -> input('cur_user_id'),
                            'work_date' => $request -> input('work_date-' . $j+1),
                            'work_type' => $request -> input('work_type-' . $j+1),
                            'result_work_start' => $request -> input('result_work_start-' . $j+1),
                            'result_work_end' => $request -> input('result_work_end-' . $j+1),
                            'user_id' => $cur_user_id,
                        ]);
    
                    }

                }
                

            }

            //beforeとafterを比較し違えばDB登録(result_work_start)
            if($before_result_work_start[$i] != $after_result_work_start[$i]){
                if(DB::table('worktimes')->where('work_date', $cur_year.'-'.$cur_month.'-'.$i+1)->exists()){
                        Worktime::where('work_date', $cur_year.'-'.$cur_month.'-'.$i+1)
                        ->where('member_id',$cur_user_id)
                        ->update(['result_work_start'=>$after_result_work_start[$i]]);
                }else{
                        for ($j=0;$j<$month_lastday;$j++){
                            Worktime::create([
                                'member_id' => $request -> input('cur_user_id'),
                                'work_date' => $request -> input('work_date-' . $j+1),
                                'work_type' => $request -> input('work_type-' . $j+1),
                                'result_work_start' => $request -> input('result_work_start-' . $j+1),
                                'result_work_end' => $request -> input('result_work_end-' . $j+1),
                                'user_id' => $cur_user_id,
                            ]);
        
                        }
    
                }
                
            }

            //beforeとafterを比較し違えばDB登録(result_work_end)
            if($before_result_work_end[$i] != $after_result_work_end[$i]){
                if(DB::table('worktimes')->where('work_date', $cur_year.'-'.$cur_month.'-'.$i+1)->exists()){
                        Worktime::where('work_date', $cur_year.'-'.$cur_month.'-'.$i+1)
                        ->where('member_id',$cur_user_id)
                        ->update(['result_work_end'=>$after_result_work_end[$i]]);
                }else{
                        for ($j=0;$j<$month_lastday;$j++){
                            Worktime::create([
                                'member_id' => $request -> input('cur_user_id'),
                                'work_date' => $request -> input('work_date-' . $j+1),
                                'work_type' => $request -> input('work_type-' . $j+1),
                                'result_work_start' => $request -> input('result_work_start-' . $j+1),
                                'result_work_end' => $request -> input('result_work_end-' . $j+1),
                                'user_id' => $cur_user_id,
                            ]);
        
                        }
    
                }
            }
        }

        



        
        //Log::debug(print_r($worktimes, true));
        //Log::debug($worktimes);

        return redirect()
            ->route('worktime.index',
                [
                    'cur_year'=>$cur_year,
                    'cur_month'=>$cur_month,
                    'cur_user_id'=>$cur_user_id
                ]
            ); //★
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
