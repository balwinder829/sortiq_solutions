<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkshopExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'workshop_expenses';

    protected $fillable = [
        'workshop_id',
        'title',
        'expense',
        'other_expense',
        'description',
    ];

    protected $casts = [
        'expense' => 'decimal:2',
        'other_expense' => 'decimal:2',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}