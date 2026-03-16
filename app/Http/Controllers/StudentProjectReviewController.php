<?php

namespace App\Http\Controllers;

use App\Models\StudentProjectReview;
use Illuminate\Http\Request;

class StudentProjectReviewController extends Controller
{   

    public function __construct()
    {
        $this->middleware('permission:student_project_reviews.view')->only('index');
        $this->middleware('permission:student_project_reviews.create')->only(['create','store']);
        $this->middleware('permission:student_project_reviews.edit')->only(['edit','update']);
        $this->middleware('permission:student_project_reviews.delete')->only('destroy');
    }

    public function index()
    {
        $reviews = StudentProjectReview::latest()->get();

        return view('student-project-reviews.index', compact('reviews'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'submission_id' => 'required',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        StudentProjectReview::create([
            'submission_id' => $request->submission_id,
            'rating' => $request->rating,
            'feedback' => $request->feedback,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now()
        ]);

        return back()->with('success','Review submitted successfully');
    }

}