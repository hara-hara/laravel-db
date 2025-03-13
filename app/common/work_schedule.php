<?php
namespace app\common;
use App\Models\Worktime;

class work_schedule
{

 
    /*
    //プロパティの宣言
    public $変数名 = 値;
 
    //メソッドの宣言
    public function メソッド名(){
        //メソッド内処理...
    }
    */

    private $cur_year = 2025;
    private $cur_month = 2;
    private $cur_member_id = 1;

    public $get_day_week[];

    
    public function __construct($cur_year, $cur_mont, $cur_member_id){
        $this->set_para($cur_year, $cur_month, $cur_member_id);
    }

    public function set_para($cur_year, $cur_month, $cur_member_id){
        $this->cur_year = $cur_year;
        $this->cur_mont = $cur_month;
        $this->cur_member_id = $cur_member_id;
    }



    public function work_type($day){

    }

  
    private function get_worktimes()
    {
        $worktimes = Worktime::select([
            'b.work_date',
            'b.real_work_start',
            'b.real_work_end',
            'b.result_work_start',
            'b.result_work_end',
            'b.member_id',
            'b.work_type',
            'r.str as work_type_ch', // work_typeがNULLだとテーブル取得しない
        ])
        ->from('worktimes as b')
        ->leftjoin('worktypes as r', function($join) {
            $join->on('b.work_type', '=', 'r.id');
        })
        ->where('b.member_id','=',$cur_member_id)
        ->whereYear('b.work_date', $cur_year)
        ->whereMonth('b.work_date', $cur_month)
        ->orderBy('b.work_date', 'ASC')
        ->paginate(31);// bladeで$worktimes->links('pagination::bootstrap-5')があるからこれ無いとエラー。


        $work_type_no=1;
        // 現在の日時を取得
        $now = Carbon::now();
        $now_format = $now->format('Y-m-d H:i:s');
        $now_year = $now->format('Y');
        $now_month = $now->format('m');


        //クエリパラメーター取得
        // ログイン中のユーザID
        $cur_user_id=\Auth::user()->id;

        // 開いている勤務表のメンバID
        if(isset($request->cur_member_id)){
            $cur_member_id=$request->cur_member_id;
        }
        else{
            $cur_member_id=\Auth::user()->id;
        }

        // 開いている勤務表の年
        if(isset($request->cur_year)){
            $cur_year=$request->cur_year;
        }
        else{
            $cur_year=$now_year;
        }

        // 開いている勤務表の月
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
        // 開いている勤務表のメンバーID
        if(isset($request->cur_member_id)){
            $cur_member_id=$request->cur_member_id;            
        }
        else{
            $cur_member_id=$cur_user_id;    
        }

        //lastday求める
        $month_lastday = date('d',strtotime($cur_year.'-'.$cur_month.' last day of this month'));

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
        ->where('user_id','=',intval($cur_member_id))
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
        ->where('b.member_id','=',$cur_member_id)
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
            'm.afternoonoff_times',
        ])
        ->from('master_worktime_types as m')
        ->where('m.id','=',$user_group_type->master_id)
        ->first();

        /* 可能就業時間帯、基本就業時間帯(G:i 表記出力) */
        $able_worktime_start = date('G:i',strtotime($master_worktime_type->able_worktime_start));
        $able_worktime_end = date('G:i',strtotime($master_worktime_type->able_worktime_end));
        $basic_worktime_start = date('G:i',strtotime($master_worktime_type->basic_worktime_start));
        $basic_worktime_end = date('G:i',strtotime($master_worktime_type->basic_worktime_end));

        /* カレント月の回数、時間を集計し変数に格納 */
        $workday_counts=0;              //出勤日数
        $v_dayoff_counts=0;             //欠勤日数
        $use_dayoff_counts=0;           //有休取得日数
        $use_am_dayoff_counts=0;        //午前半休取得日数
        $use_pm_dayoff_counts=0;        //午後半休取得日数
        $use_dayoff_hours=0;            //有休取得時間(H)
        //1:出勤
        //2:休暇
        //3:午前半休
        //4:午後半休
        //5:施設外
        //6:代休
        //7:欠勤
        //8:休日

        
        $kinType=0;


        //基本就業時間 =  基本就業時間帯(終了) - 基本就業時間帯(開始) - 休憩時間 
        $basic_worktimes = (strtotime($master_worktime_type->basic_worktime_end) - strtotime($master_worktime_type->basic_worktime_start)) / 3600 - $master_worktime_type->lunch_break_times; 
        //休暇の工数
        $dayoff_times = $master_worktime_type->dayoff_times;
        //午前半休の工数
        $morningoff_times = $master_worktime_type->morningoff_times;
        //午後半休の工数
        $afternoonoff_times = $master_worktime_type->afternoonoff_times;

        //可能就業時間帯と基本就業時間帯をG:iの形式で格納
        $able_worktime_start = date('G:i',strtotime($master_worktime_type->able_worktime_start));
        $able_worktime_end = date('G:i',strtotime($master_worktime_type->able_worktime_end));
        $basic_worktime_start = date('G:i',strtotime($master_worktime_type->basic_worktime_start));
        $basic_worktime_end = date('G:i',strtotime($master_worktime_type->basic_worktime_end));        

        $total_work_hours=0;            //総労働時間(H)
        $total_overtime_hours=0;        //総残業時間(H)
        $total_law_time_hours=0;        //総法定内残業時間(H)
        $total_law_time_outer_hours=0;  //総法定外内残業時間(H)
        $total_worktime_hours=0;        //総就業時間(H)


        /* 日付毎に各種時間を計算して配列に格納 */
        $ii=0;
        if(!$worktimes->isEmpty()){

            foreach($worktimes as $worktime){
                $ii++;
                //日(いらないかも)
                $w_time_results[$ii]['day'] = date('j',strtotime($worktime->work_date));

                //区分
                $w_time_results[$ii]['work_type']  = $worktime->work_type;

                //出退時刻(出社、退社)、就業時刻(開始、終了)
                if($worktime->result_work_start != ""){
                    $w_time_results[$ii]['result_work_start']  = date('G:i',strtotime($worktime->result_work_start));
                    if(strtotime($w_time_results[$ii]['result_work_start'] ) < strtotime($able_worktime_start)){
                        $w_time_results[$ii]['real_work_start'] = $able_worktime_start;   
                    }
                    elseif(strtotime($w_time_results[$ii]['result_work_start'] ) >= strtotime("12:00") && strtotime($w_time_results[$ii]['result_work_start'] ) < strtotime("13:00")){
                        $w_time_results[$ii]['real_work_start'] = "13:00";
                    }else{
                        $w_time_results[$ii]['real_work_start'] = $w_time_results[$ii]['result_work_start'] ;
                    }
                }
                else{
                    $w_time_results[$ii]['result_work_start']  = "";
                    $w_time_results[$ii]['real_work_start'] = "";
                }
                if($worktime->result_work_end != ""){
                    $w_time_results[$ii]['result_work_end']  = date('G:i',strtotime($worktime->result_work_end));
                    if(strtotime($w_time_results[$ii]['result_work_end'] ) >= strtotime($able_worktime_end)){
                        $w_time_results[$ii]['real_work_end'] = $able_worktime_end;   
                    }
                    elseif(strtotime($w_time_results[$ii]['result_work_end'] ) > strtotime("12:00") && strtotime($w_time_results[$ii]['result_work_end'] ) <= strtotime("13:00")){
                        $w_time_results[$ii]['real_work_end'] = "12:00";
                    }else{
                        $w_time_results[$ii]['real_work_end'] = $w_time_results[$ii]['result_work_end'] ;
                    }
                }
                else{
                    $w_time_results[$ii]['result_work_end'] = "";
                    $w_time_results[$ii]['real_work_end'] = "";
                }

                //1日毎の就業時間
                if($w_time_results[$ii]['real_work_start'] =="" || $w_time_results[$ii]['real_work_end'] ==""){
                    $w_time_results[$ii]['roudou_time']=0;
                    $w_time_results[$ii]['zangyou_time']=0;
                    $w_time_results[$ii]['houteinai_time']=0;
                    $w_time_results[$ii]['houteigai_time']=0;
                }
                else{
                    //労働時間
                    if(strtotime($w_time_results[$ii]['real_work_start']) >= strtotime("13:00") || strtotime($w_time_results[$ii]['real_work_end']) <= strtotime("12:00")){
                        $w_time_results[$ii]['roudou_time']=ceil((strtotime($w_time_results[$ii]['real_work_end']) - strtotime($w_time_results[$ii]['real_work_start']) )/ 36) / 100 ;
                    }
                    else{
                        $w_time_results[$ii]['roudou_time']=ceil((strtotime($w_time_results[$ii]['real_work_end']) - strtotime($w_time_results[$ii]['real_work_start']) )/ 36) / 100 - 1 ;
                    }
                    //残業時間
                    if($worktime->work_type == 2){ //休暇
                        $w_time_results[$ii]['zangyou_time'] = $dayoff_times + $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }elseif($worktime->work_type == 3){ //午前半休
                        $w_time_results[$ii]['zangyou_time'] = $morningoff_times + $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }elseif($worktime->work_type == 4){ //午後半休
                        $w_time_results[$ii]['zangyou_time'] = $afternoonoff_times + $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }else{
                        $w_time_results[$ii]['zangyou_time'] = $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }
                    if($w_time_results[$ii]['zangyou_time'] < 0){
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

                //出勤日数
                if($worktime->work_type==1 || $worktime->work_type==3 || $worktime->work_type==4 || $worktime->work_type==5){
                    $workday_counts++;
                }
                //欠勤日数
                if($worktime->work_type==7){
                    $v_dayoff_counts++;
                }
                //有休取得日数
                if($worktime->work_type==2){
                    $use_dayoff_counts++;
                }
                //午前半休取得日数
                if($worktime->work_type==3){
                    $use_am_dayoff_counts++;
                }            
                //午後半休取得日数
                if($worktime->work_type==4){
                    $use_pm_dayoff_counts++;
                }            
            }
        }
        else{
            //初めて入力する月はDBにデータなしのため、データ空のコレクション型になる。
            //compactで空のコレクション型を渡すとエラーになる為その場合はnullを入れる。
            for($i=1;$i<=$month_lastday;$i++){
                $w_time_results[$i]['day'] = $i;
                $w_time_results[$i]['work_type']  = "";
                $w_time_results[$i]['result_work_start'] ="";
                $w_time_results[$i]['result_work_end'] = "";
                $w_time_results[$i]['real_work_start'] = "";
                $w_time_results[$i]['real_work_end'] = "";
                $w_time_results[$i]['roudou_time'] = 0;
                $w_time_results[$i]['zangyou_time'] = 0;
                $w_time_results[$i]['houteinai_time'] = 0;
                $w_time_results[$i]['houteigai_time'] = 0;
            }
            /* カレント月の日数、時間を集計し変数に格納(既設配列から計算) */
            $worktimes = null;
            for($i=1;$i<=$month_lastday;$i++){
                $total_work_hours = $total_work_hours + $w_time_results[$i]['roudou_time'];
                $total_overtime_hours = $total_overtime_hours + $w_time_results[$i]['zangyou_time']; 
                $total_law_time_hours = $total_law_time_hours + $w_time_results[$i]['houteinai_time'];
                $total_law_time_outer_hours = $total_law_time_outer_hours + $w_time_results[$i]['houteigai_time'];
            }
            $total_worktime_hours = $total_work_hours + $use_dayoff_hours; //総就業時間(H)
        }

        //必要総就業時間(H)
        $need_total_worktimes_hours = $workday_counts * $basic_worktimes;
        //有休取得時間(H)
        $use_dayoff_hours = $use_dayoff_counts * $dayoff_times + $use_am_dayoff_counts * $morningoff_times + $use_pm_dayoff_counts * $afternoonoff_times ;


        if(empty($w_time_results)){
            $w_time_results = null;
        }
    }



    public static function common_kinmu_monthly(Request $request)
    {
        $work_type_no=1;
        // 現在の日時を取得
        $now = Carbon::now();
        $now_format = $now->format('Y-m-d H:i:s');
        $now_year = $now->format('Y');
        $now_month = $now->format('m');


        //クエリパラメーター取得
        // ログイン中のユーザID
        $cur_user_id=\Auth::user()->id;

        // 開いている勤務表のメンバID
        if(isset($request->cur_member_id)){
            $cur_member_id=$request->cur_member_id;
        }
        else{
            $cur_member_id=\Auth::user()->id;
        }

        // 開いている勤務表の年
        if(isset($request->cur_year)){
            $cur_year=$request->cur_year;
        }
        else{
            $cur_year=$now_year;
        }

        // 開いている勤務表の月
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
        // 開いている勤務表のメンバーID
        if(isset($request->cur_member_id)){
            $cur_member_id=$request->cur_member_id;            
        }
        else{
            $cur_member_id=$cur_user_id;    
        }

        //lastday求める
        $month_lastday = date('d',strtotime($cur_year.'-'.$cur_month.' last day of this month'));

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
        ->where('user_id','=',intval($cur_member_id))
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
        ->where('b.member_id','=',$cur_member_id)
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
            'm.afternoonoff_times',
        ])
        ->from('master_worktime_types as m')
        ->where('m.id','=',$user_group_type->master_id)
        ->first();

        /* 可能就業時間帯、基本就業時間帯(G:i 表記出力) */
        $able_worktime_start = date('G:i',strtotime($master_worktime_type->able_worktime_start));
        $able_worktime_end = date('G:i',strtotime($master_worktime_type->able_worktime_end));
        $basic_worktime_start = date('G:i',strtotime($master_worktime_type->basic_worktime_start));
        $basic_worktime_end = date('G:i',strtotime($master_worktime_type->basic_worktime_end));

        /* カレント月の回数、時間を集計し変数に格納 */
        $workday_counts=0;              //出勤日数
        $v_dayoff_counts=0;             //欠勤日数
        $use_dayoff_counts=0;           //有休取得日数
        $use_am_dayoff_counts=0;        //午前半休取得日数
        $use_pm_dayoff_counts=0;        //午後半休取得日数
        $use_dayoff_hours=0;            //有休取得時間(H)
        //1:出勤
        //2:休暇
        //3:午前半休
        //4:午後半休
        //5:施設外
        //6:代休
        //7:欠勤
        //8:休日

        
        $kinType=0;


        //基本就業時間 =  基本就業時間帯(終了) - 基本就業時間帯(開始) - 休憩時間 
        $basic_worktimes = (strtotime($master_worktime_type->basic_worktime_end) - strtotime($master_worktime_type->basic_worktime_start)) / 3600 - $master_worktime_type->lunch_break_times; 
        //休暇の工数
        $dayoff_times = $master_worktime_type->dayoff_times;
        //午前半休の工数
        $morningoff_times = $master_worktime_type->morningoff_times;
        //午後半休の工数
        $afternoonoff_times = $master_worktime_type->afternoonoff_times;

        //可能就業時間帯と基本就業時間帯をG:iの形式で格納
        $able_worktime_start = date('G:i',strtotime($master_worktime_type->able_worktime_start));
        $able_worktime_end = date('G:i',strtotime($master_worktime_type->able_worktime_end));
        $basic_worktime_start = date('G:i',strtotime($master_worktime_type->basic_worktime_start));
        $basic_worktime_end = date('G:i',strtotime($master_worktime_type->basic_worktime_end));        

        $total_work_hours=0;            //総労働時間(H)
        $total_overtime_hours=0;        //総残業時間(H)
        $total_law_time_hours=0;        //総法定内残業時間(H)
        $total_law_time_outer_hours=0;  //総法定外内残業時間(H)
        $total_worktime_hours=0;        //総就業時間(H)


        /* 日付毎に各種時間を計算して配列に格納 */
        $ii=0;
        if(!$worktimes->isEmpty()){

            foreach($worktimes as $worktime){
                $ii++;
                //日(いらないかも)
                $w_time_results[$ii]['day'] = date('j',strtotime($worktime->work_date));

                //区分
                $w_time_results[$ii]['work_type']  = $worktime->work_type;

                //出退時刻(出社、退社)、就業時刻(開始、終了)
                if($worktime->result_work_start != ""){
                    $w_time_results[$ii]['result_work_start']  = date('G:i',strtotime($worktime->result_work_start));
                    if(strtotime($w_time_results[$ii]['result_work_start'] ) < strtotime($able_worktime_start)){
                        $w_time_results[$ii]['real_work_start'] = $able_worktime_start;   
                    }
                    elseif(strtotime($w_time_results[$ii]['result_work_start'] ) >= strtotime("12:00") && strtotime($w_time_results[$ii]['result_work_start'] ) < strtotime("13:00")){
                        $w_time_results[$ii]['real_work_start'] = "13:00";
                    }else{
                        $w_time_results[$ii]['real_work_start'] = $w_time_results[$ii]['result_work_start'] ;
                    }
                }
                else{
                    $w_time_results[$ii]['result_work_start']  = "";
                    $w_time_results[$ii]['real_work_start'] = "";
                }
                if($worktime->result_work_end != ""){
                    $w_time_results[$ii]['result_work_end']  = date('G:i',strtotime($worktime->result_work_end));
                    if(strtotime($w_time_results[$ii]['result_work_end'] ) >= strtotime($able_worktime_end)){
                        $w_time_results[$ii]['real_work_end'] = $able_worktime_end;   
                    }
                    elseif(strtotime($w_time_results[$ii]['result_work_end'] ) > strtotime("12:00") && strtotime($w_time_results[$ii]['result_work_end'] ) <= strtotime("13:00")){
                        $w_time_results[$ii]['real_work_end'] = "12:00";
                    }else{
                        $w_time_results[$ii]['real_work_end'] = $w_time_results[$ii]['result_work_end'] ;
                    }
                }
                else{
                    $w_time_results[$ii]['result_work_end'] = "";
                    $w_time_results[$ii]['real_work_end'] = "";
                }

                //1日毎の就業時間
                if($w_time_results[$ii]['real_work_start'] =="" || $w_time_results[$ii]['real_work_end'] ==""){
                    $w_time_results[$ii]['roudou_time']=0;
                    $w_time_results[$ii]['zangyou_time']=0;
                    $w_time_results[$ii]['houteinai_time']=0;
                    $w_time_results[$ii]['houteigai_time']=0;
                }
                else{
                    //労働時間
                    if(strtotime($w_time_results[$ii]['real_work_start']) >= strtotime("13:00") || strtotime($w_time_results[$ii]['real_work_end']) <= strtotime("12:00")){
                        $w_time_results[$ii]['roudou_time']=ceil((strtotime($w_time_results[$ii]['real_work_end']) - strtotime($w_time_results[$ii]['real_work_start']) )/ 36) / 100 ;
                    }
                    else{
                        $w_time_results[$ii]['roudou_time']=ceil((strtotime($w_time_results[$ii]['real_work_end']) - strtotime($w_time_results[$ii]['real_work_start']) )/ 36) / 100 - 1 ;
                    }
                    //残業時間
                    if($worktime->work_type == 2){ //休暇
                        $w_time_results[$ii]['zangyou_time'] = $dayoff_times + $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }elseif($worktime->work_type == 3){ //午前半休
                        $w_time_results[$ii]['zangyou_time'] = $morningoff_times + $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }elseif($worktime->work_type == 4){ //午後半休
                        $w_time_results[$ii]['zangyou_time'] = $afternoonoff_times + $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }else{
                        $w_time_results[$ii]['zangyou_time'] = $w_time_results[$ii]['roudou_time'] - $basic_worktimes;
                    }
                    if($w_time_results[$ii]['zangyou_time'] < 0){
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

                //出勤日数
                if($worktime->work_type==1 || $worktime->work_type==3 || $worktime->work_type==4 || $worktime->work_type==5){
                    $workday_counts++;
                }
                //欠勤日数
                if($worktime->work_type==7){
                    $v_dayoff_counts++;
                }
                //有休取得日数
                if($worktime->work_type==2){
                    $use_dayoff_counts++;
                }
                //午前半休取得日数
                if($worktime->work_type==3){
                    $use_am_dayoff_counts++;
                }            
                //午後半休取得日数
                if($worktime->work_type==4){
                    $use_pm_dayoff_counts++;
                }            
            }
        }
        else{
            //初めて入力する月はDBにデータなしのため、データ空のコレクション型になる。
            //compactで空のコレクション型を渡すとエラーになる為その場合はnullを入れる。
            for($i=1;$i<=$month_lastday;$i++){
                $w_time_results[$i]['day'] = $i;
                $w_time_results[$i]['work_type']  = "";
                $w_time_results[$i]['result_work_start'] ="";
                $w_time_results[$i]['result_work_end'] = "";
                $w_time_results[$i]['real_work_start'] = "";
                $w_time_results[$i]['real_work_end'] = "";
                $w_time_results[$i]['roudou_time'] = 0;
                $w_time_results[$i]['zangyou_time'] = 0;
                $w_time_results[$i]['houteinai_time'] = 0;
                $w_time_results[$i]['houteigai_time'] = 0;
            }
            /* カレント月の日数、時間を集計し変数に格納(既設配列から計算) */
            $worktimes = null;
            for($i=1;$i<=$month_lastday;$i++){
                $total_work_hours = $total_work_hours + $w_time_results[$i]['roudou_time'];
                $total_overtime_hours = $total_overtime_hours + $w_time_results[$i]['zangyou_time']; 
                $total_law_time_hours = $total_law_time_hours + $w_time_results[$i]['houteinai_time'];
                $total_law_time_outer_hours = $total_law_time_outer_hours + $w_time_results[$i]['houteigai_time'];
            }
            $total_worktime_hours = $total_work_hours + $use_dayoff_hours; //総就業時間(H)
        }

        //必要総就業時間(H)
        $need_total_worktimes_hours = $workday_counts * $basic_worktimes;
        //有休取得時間(H)
        $use_dayoff_hours = $use_dayoff_counts * $dayoff_times + $use_am_dayoff_counts * $morningoff_times + $use_pm_dayoff_counts * $afternoonoff_times ;


        if(empty($w_time_results)){
            $w_time_results = null;
        }
    }

}