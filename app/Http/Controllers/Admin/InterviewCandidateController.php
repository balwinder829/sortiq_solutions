<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InterviewCandidate;
use Illuminate\Http\Request;
use App\Rules\NotBlockedNumber;

class InterviewCandidateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:interview_candidate.view')->only(['index']);
    }

    /**
     * Interview Candidates List
     */
    public function index(Request $request)
    {
        $query = InterviewCandidate::query();

        // Candidate Name
        if ($request->filled('candidate_name')) {
            $query->where(
                'candidate_name',
                'like',
                '%' . $request->candidate_name . '%'
            );
        }

        // Mobile
        if ($request->filled('mobile')) {
            $query->where(
                'mobile',
                'like',
                '%' . $request->mobile . '%'
            );
        }

        // Position
        if ($request->filled('position_applied')) {
            $query->where(
                'position_applied',
                'like',
                '%' . $request->position_applied . '%'
            );
        }

        // Status
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        // Preferred Date
        if ($request->filled('preferred_date')) {
            $query->whereDate(
                'preferred_date',
                $request->preferred_date
            );
        }

        $candidates = $query
            ->latest('id')
            ->get();

        return view(
            'interview_candidates.index',
            compact('candidates')
        );
    }

    /**
     * Edit Candidate
     */
    public function edit($id)
    {
        $candidate = InterviewCandidate::findOrFail($id);

        return view(
            'interview_candidates.edit',
            compact('candidate')
        );
    }

    /**
     * Update Candidate
     */
    public function update(Request $request, $id)
    {
        $candidate = InterviewCandidate::findOrFail($id);

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
                        $fail(
                            'Sunday is not available for interview scheduling.'
                        );
                    }
                },
            ],

            'preferred_time' => 'nullable',

            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',

            'message' => 'nullable|string',

            'status' => 'required|in:pending,confirmed,completed,cancelled',

            'admin_notes' => 'nullable|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Resume Upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('resume')) {

            $file = $request->file('resume');

            $filename =
                time() . '_' . $file->getClientOriginalName();

            $file->move(
                public_path('uploads/interview-resumes'),
                $filename
            );

            $validated['resume'] =
                'uploads/interview-resumes/' . $filename;
        }

        $candidate->update($validated);

        return redirect()
            ->route('admin.interview_candidates.index')
            ->with(
                'success',
                'Interview candidate updated successfully!'
            );
    }

    public function show($id)
    {
        $candidate = InterviewCandidate::findOrFail($id);

        return view('interview_candidates.show', compact('candidate'));
    }

    /**
     * Delete Candidate
     */
    public function destroy($id)
    {
        $candidate = InterviewCandidate::findOrFail($id);

        $candidate->delete();

        return redirect()
            ->route('admin.interview_candidates.index')
            ->with(
                'success',
                'Interview candidate deleted successfully!'
            );
    }
}