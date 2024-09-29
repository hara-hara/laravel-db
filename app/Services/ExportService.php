<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class ExportService
{
    public function makePdf($req, $file_name)
    {
        // もとになるExcelを読み込み
        $excel_file = storage_path('app/excel/template/hinaD.xlsx');
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($excel_file);

        // 編集するシート名を指定
        $worksheet = $spreadsheet->getSheetByName('hoge');


        dd($req -> cur_year);




        // セルに指定した値挿入
        $worksheet->setCellValue('D4', 'abc1112');

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
