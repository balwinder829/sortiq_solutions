<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LetterTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'letter_type',
        'title',
        'department',
        'content',
        'status',
    ];
}