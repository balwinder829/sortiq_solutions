<?php

// app/Models/AcceptedLetter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'emp_code',
        'email',
        'file_path',
    ];
}
