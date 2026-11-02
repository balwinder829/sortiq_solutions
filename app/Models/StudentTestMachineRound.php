<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentTestMachineRound extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'student_test_machine_round';

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

    public function sourceStudentTest()
    {
        return $this->belongsTo(StudentTest::class, 'source_student_test_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }
}
