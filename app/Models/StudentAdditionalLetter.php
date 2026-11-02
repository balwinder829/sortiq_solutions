<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAdditionalLetter extends Model
{
    protected $fillable = [
        // 'student_name',
        'internship_type',
        'subject',
        'student_id',
        'issue_date',
        'roll_number',
        'semester',
        // 'email',
        'offer_training_start_date',
        'offer_training_duration_months',
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
