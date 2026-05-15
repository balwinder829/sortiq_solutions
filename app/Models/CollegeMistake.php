<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollegeMistake extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'college_mistakes';

    protected $fillable = [
        'college_name',
        'contact_person',
        'location',
        'website',
        'description',
    ];
}