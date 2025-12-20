<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTest extends Model
{
    use HasFactory;

    protected $casts = [
        'is_finalized' => 'boolean',
    ];

    // ✅ Allow mass assignment
    protected $fillable = [
        'test_id',
        'student_name',
        'student_email',
        'college_name',
        'student_mobile',
        'session_key',
        'exam_started_at',
        'exam_submitted_at',
        'exam_locked',
        'gender',
        'ip_address',
        'score'
    ];

    public function scopeOnline($q)
    {
        return $q->where('source', 'online');
    }

    public function scopeOffline($q)
    {
        return $q->where('source', 'offline');
    }

    public function scopeFinalized($q)
    {
        return $q->where('is_finalized', 1);
    }

    public function scopeIntern($q)
    {
        return $q->where('selection_type', 'intern');
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }
}
