<?php

namespace App\Models; 

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class SalesStaff extends Authenticatable
{
    use Notifiable, SoftDeletes;
    protected $table = 'sales_staff';

    protected $fillable = [
        'name','username','email','phone','password','plain_pswd','gender','status'
    ];

    protected $hidden = ['password'];

    public function setPasswordAttribute($value)
    {   
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'actor_id')
            ->where('actor_type', 'sales_staff');
    }

    public function enquiriesAssigned()
    {
        return $this->hasMany(Enquiry::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(EnquiryActivity::class, 'user_id');
    }
}
