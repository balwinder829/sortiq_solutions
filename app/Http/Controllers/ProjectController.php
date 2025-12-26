<?php

// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // You might want to add middleware here:
    // public function __construct() {
    //     $this->middleware('auth')->except(['index', 'show']);
    // }

    /**
     * Display a listing of the resource (all projects).
     */
    public function index()
    {

        $projects = Project::orderBy('created_at', 'desc')->paginate(100)->withQueryString();
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());

        // Assign the authenticated user's ID
        // $validated['user_id'] = auth()->id();

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        // Add authorization check here (e.g., Policy)
        // $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        // Add authorization check here
        // $this->authorize('update', $project);

        $validated = $request->validate($this->validationRules($project->id));

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Project $project)
    {
        // Add authorization check here
        // $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project moved to trash.');
    }

    /**
     * Reusable validation rules.
     */
    protected function validationRules($ignoreId = null)
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('projects')->ignore($ignoreId)],
            'tech_stack' => ['required', 'string', 'max:255'],
            'backend_lang' => ['nullable', 'string', 'max:50'],
            'frontend_framework' => ['nullable', 'string', 'max:50'],
            'versions' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'github_link' => ['nullable', 'url', 'max:255'],
        ];
    }
}