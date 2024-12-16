<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ExportService as ExportService;
use Illuminate\Support\Facades\Storage;

class ExportController extends Controller
{
    public function pdf(Request $request)
    {
        $file_name = 'filename_' . date('YmdHis');
        $export_service = new ExportService();

        //$cur_user_id = $request->cur_user_id;
        //$cur_year = $request->cur_year;
        //$cur_month = $request->cur_month;
        $cur_user_id = 1;
        $cur_year = '2014';
        $cur_month = '10';
        
        $export_service->makePdf($request, $file_name,$cur_user_id,$cur_year,$cur_month);
 
        $file_path = Storage::path('pdf/export/' . $file_name . '.pdf');
        $headers = ['Content-Type' => 'application/pdf'];
        return response()->download($file_path, $file_name . '.pdf', $headers);
    }
}
