<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\RichText\RichText as RichText;
use PhpOffice\PhpSpreadsheet\Style\Color as Color;
use App\Models\Worktime;
use App\Models\Worktype;
use App\Models\Master_worktime_type;
use App\Models\User_group_type;
use App\common\commonDB;

class ExportService
{
    public $worktimes;

    public function makePdf($req, $file_name,$cur_member_id,$cur_year,$cur_month)
    {
        // もとになるExcelを読み込み
        $excel_file = storage_path('app/excel/template/hinaF.xlsx');
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($excel_file);

        // 編集するシート名を指定
        $worksheet = $spreadsheet->getSheetByName('hoge');

        // 勤務表データ取得
        $common_kinmuhyou = commonDB::common_kinmuhyou($cur_member_id,$cur_year,$cur_month);
        $cur_worktimes = $common_kinmuhyou[0];
        $cur_worktimes_total = $common_kinmuhyou[1];


        $i = 0;
        foreach($cur_worktimes as $worktime){
            $i++;
            //セルに指定した値挿入
            //日付
            $day = $worktime->day;
 
            //曜日
            //配列を使用し、要素順に(日:0〜土:6)を設定する
            $week = [
                '日', //6
                '月', //0
                '火', //1
                '水', //2
                '木', //3
                '金', //4
                '土', //5
            ];
            $cur_date = $cur_year.'-'.$cur_month.'-'.$day;

            //日なら赤、土なら青、その他なら黒にする
            $RichText_day = new RichText();
            $RichText_week = new RichText();
            $color = new Color();
            if( date('w',strtotime($cur_date))==0 ){                
                $color-> setRGB('FF0000'); //フォント色のインスタンスを作っておく
            }elseif( date('w',strtotime($cur_date))==6 ){
                $color-> setRGB('0000FF'); //フォント色のインスタンスを作っておく
            }else{
                $color-> setRGB('000000'); //フォント色のインスタンスを作っておく
            }
            //$RichText_day -> createTextRun(date('j',$day)) -> getFont() -> setColor($color);//文字色;
            $RichText_day -> createTextRun($day) -> getFont() -> setColor($color);//文字色;
            $RichText_week -> createTextRun($week[date('w',strtotime($cur_date))]) -> getFont() -> setColor($color);//文字色;
            $worksheet -> getCell('A'.$i+12) -> setValue($RichText_day); 
            $worksheet -> getCell('C'.$i+12) -> setValue($RichText_week); 

            //区分
            $worksheet->setCellValue('E'.$i+12, $worktime->work_type_ch);
 
            //打刻出社時間
            $worksheet->setCellValue('O'.$i+12, $worktime->real_work_start);

            //打刻退社時間
            $worksheet->setCellValue('R'.$i+12, $worktime->real_work_end);

            //就業開始時間
            $worksheet->setCellValue('I'.$i+12, $worktime->result_work_start);

            //就業終了時間
            $worksheet->setCellValue('L'.$i+12, $worktime->result_work_end);
               
            //労働時間
            //$roudou_time = self::roudou_time($worktime->result_work_start,$worktime->result_work_end,$master_worktime_type->basic_worktime_end);
            $worksheet->setCellValue('U'.$i+12, $worktime->roudou_time);

            //残業時間
            //$zangyou_time = self::zangyou_time($worktime->work_type,$roudou_time,$basic_work_times,$morningoff_times,$aftenoonoff_times);
            $worksheet->setCellValue('AD'.$i+12, $worktime->zangyou_time);

            //法定内残業時間
            //$houteinai_time = self::houteinai_time($zangyou_time);
            $worksheet->setCellValue('X'.$i+12, $worktime->houteinai_time);

            //法定外残業時間
            //$houteigai_time = self::houteigai_time($zangyou_time,$houteinai_time);
            $worksheet->setCellValue('AA'.$i+12, $worktime->houteigai_time);


        }

        //月の総計情報をセルに埋め込む
        
        //年月
        $worksheet->setCellValue('A1', $cur_year."　年　　".(int)$cur_month."　月");

        //従業員ID
        $worksheet->setCellValue('D3', $cur_member_id);

        //氏名
        $worksheet->setCellValue('K3', $cur_worktimes_total['member_name']);
        
        //出勤日数
        $worksheet->setCellValue('A6', $cur_worktimes_total['workday_counts']);
        //欠勤日数
        $worksheet->setCellValue('G6', $cur_worktimes_total['v_dayoff_counts']);
        //有休取得日数
        $worksheet->setCellValue('M6', $cur_worktimes_total['use_dayoff_counts']);
        //有休取得時間(H)
        $worksheet->setCellValue('S6', $cur_worktimes_total['use_dayoff_hours']);
        //必要総就業時間(H)			
        $worksheet->setCellValue('Y6', $cur_worktimes_total['need_total_worktimes_hours']);
                    
        //総労働時間(H)
        $worksheet->setCellValue('A8', $cur_worktimes_total['total_worktime_hours']);

        //総残業(H)
        $worksheet->setCellValue('G8', $cur_worktimes_total['total_overtime_hours']);

        //総法定内残業(H)
        $worksheet->setCellValue('M8', $cur_worktimes_total['total_law_time_hours']);

        //総法定外残業(H)
        $worksheet->setCellValue('S8', $cur_worktimes_total['total_law_time_outer_hours']);

        //総就業時間(H)
        $worksheet->setCellValue('Y8', $cur_worktimes_total['basic_worktimes']);

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
}
