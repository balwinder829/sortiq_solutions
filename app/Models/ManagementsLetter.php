<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ManagementsLetter extends Model
{   
    use SoftDeletes;
    protected $fillable = [
        'letter_type',
        'title',
        'issue_date',
        'content',
    ];

}
