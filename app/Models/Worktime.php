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
        'real_work_start',
        'real_work_end',
        'result_work_start',
        'result_work_end',
        'work_type',
        'user_id',
        'accept',
        'accAb_id',
        'reason',
        'created_at',
        'updated_at',        
    ];

    public function user(){
        return $this->belongsTo(User::class, 'member_id');
    }
}
