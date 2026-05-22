<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Authenticatable
{
    use HasFactory;
    use Notifiable, SoftDeletes;
    
    protected $table = 'trainers';

    protected $fillable = [
        'username',
        'password',
        'trainer_pswd',
        'name',
        'email',
        'phone',
        'gender',
        'technology',
        'status',
    ];

    protected $hidden = ['password'];

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'batch_assign');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class)->withTrashed();
    // }

    // public function activeUser()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function evaluations()
    {
        return $this->hasMany(StudentEvaluation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'actor_id')
            ->where('actor_type', 'trainer');
    }

    public function letters()
    {
        return $this->hasMany(TrainerLetter::class, 'trainer_id');
    }


}
