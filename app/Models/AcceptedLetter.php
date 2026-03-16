<?php

// app/Models/AcceptedLetter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptedLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'employee_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
