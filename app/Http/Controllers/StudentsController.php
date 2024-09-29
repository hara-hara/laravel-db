<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Imports\StudentImport;
use App\Exports\StudentExport; //追加
use Maatwebsite\Excel\Facades\Excel;

class StudentsController extends Controller
{
    public function import(Request $request){
        $excel_file = $request->file('excel_file');
        $excel_file->store('excels');
        Excel::import(new StudentImport, $excel_file);
        return view('index');
    }

    public function export(){ //追加
        return Excel::download(new StudentExport, 'output_student_data.xlsx');
    }
}


