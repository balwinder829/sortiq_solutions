<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeExpense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_date',
        'title',
        'amount',
        'description',
        'image',
    ];
}
