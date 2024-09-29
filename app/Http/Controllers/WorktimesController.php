<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Exports\StudentExport; //追加
use Maatwebsite\Excel\Facades\Excel;

class WorktimesController extends Controller
{
    public function export(){ //追加
        return Excel::download(new WorktimeExport, 'output_worktime_data.xlsx');
    }
}


