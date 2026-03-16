<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeaPantryExpense extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'tea_pantry_expenses';

    protected $guarded = ['id'];
}
