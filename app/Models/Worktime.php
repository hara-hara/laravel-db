<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worktime extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'member_id',
        'work_date',
        'work_start',
        'work_end',
        'work_type',
        'created_at',
        'updated_at',        
    ];

}
