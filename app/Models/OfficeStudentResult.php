<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeStudentResult extends Model
{

    protected $table = 'office_student_results';

    protected $fillable = [

        'office_test_id',
        'student_id',
        'score',
        'remarks',
        'is_finalized',
        'created_by'

    ];

    protected $casts = [
        'is_finalized' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function test()
    {
        return $this->belongsTo(OfficeTest::class,'office_test_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}