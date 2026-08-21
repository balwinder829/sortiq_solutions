<?php

namespace App\Http\Controllers;

use App\Models\VisitorRecord;
use Illuminate\Http\Request;
use App\Rules\NotBlockedNumber;

class VisitorRecordController extends Controller
{
    public function create()
    {
        return view('visitor_record');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:150',
            'mobile' => ['required', 'string', new NotBlockedNumber],
            'email' => 'nullable|email|max:150',
            'organization' => 'nullable|string|max:200',
            'purpose' => 'required|string|max:255',
            'person_to_meet' => 'nullable|string|max:150',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_time' => 'nullable',
            'message' => 'nullable|string',
        ]);

        VisitorRecord::create($validated);

        return back()->with(
            'success',
            'Request submitted successfully!'
        );
    }
}