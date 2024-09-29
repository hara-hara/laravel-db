<?php

namespace App\Imports;

use App\Models\Students;
use Maatwebsite\Excel\Concerns\ToModel;

class StudentImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Students([
            'name'                => $row[0],
            'grade'            => $row[1],
            'math'                   => $row[2],
            'japanese'              => $row[3],
            'english'               => $row[4],
        ]);
    }
}
