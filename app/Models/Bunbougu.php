<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bunbougu extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'kakaku',
        'bunrui',
        'shosai',
        'created_at',
        'updated_at',
    ];
}
