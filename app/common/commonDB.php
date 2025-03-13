<?php
namespace app\common;
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

use Illuminate\Console\Command;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Address;

use App\Mail\MailSend;



class commonDB
{

    public static function common_kinmuhyou($cur_member_id,$cur_year,$cur_month){    
        $kinmuhyou_day = self::common_kinmuhyou_day($cur_member_id,$cur_year,$cur_month);
        $kinmuhyou_data = self::common_kinmuhyou_month($kinmuhyou_day,$cur_member_id,$cur_year,$cur_month);
        return [$kinmuhyou_day,$kinmuhyou_data];
    }
        

    //勤務表の日ごと行の情報を取得
    public static function common_kinmuhyou_day($cur_member_id,$cur_year,$cur_month){    

        //lastday求める
        $month_lastday = date('d',strtotime($cur_year.'-'.$cur_month.' last day of this month'));

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
        
        $hoge="";
        $worktimes = Worktime::select([
            'b.work_date',
            'b.real_work_start',
            'b.real_work_end',
            'b.result_work_start',
            'b.result_work_end',
            'b.member_id',
            'b.work_type',
            'r.str as work_type_ch',
            DB::raw("'$hoge' as day"),
            DB::raw("'$hoge' as roudou_time"),
            DB::raw("'$hoge' as zangyou_time"),
            DB::raw("'$hoge' as houteinai_time"),
            DB::raw("'$hoge' as houteigai_time"),
        ])
        ->from('worktimes as b')
        ->leftjoin('worktypes as r', function($join) { // joinの場合、work_typeがNULLだとテーブル取得しないがleftjoinならOK
            $join->on('b.work_type', '=', 'r.id');
        })
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


        // 日付毎に各種時間を計算して配列に格納 
        if(!$worktimes->isEmpty()){

            foreach($worktimes as $worktime){
                //日(いらないかも)
                $worktime->day = date('j',strtotime($worktime->work_date));


                //出退時刻(出社、退社)、就業時刻(開始、終了)
                if($worktime->result_work_start != ""){
                    $worktime->result_work_start  = date('G:i',strtotime($worktime->result_work_start));
                    if(strtotime($worktime->result_work_start) < strtotime($able_worktime_start)){
                        $worktime->real_work_start = $able_worktime_start;   
                    }
                    elseif(strtotime($worktime->result_work_start ) >= strtotime("12:00") && strtotime($worktime->result_work_start ) < strtotime("13:00")){
                        $worktime->real_work_start = "13:00";
                    }else{
                        $worktime->real_work_start = $worktime->result_work_start ;
                    }
                }
                else{
                    $worktime->result_work_start = "";
                    $worktime->real_work_start = "";
                }
                if($worktime->result_work_end != ""){
                    $worktime->result_work_end = date('G:i',strtotime($worktime->result_work_end));
                    if(strtotime($worktime->result_work_end ) >= strtotime($able_worktime_end)){
                        $worktime->real_work_end = $able_worktime_end;   
                    }
                    elseif(strtotime($worktime->result_work_end ) > strtotime("12:00") && strtotime($worktime->result_work_end ) <= strtotime("13:00")){
                        $worktime->real_work_end = "12:00";
                    }else{
                        $worktime->real_work_end = $worktime->result_work_end ;
                    }
                }
                else{
                    $worktime->result_work_end = "";
                    $worktime->real_work_end = "";
                }

                //1日毎の就業時間
                if($worktime->real_work_start =="" || $worktime->real_work_end ==""){
                    $worktime->roudou_time = 0;
                    $worktime->zangyou_time = 0;
                    $worktime->houteinai_time = 0;
                    $worktime->houteigai_time = 0;
                }
                else{
                    //労働時間
                    if(strtotime($worktime->real_work_start) >= strtotime("13:00") || strtotime($worktime->real_work_end) <= strtotime("12:00")){
                        $worktime->roudou_time = ceil((strtotime($worktime->real_work_end) - strtotime($worktime->real_work_start) )/ 36) / 100 ;
                    }
                    else{
                        $worktime->roudou_time = ceil((strtotime($worktime->real_work_end) - strtotime($worktime->real_work_start) )/ 36) / 100 - 1 ;
                    }
                    //残業時間
                    if($worktime->work_type == 2){ //休暇
                        $worktime->zangyou_time = $dayoff_times + $worktime->roudou_time - $basic_worktimes;
                    }elseif($worktime->work_type == 3){ //午前半休
                        $worktime->zangyou_time = $morningoff_times + $worktime->roudou_time - $basic_worktimes;
                    }elseif($worktime->work_type == 4){ //午後半休
                        $worktime->zangyou_time = $afternoonoff_times + $worktime->roudou_time - $basic_worktimes;
                    }else{
                        $worktime->zangyou_time = $worktime->roudou_time - $basic_worktimes;
                    }
                    if($worktime->zangyou_time < 0){
                        $worktime->zangyou_time = 0;
                    }

                    //法定内残業時間
                    if($worktime->zangyou_time == 0){
                        $worktime->houteinai_time = 0;
                    }elseif($worktime->zangyou_time <=1 && $worktime->zangyou_time >= 0){
                        $worktime->houteinai_time = $worktime->zangyou_time;
                    }else{
                        $worktime->houteinai_time = 1;
                    }

                    //法定外残業時間
                    if($worktime->zangyou_time <= 0){
                        $worktime->houteigai_time = 0;
                    }else{
                        $worktime->houteigai_time = $worktime->zangyou_time - $worktime->houteinai_time;
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
            //カレント月のテーブルデータが無い場合、ダミーデータを作る　もしかして->get()を使えばNULLはエラーにならない?
            $worktimes = Worktime::select([
                DB::raw("'$hoge' as day"),
                DB::raw("'$hoge' as work_type"),
                DB::raw("'$hoge' as work_type_ch"),
                DB::raw("'$hoge' as real_work_start"),
                DB::raw("'$hoge' as real_work_end"),
                DB::raw("'$hoge' as result_work_start"),
                DB::raw("'$hoge' as result_work_end"),
                DB::raw("'$hoge' as member_id"),
                DB::raw("'$hoge' as roudou_time"),
                DB::raw("'$hoge' as zangyou_time"),
                DB::raw("'$hoge' as houteinai_time"),
                DB::raw("'$hoge' as houteigai_time"),
            ])
            ->paginate($month_lastday);// bladeで$worktimes->links('pagination::bootstrap-5')があるからこれ無いとエラー。

            $i = 0;
            foreach($worktimes as $worktime){
                $i++;
                $worktime->day = $i;
                $worktime->work_type = "";
                $worktime->work_type_ch = "";
                $worktime->real_work_start = "";
                $worktime->real_work_end = "";
                $worktime->result_work_start = "";
                $worktime->result_work_end = "";
                $worktime->member_id = "";
                $worktime->roudou_time = 0;
                $worktime->zangyou_time = 0;
                $worktime->houteinai_time = 0;
                $worktime->houteigai_time = 0;
            }

            // カレント月の日数、時間を集計し変数に格納(既設配列から計算) 
            $total_work_hours = 0;
            $total_overtime_hours = 0;
            $total_law_time_hours = 0;
            $total_law_time_outer_hours = 0;
            $total_worktime_hours = 0;  //総就業時間(H)
        }
        return $worktimes;
    }

    //勤務表の月トータルの情報を取得
    public static function common_kinmuhyou_month($cur_worktimes,$cur_member_id,$cur_year,$cur_month){   
        // ユーザのmasterテーブルidを取得
        $user_group_type = User_group_type::select([
            'user_id',
            'group_id',
            'master_id',
        ])
        ->from('user_group_types as b')
        ->where('user_id','=',intval($cur_member_id))
        ->first();

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



        foreach($cur_worktimes as $cur_worktime){
            //日(いらないかも)
            //$w_time_results['day'] = date('j',strtotime($cur_worktime->work_date));
            $w_time_results['day'] = $cur_worktime->day;

            //区分
            $w_time_results['work_type']  = $cur_worktime->work_type;

            //出退時刻(出社、退社)、就業時刻(開始、終了)
            if($cur_worktime->result_work_start != ""){
                $w_time_results['result_work_start']  = date('G:i',strtotime($cur_worktime->result_work_start));
                if(strtotime($w_time_results['result_work_start'] ) < strtotime($able_worktime_start)){
                    $w_time_results['real_work_start'] = $able_worktime_start;   
                }
                elseif(strtotime($w_time_results['result_work_start'] ) >= strtotime("12:00") && strtotime($w_time_results['result_work_start'] ) < strtotime("13:00")){
                    $w_time_results['real_work_start'] = "13:00";
                }else{
                    $w_time_results['real_work_start'] = $w_time_results['result_work_start'] ;
                }
            }
            else{
                $w_time_results['result_work_start']  = "";
                $w_time_results['real_work_start'] = "";
            }
            if($cur_worktime->result_work_end != ""){
                $w_time_results['result_work_end']  = date('G:i',strtotime($cur_worktime->result_work_end));
                if(strtotime($w_time_results['result_work_end'] ) >= strtotime($able_worktime_end)){
                    $w_time_results['real_work_end'] = $able_worktime_end;   
                }
                elseif(strtotime($w_time_results['result_work_end'] ) > strtotime("12:00") && strtotime($w_time_results['result_work_end'] ) <= strtotime("13:00")){
                    $w_time_results['real_work_end'] = "12:00";
                }else{
                    $w_time_results['real_work_end'] = $w_time_results['result_work_end'] ;
                }
            }
            else{
                $w_time_results['result_work_end'] = "";
                $w_time_results['real_work_end'] = "";
            }

            //1日毎の就業時間
            if($w_time_results['real_work_start'] =="" || $w_time_results['real_work_end'] ==""){
                $w_time_results['roudou_time']=0;
                $w_time_results['zangyou_time']=0;
                $w_time_results['houteinai_time']=0;
                $w_time_results['houteigai_time']=0;
            }
            else{
                //労働時間
                if(strtotime($w_time_results['real_work_start']) >= strtotime("13:00") || strtotime($w_time_results['real_work_end']) <= strtotime("12:00")){
                    $w_time_results['roudou_time']=ceil((strtotime($w_time_results['real_work_end']) - strtotime($w_time_results['real_work_start']) )/ 36) / 100 ;
                }
                else{
                    $w_time_results['roudou_time']=ceil((strtotime($w_time_results['real_work_end']) - strtotime($w_time_results['real_work_start']) )/ 36) / 100 - 1 ;
                }
                //残業時間
                if($cur_worktime->work_type == 2){ //休暇
                    $w_time_results['zangyou_time'] = $dayoff_times + $w_time_results['roudou_time'] - $basic_worktimes;
                }elseif($cur_worktime->work_type == 3){ //午前半休
                    $w_time_results['zangyou_time'] = $morningoff_times + $w_time_results['roudou_time'] - $basic_worktimes;
                }elseif($cur_worktime->work_type == 4){ //午後半休
                    $w_time_results['zangyou_time'] = $afternoonoff_times + $w_time_results['roudou_time'] - $basic_worktimes;
                }else{
                    $w_time_results['zangyou_time'] = $w_time_results['roudou_time'] - $basic_worktimes;
                }
                if($w_time_results['zangyou_time'] < 0){
                    $w_time_results['zangyou_time'] = 0;
                }
                //法定内残業時間
                if($w_time_results['zangyou_time'] == 0){
                    $w_time_results['houteinai_time'] = 0;
                }elseif($w_time_results['zangyou_time'] <=1 && $w_time_results['zangyou_time'] >= 0){
                    $w_time_results['houteinai_time'] = $w_time_results['zangyou_time'];
                }else{
                    $w_time_results['houteinai_time'] = 1;
                }
                //法定外残業時間
                if($w_time_results['zangyou_time'] <= 0){
                    $w_time_results['houteigai_time'] = 0;
                }else{
                    $w_time_results['houteigai_time'] = $w_time_results['zangyou_time'] - $w_time_results['houteinai_time'];
                }
            }

            //出勤日数
            if($cur_worktime->work_type == 1 || $cur_worktime->work_type == 3 || $cur_worktime->work_type == 4 || $cur_worktime->work_type == 5){
                $workday_counts++;
            }
            //欠勤日数
            if($cur_worktime->work_type == 7){
                $v_dayoff_counts++;
            }
            //有休取得日数
            if($cur_worktime->work_type == 2){
                $use_dayoff_counts++;
            }
            //午前半休取得日数
            if($cur_worktime->work_type == 3){
                $use_am_dayoff_counts++;
            }            
            //午後半休取得日数
            if($cur_worktime->work_type == 4){
                $use_pm_dayoff_counts++;
            }
        }
    

        //必要総就業時間(H)
        $need_total_worktimes_hours = $workday_counts * $basic_worktimes;
        //有休取得時間(H)
        $use_dayoff_hours = $use_dayoff_counts * $dayoff_times + $use_am_dayoff_counts * $morningoff_times + $use_pm_dayoff_counts * $afternoonoff_times ;

        //名前取得
        $cur_member = User::select([
            'name',
        ])
        ->from('users')
        ->where('id','=',intval($cur_member_id))
        ->first();
        $member_name = $cur_member->name;

        $cur_worktimes_total = collect([
            //可能就業時間帯と基本就業時間帯(G:iの形式)
            "member_name" => $member_name,
            "basic_worktimes" => $basic_worktimes,
            "able_worktime_start" => $able_worktime_start,
            "able_worktime_end" => $able_worktime_end,
            "basic_worktime_start" => $basic_worktime_start,
            "basic_worktime_end" => $basic_worktime_end,
            "total_work_hours" => $total_work_hours,
            "total_overtime_hours" => $total_overtime_hours,
            "total_law_time_hours" => $total_law_time_hours,
            "total_law_time_outer_hours" => $total_law_time_outer_hours,
            "total_worktime_hours" => $total_worktime_hours,  //総就業時間(H)
            "workday_counts" => $workday_counts,
            "v_dayoff_counts" => $v_dayoff_counts,
            "use_dayoff_counts" => $use_dayoff_counts,
            "use_am_dayoff_counts" => $use_am_dayoff_counts,
            "use_pm_dayoff_counts" => $use_pm_dayoff_counts,
            "need_total_worktimes_hours" => $need_total_worktimes_hours,
            "use_dayoff_hours" => $use_dayoff_hours,
        ]);
        return $cur_worktimes_total;
    }

    /*
    //プロパティの宣言
    public $変数名 = 値;
 
    //メソッドの宣言
    public function メソッド名(){
        //メソッド内処理...
    }
    */


}