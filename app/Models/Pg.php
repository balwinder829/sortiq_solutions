<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pg extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'rent_estimate',
        'pg_type',
        'description',
        'food_type',
        'status'
    ];
}
