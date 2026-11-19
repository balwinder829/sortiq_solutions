<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeminarRegistration;
use App\Models\College;
use App\Models\Course;
use App\Rules\NotBlockedNumber;
use App\Models\BlockedNumber;

class SeminarRegistrationController extends Controller
{
    public function create()
    {
        // $paymentUpi = $paymentUpiService->getEffectiveUpi(
        //     'student_registration'
        // );

        // $paymentUpi = $paymentUpiService->getEffectiveUpi(
        //     'student_registration'
        // );

        return view('seminar_registration');
    }

    public function store(
        Request $request
    ) {
        $validated = $request->validate([

            'student_name' => 'required',

            'contact' => ['required', 'string', new NotBlockedNumber],

            'email' => 'nullable|email',

            'gender' => 'required',

            'father_name' => 'required',

            'college_name_input' => 'nullable',

            'course_name_input' => 'nullable',

            'semester' => 'nullable',

            // 'study_mode' => 'required',

            // 'start_date' => 'required|date',
        ]);

        
        /*
        |--------------------------------------------------------------------------
        | Get the UPI assigned to this form
        |--------------------------------------------------------------------------
        |
        | Specific form assignment has priority.
        | Otherwise global default is returned.
        |
        */

    


        /*
        |--------------------------------------------------------------------------
        | Payment data
        |--------------------------------------------------------------------------
        */

        SeminarRegistration::create(
            $validated
        );


        return back()->with(
            'success',
            'Details submitted successfully!'
        );
    }
}