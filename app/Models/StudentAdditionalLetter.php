<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAdditionalLetter extends Model
{
    protected $fillable = [
        'letter_for',
        'student_id',
        'intern_student_id',

        'internship_type',
        'subject',
        'issue_date',
        'roll_number',
        'semester',

        // Normal Offer Letter Dynamic Fields
        'offer_training_start_date',
        'offer_training_duration_months',
        'offer_internship_type',
        'offer_ppo_evaluation_months',

        // Strict Offer Letter Fields
        'offer_probation_duration_months',
        'offer_working_hours',
        'offer_service_agreement_duration',
        'offer_annual_salary',

        'letter_content'
    ];

    protected static function booted()
    {
        static::addGlobalScope('student_default', function ($query) {
            $query->where('letter_for', 'student');
        });
    }

    // Explicit Intern records
    public function scopeForInterns($query)
    {
        return $query->withoutGlobalScope('student_default')
                     ->where('letter_for', 'intern');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function intern()
    {
        return $this->belongsTo(
            InternStudent::class,
            'intern_student_id'
        );
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }
}
