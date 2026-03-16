<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeQuestion extends Model
{

    protected $table = 'office_questions';

    protected $fillable = [

        'office_test_id',
        'question',
        'marks',
        'question_order'

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

    public function answers()
    {
        return $this->hasMany(
            OfficeStudentAnswer::class,
            'office_question_id'
        );
    }
}