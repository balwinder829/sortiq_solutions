<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlacementCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'contact_person',
        'email',
        'phone',
        'website',
        'remarks',
        'status',
    ];

    public function getPhoneListAttribute()
    {
        return $this->phone ? explode(',', $this->phone) : [];
    }

    public function getEmailListAttribute()
    {
        return $this->email ? explode(',', $this->email) : [];
    }

}
