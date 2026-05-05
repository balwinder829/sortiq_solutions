<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Course;

class Batch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'batches';

    // protected $appends = ['trainer_name'];

    protected $casts = [
        'session_name' => 'integer',
    ];

    protected $fillable = [
        'batch_name',
        'session_name',
        'start_time',
        'end_time',
        'department',
        'batch_assign',
        'class_assign',
        'duration',
        'batch_mode',
        'status',
    ];



    public function courseData()
    {
        return $this->belongsTo(Course::class, 'class_assign'); 
    }

   public function trainerData()
    {
        return $this->belongsTo(Trainer::class, 'batch_assign', 'id');
    }

    // public function getTrainerNameAttribute()
    // {
    //     return $this->trainerData?->name ?? 'Not Assigned';
    // }

    public function sessionData()
    {
        return $this->belongsTo(StudentSession::class, 'session_name', 'id');
    }

    public function durationData()
    {
        return $this->belongsTo(Duration::class, 'duration', 'duration');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'batch_assign');
    }

    // 🔵 Accessor for multiple technologies
    public function getCoursesAttribute()
    {
        $ids = $this->class_assign ? explode(',', $this->class_assign) : [];

        if (empty($ids)) {
            return collect();
        }

        return Course::whereIn('id', $ids)->get();
    }

    public function mentor()
    {
        return $this->belongsTo(Trainer::class, 'batch_assign');
    }



}
