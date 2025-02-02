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

        $cur_member_id = $request->cur_member_id;
        $cur_year = $request->cur_year;
        $cur_month = $request->cur_month;
        
        $export_service->makePdf($request, $file_name,$cur_member_id,$cur_year,$cur_month);
 
        $file_path = Storage::path('pdf/export/' . $file_name . '.pdf');
        $headers = ['Content-Type' => 'application/pdf'];
        return response()->download($file_path, $file_name . '.pdf', $headers);
    }
}
