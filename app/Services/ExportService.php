<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use App\Models\Worktime;
use App\Models\Master_worktime_type;
use App\Models\User_group_type;


class ExportService
{
    public $worktimes;

    public function makePdf($req, $file_name,$cur_member_id,$cur_year,$cur_month)
    {
        // もとになるExcelを読み込み
        $excel_file = storage_path('app/excel/template/hinaE.xlsx');
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($excel_file);

        // 編集するシート名を指定
        $worksheet = $spreadsheet->getSheetByName('hoge');

        // ユーザのmasterテーブルidを取得
        $user_group_type = User_group_type::select([
            'user_id',
            'group_id',
            'master_id',
        ])
        ->from('user_group_types as b')
        ->where('user_id','=',$cur_member_id)
        ->first();

        //基本勤務データ取得
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

        //基本就業時間 =  基本就業時間帯(終了) - 基本就業時間帯(開始) - 休憩時間
        $basic_work_times = self::basic_work_times($master_worktime_type->dayoff_times,$master_worktime_type->basic_worktime_start,$master_worktime_type->basic_worktime_end);

        //休暇の工数
        $dayoff_times = $master_worktime_type->dayoff_times;

        //午前半休の工数
        $morningoff_times = $master_worktime_type->morningoff_times;

        //午後半休の工数
        $aftenoonoff_times = $master_worktime_type->aftenoonoff_times;

        // メイン勤怠データ取得
        $worktimes = self::worktimes_fromDB($cur_member_id,$cur_year,$cur_month);
        $i=0;
        foreach($worktimes as $worktime){
            $i++;
            //セルに指定した値挿入
            //日付
            $day = strtotime($worktime->work_date);
            $worksheet->setCellValue('A'.$i+12, date('j',$day));
 
            //曜日
            //配列を使用し、要素順に(日:0〜土:6)を設定する
            $week = [
                '日', //0
                '月', //1
                '火', //2
                '水', //3
                '木', //4
                '金', //5
                '土', //6
            ];
            $worksheet->setCellValue('C'.$i+12, date('w',$day));

            //区分
            $worksheet->setCellValue('E'.$i+12, $worktime->work_type);
            //打刻出社時間
            $worksheet->setCellValue('I'.$i+12, $worktime->real_work_start);
            //打刻退社時間
            $worksheet->setCellValue('L'.$i+12, $worktime->real_work_end);
            //就業開始時間
            $worksheet->setCellValue('O'.$i+12, $worktime->result_work_start);
            //就業終了時間
            $worksheet->setCellValue('R'.$i+12, $worktime->result_work_end);
   
            //労働時間
            $roudou_time = self::roudou_time($worktime->result_work_start,$worktime->result_work_end,$master_worktime_type->basic_worktime_end);
            $worksheet->setCellValue('U'.$i+12,$roudou_time);

            //残業時間
            $zangyou_time = self::zangyou_time($worktime->work_type,$roudou_time,$basic_work_times,$morningoff_times,$aftenoonoff_times);
            $worksheet->setCellValue('AD'.$i+12, $zangyou_time);

            //法定内残業時間
            $houteinai_time = self::houteinai_time($zangyou_time);
            $worksheet->setCellValue('X'.$i+12, $houteinai_time);

            //法定外残業時間
            $houteigai_time = self::houteigai_time($zangyou_time,$houteinai_time);
            $worksheet->setCellValue('AA'.$i+12, $houteigai_time);


        }








        // 以下のコードで、「シートを1ページに印刷」の設定を行います。
        $worksheet->getPageSetup()
            ->setPrintArea('A1:AD43')	//印刷範囲をA1からE10までに設定
            ->setFitToWidth(1)  // 1ページの幅に合わせる
            ->setFitToHeight(1); // 1ページの高さに合わせる

        $worksheet->getPageMargins()	//以下上、右、左、下の余白をそれぞれ設定
            ->setTop(0.75)
            ->setRight(0.7)
            ->setLeft(0.7)
            ->setBottom(0.75);
   

        // Excel出力
        $writer = new XlsxWriter($spreadsheet);
        $export_excel_path = storage_path('app/excel/export/' . $file_name . '.xlsx');
        $writer->save($export_excel_path);

        // Pdf出力
        if (file_exists($export_excel_path)) {
            $export_pdf_path = storage_path('app/pdf/export');
            $cmd = 'export HOME=/tmp; libreoffice --headless --convert-to pdf --outdir ' . $export_pdf_path . ' ' . $export_excel_path;
            exec($cmd);
        }
    }

    public function worktimes_fromDB($cur_member_id,$cur_year,$cur_month)
    {
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
        ->where('b.member_id','=',$cur_member_id)
        ->whereYear('b.work_date', $cur_year)
        ->orwhereMonth('b.work_date', $cur_month)
        ->orderBy('b.work_date', 'ASC')
        ->paginate(31);// bladeで$worktimes->links('pagination::bootstrap-5')があるからこれ無いとエラー。
        return($worktimes);


    }

    //基本就業時間
    public function basic_work_times($dayoff_times,$basic_worktime_start,$basic_worktime_end)
    {
        //基本就業時間 =  基本就業時間帯(終了) - 基本就業時間帯(開始) - 休憩時間 
        return (strtotime($basic_worktime_end) - strtotime($basic_worktime_start)) / 3600 - $dayoff_times; 
    }

    //労働時間
    public function roudou_time($result_work_start,$result_work_end,$basic_worktime_end)
    {
        if($result_work_start=="" || $result_work_end==""){
            $roudou_time=0;
        }elseif(strtotime($result_work_start) >= strtotime("13:00") || strtotime($result_work_end) <= strtotime("12:00")){
            $roudou_time=(strtotime($result_work_end) - strtotime($result_work_start) )/ 3600 -1;
        }else{
            $roudou_time=(strtotime($result_work_end) - strtotime($result_work_start) )/ 3600 -1;
        }
        return $roudou_time;
    }
    

    //残業時間
    public function zangyou_time($work_type,$roudou_time,$basic_work_times,$morningoff_times,$aftenoonoff_times)
    {
        if($work_type == 2){ //休暇
            $zangyou_time = $work_type + $roudou_time - $basic_work_times;
        }elseif($work_type == 3){ //午前半休
            $zangyou_time = $morningoff_times + $roudou_time - $basic_work_times;
        }elseif($work_type == 4){ //午後半休
            $zangyou_time = $aftenoonoff_times + $roudou_time - $basic_work_times;
        }else{
            $zangyou_time = 0;
        }
        return $zangyou_time;
    }

    //法定内残業時間
    public function houteinai_time($zangyou_time)
    {
        if($zangyou_time == 0){
            $houteinai_time = 0;
        }elseif($zangyou_time <=1 && $zangyou_time >= 0){
            $houteinai_time = $zangyou_time;
        }else{
            $houteinai_time = 1;
        }
        return $houteinai_time;
    }

    //法定外残業時間
    public function houteigai_time($zangyou_time,$houteinai_time)
    {

            if($zangyou_time <= 0){
                $houteigai_time = 0;
            }else{
                $houteigai_time = $zangyou_time - $houteinai_time;
            }
            return $houteigai_time;
    }



}
