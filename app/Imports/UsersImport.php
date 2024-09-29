<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'id'=>$row['id'],
            'name'=>$row['name'],
            'email'=>$row['email'],
            'email_verified_at'=>$row['email_verified_at'],
            'password'=>$row['password'],
            'remember_token'=>$row['remember_token'],
            'created_at'=>$row['created_at'],
            'updated_at'=>$row['updated_at'],
        ]);
    }
    public function headingRow() : int
    {
      return 1;
    }
  
}
