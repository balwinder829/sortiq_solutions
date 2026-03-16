<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeAccessoryExpense extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'office_accessory_expenses';

    protected $guarded = ['id'];
}
