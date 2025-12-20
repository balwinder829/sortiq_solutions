<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'expense_date',
        'title',
        'amount',
        'description',
    ];
}
