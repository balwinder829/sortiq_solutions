<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfflineTestStudent extends Model
{
    protected $fillable = [
        'test_id',
        'student_name',
        'student_email',
        'student_mobile',
        'score',
        'rank',
        'is_finalized',
        'source',
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }
}
