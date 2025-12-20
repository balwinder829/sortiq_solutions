<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'trainer_name',
        'gender',
        'phone',
        'email',
        'technology',
        'user_id',
        'department',
    ];

    public function courseData()
    {
        return $this->belongsTo(Course::class, 'technology', 'id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'batch_assign');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function activeUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
