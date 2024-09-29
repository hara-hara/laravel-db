<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_group_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'master_id',
        'created_at',
        'updated_at',        
    ];

}
