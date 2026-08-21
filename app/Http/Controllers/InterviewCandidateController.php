<?php

namespace App\Http\Controllers;

use App\Models\InterviewCandidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\NotBlockedNumber;

class InterviewCandidateController extends Controller
{
    public function create()
    {
        return view('interview_candidate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_name' => 'required|string|max:150',

            'mobile' => ['required', 'string', new NotBlockedNumber],

            'email' => 'nullable|email|max:150',

            'current_location' => 'nullable|string|max:150',

            'current_company' => 'nullable|string|max:200',

            'position_applied' => 'required|string|max:200',

            'qualification' => 'nullable|string|max:200',

            'experience' => 'nullable|string|max:100',

            'technology_known' => 'nullable|string|max:500',

            'preferred_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    if (date('N', strtotime($value)) == 7) {
                        $fail('Sunday is not available for interview scheduling.');
                    }
                },
            ],

            'preferred_time' => 'nullable',

            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',

            'message' => 'nullable|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Resume Upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('resume')) {

            $file = $request->file('resume');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->move(
                public_path('uploads/interview-resumes'),
                $filename
            );

            $validated['resume'] = 'uploads/interview-resumes/' . $filename;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Status
        |--------------------------------------------------------------------------
        */

        $validated['status'] = 'pending';

        InterviewCandidate::create($validated);

        return back()->with(
            'success',
            'Interview request submitted successfully!'
        );
    }
}