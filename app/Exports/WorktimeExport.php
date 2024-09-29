<?php

namespace App\Exports;

use App\Models\Students;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings; //ヘッダーをつける
//se Maatwebsite\Excel\Concerns\WithHeadingRow; //ヘッダーをつける
//use Maatwebsite\Excel\Concerns\WithTitle;
//use Maatwebsite\Excel\Concerns\ToModel;

class StudentExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Students::all()->makeHidden(['id', 'created_at', 'updated_at']);
        //return users::all();
    }

    public function headings() : array //ヘッダーをつける
    {
        return [
            '名前',
            '学年',
            '数学',
            '国語',
            '英語'
        ];
    }
}
