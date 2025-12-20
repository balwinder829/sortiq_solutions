<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedNumberLog extends Model
{
    protected $fillable = [
        'blocked_number_id',
        'table_name',
        'count',
    ];
}
