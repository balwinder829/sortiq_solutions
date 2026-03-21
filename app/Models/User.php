<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash; // <-- Import Hash
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    use HasRoles;
    protected $table = 'users';


    protected $fillable = [
        'name',
        'username',
        'email',
        'mobile',
        'password',
        'role',
        'status',
        'last_login',
        'phone',
        'plain_pswd',
    ];


    protected $hidden = [
        'password',
    ];

    // Automatically hash password when setting it
    public function setPasswordAttribute($password)
    {   
        if (!empty($password)) {
            $this->attributes['password'] = Hash::make($password);
        }
    }

    // public function roles()
    // {
    //     return $this->belongsTo(Role::class, 'role','id');
    // }

    public function legacyRole()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role', 'id');
    }


    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'user_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function isAdmin()
    {
        return $this->role === '1'; // or whatever column you use
    }
    public function enquiriesAssigned()
    {
        return $this->hasMany(Enquiry::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(EnquiryActivity::class, 'user_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'collected_by');
    }

}
