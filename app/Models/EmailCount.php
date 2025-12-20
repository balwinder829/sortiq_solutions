<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailCount extends Model
{
    protected $fillable = ['email', 'count'];
}
