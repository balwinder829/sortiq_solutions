<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    protected $table = 'duration_list';

    protected $fillable = [
        'name',
        'duration'
    ];
}
