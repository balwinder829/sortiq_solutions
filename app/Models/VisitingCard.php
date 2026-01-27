<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitingCard extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'company_name',
        'phone_primary',
        'phone_secondary',
        'email',
        'website',
        'address',
        'card_front',
        'card_back',
    ];
}
