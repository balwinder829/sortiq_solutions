<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectCategoryController extends Controller
{
    protected string $permissionPrefix = 'project-categories';

    protected array $permissionMap = [
        'index'   => 'view',
        'create'  => 'create',
        'store'   => 'create',
        'edit'    => 'edit',
        'update'  => 'edit',
        'destroy' => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // foreach ($this->permissionMap as $method => $action) {
        //     $this->middleware(
        //         "permission:{$this->permissionPrefix}.{$action}"
        //     )->only($method);
        // }
    }

    /**
     * Display all categories.
     */
    public function index(Request $request)
    {
        $query = ProjectCategory::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query
            ->orderBy('name')
            ->get();

        return view('project-categories.index', compact('categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('project-categories.create');
    }

    /**
     * Store category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:project_categories,name',
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        ProjectCategory::create($validated);

        return redirect()
            ->route('project-categories.index')
            ->with('success', 'Project category created successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(ProjectCategory $projectCategory)
    {
        return view('project-categories.edit', compact('projectCategory'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('project_categories', 'name')
                    ->ignore($projectCategory->id),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $projectCategory->update($validated);

        return redirect()
            ->route('project-categories.index')
            ->with('success', 'Project category updated successfully!');
    }

    /**
     * Delete category.
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        // Prevent deleting category if projects are using it.
        if ($projectCategory->projects()->exists()) {
            return redirect()
                ->route('project-categories.index')
                ->with('error', 'This category cannot be deleted because it is assigned to one or more projects.');
        }

        $projectCategory->delete();

        return redirect()
            ->route('project-categories.index')
            ->with('success', 'Project category deleted successfully!');
    }
}