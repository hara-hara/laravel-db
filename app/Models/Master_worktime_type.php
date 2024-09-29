<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Master_worktime_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'able_worktime_start',
        'able_worktime_end',
        'basic_worktime_start',
        'basic_worktime_end',
        'lunch_break_times',
        'dayoff_times',
        'morningoff_times',
        'aftenoonoff_times',
        'created_at',
        'updated_at',
    ];
}
