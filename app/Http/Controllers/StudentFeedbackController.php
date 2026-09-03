<?php

namespace App\Http\Controllers;

use App\Models\StudentFeedback;
use Illuminate\Http\Request;
use App\Rules\NotBlockedNumber;
class StudentFeedbackController extends Controller
{
    /**
     * Show the public feedback form.
     */
    public function create()
    {
        return view('student_feedback.create');
    }

    /**
     * Store feedback submitted from the frontend.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',

            'mobile' => ['required', 'string', new NotBlockedNumber],

            'email' => [
                'nullable',
                'email',
                'max:100',
            ],

            'course' => [
                'nullable',
                'string',
                'max:150',
            ],

            'batch' => [
                'nullable',
                'string',
                'max:150',
            ],

            'message' => [
                'required',
                'string',
            ],
        ], [
            'name.required' => 'Please enter your name.',

            'mobile.required' => 'Please enter your mobile number.',

            'email.email' => 'Please enter a valid email address.',

            'message.required' => 'Please enter your feedback.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Store Feedback
        |--------------------------------------------------------------------------
        */

        StudentFeedback::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'course' => $validated['course'] ?? null,
            'batch' => $validated['batch'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Redirect Back With Success Message
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('student-feedback.create')
            ->with(
                'success',
                'Thank you! Your feedback has been submitted successfully.'
            );
    }
}