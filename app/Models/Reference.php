<?php

// app/Models/Reference.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{   
    use SoftDeletes;

    protected $fillable = ['name'];
}
