<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pg extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact',
        'email',
        'rent_estimate',
        'pg_type',
        'description',
        'food_type',
        'status'
    ];

    public function getPhoneListAttribute()
    {
        return $this->contact ? explode(',', $this->contact) : [];
    }

    public function getEmailListAttribute()
    {
        return $this->email ? explode(',', $this->email) : [];
    }
}
