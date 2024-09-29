<?php

namespace App;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'name',
        'grade',
        'math',
        'japanese',
        'english',
    ];

}
