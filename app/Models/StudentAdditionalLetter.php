<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAdditionalLetter extends Model
{
    protected $fillable = [
         'internship_type',
        'subject',
        'student_id',
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    
}
