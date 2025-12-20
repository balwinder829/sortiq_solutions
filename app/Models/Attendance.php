<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id', 
        'login_time', 
        'logout_time',
        'ip_address',
        'user_agent',
        'browser',
        'browser_version',
        'os',
        'device',
        'device_type',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'login_time'  => 'datetime',
        'logout_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    protected function loginTime(): Attribute
    {
        return Attribute::get(fn ($value) =>
            $value
                ? Carbon::parse($value)->setTimezone('Asia/Kolkata')
                : null
        );
    }

    protected function logoutTime(): Attribute
    {
        return Attribute::get(fn ($value) =>
            $value
                ? Carbon::parse($value)->setTimezone('Asia/Kolkata')
                : null
        );
    }
}
