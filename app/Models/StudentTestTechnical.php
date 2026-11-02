<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTestTechnical extends Model
{
    use SoftDeletes;

    protected $table = 'student_test_technical';

    protected $fillable = [
        'source_student_test_id',
        'previous_round_type',
        'previous_round_record_id',
        'student_name',
        'student_email',
        'student_mobile',
        'college_id',
        'college_name',
        'score',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'source_student_test_id' => 'integer',
        'previous_round_record_id' => 'integer',
        'college_id' => 'integer',
        'score' => 'integer',
    ];

    public function sourceStudentTest(): BelongsTo
    {
        return $this->belongsTo(
            StudentTest::class,
            'source_student_test_id'
        );
    }
}