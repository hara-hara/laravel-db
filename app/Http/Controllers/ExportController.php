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
        dd($request);

        $export_service->makePdf($request, $file_name);
 
        $file_path = Storage::path('pdf/export/' . $file_name . '.pdf');
        $headers = ['Content-Type' => 'application/pdf'];
        return response()->download($file_path, $file_name . '.pdf', $headers);
    }
}
