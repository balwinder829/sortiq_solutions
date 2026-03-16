<?php

namespace App\Http\Controllers;

use App\Models\StudentProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentProjectController extends Controller
{   

    public function __construct()
    {
        $this->middleware('permission:student_projects.view')->only('index');
        $this->middleware('permission:student_projects.create')->only(['create','store']);
        $this->middleware('permission:student_projects.edit')->only(['edit','update']);
        $this->middleware('permission:student_projects.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = StudentProject::query();

        if ($request->type) {
            $query->where('project_type',$request->type);
        }

        $projects = $query->latest()->get();

        return view('student-projects.index', compact('projects'));
    }


    public function create()
    {
        return view('student-projects.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'project_type' => 'required|in:mini,major'
        ]);

        StudentProject::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'technology' => $request->technology,
            'difficulty' => $request->difficulty,
            'estimated_days' => $request->estimated_days,
            'project_type' => $request->project_type,
            'created_by' => auth()->id(),
            'status' => 1
        ]);

        return redirect()
            ->route('student-projects.index')
            ->with('success','Project created successfully');
    }


    public function edit($id)
    {
        $project = StudentProject::findOrFail($id);

        return view('student-projects.edit', compact('project'));
    }


    public function update(Request $request,$id)
    {
        $project = StudentProject::findOrFail($id);

        $project->update($request->only([
            'title',
            'description',
            'technology',
            'difficulty',
            'estimated_days',
            'project_type'
        ]));

        return redirect()
            ->route('student-projects.index')
            ->with('success','Project updated successfully');
    }


    public function destroy($id)
    {
        StudentProject::findOrFail($id)->delete();

        return back()->with('success','Deleted successfully');
    }

}