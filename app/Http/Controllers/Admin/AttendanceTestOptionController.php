<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceTestCategory;
use App\Models\ExternalAttendanceTest;
use App\Models\ExternalAttendanceTestOption;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceTestOptionController extends Controller
{
    protected string $permissionPrefix = 'attendance_test_options';

    protected array $permissionMap = [
        'index' => 'view',
        'create' => 'create',
        'store' => 'create',
        'edit' => 'edit',
        'update' => 'edit',
        'destroy' => 'delete',

        'storeCategory' => 'create',
        'editCategory' => 'edit',
        'updateCategory' => 'edit',
        'destroyCategory' => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index()
{
    $categories = AttendanceTestCategory::withCount('options')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $options = ExternalAttendanceTestOption::with([
        'attendanceTest',
        'category',
        'test',
    ])
        ->orderBy('external_attendance_test_id')
        ->orderBy('sort_order')
        ->get();

    return view(
        'admin.attendance_test_options.index',
        compact('categories', 'options')
    );
}
    public function index_19oct()
    {
        $categories = AttendanceTestCategory::withCount('options')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $options = ExternalAttendanceTestOption::with([
            'attendanceTest',
            'category',
            'test',
        ])
            ->orderBy('external_attendance_test_id')
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.attendance_test_options.index',
            compact('categories', 'options')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Category
    |--------------------------------------------------------------------------
    */

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:attendance_test_categories,name',
            ],
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        while (
            AttendanceTestCategory::where('slug', $slug)->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        AttendanceTestCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.attendance-test-options.index')
            ->with('success', 'Category added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Category
    |--------------------------------------------------------------------------
    */

    public function editCategory(AttendanceTestCategory $category)
    {
        return view(
            'admin.attendance_test_options.category_edit',
            compact('category')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Category
    |--------------------------------------------------------------------------
    */

    public function updateCategory(
        Request $request,
        AttendanceTestCategory $category
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('attendance_test_categories', 'name')
                    ->ignore($category->id),
            ],
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.attendance-test-options.index')
            ->with('success', 'Category updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Category
    |--------------------------------------------------------------------------
    */

    public function destroyCategory(AttendanceTestCategory $category)
    {
        if ($category->options()->exists()) {
            return back()->with(
                'error',
                'This category is assigned to test options. Remove those options first.'
            );
        }

        $category->delete();

        return back()->with(
            'success',
            'Category deleted successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Option
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $attendanceTests = ExternalAttendanceTest::orderBy('title')->get();

        $categories = AttendanceTestCategory::where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tests = Test::where('is_active', 1)
            ->where('status', 'published')
            ->orderBy('title')
            ->get();

        return view(
            'admin.attendance_test_options.create',
            compact('attendanceTests', 'categories', 'tests')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store Option
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
{
    $validated = $request->validate([
        'external_attendance_test_id' => [
            'required',
            'integer',
            'exists:external_attendance_tests,id',
        ],
        'category_id' => [
            'required',
            'integer',
            'exists:attendance_test_categories,id',
        ],
        'test_id' => [
            'required',
            'integer',
            'exists:tests,id',
        ],
        'display_name' => 'required|string|max:150',
        'test_type' => 'required|in:technical,non_technical',
        'is_default' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
        'is_active' => 'required|boolean',
    ]);

    $this->validateOption(
        $validated['external_attendance_test_id'],
        $validated['category_id'],
        $validated['test_id']
    );

    $isDefault = $validated['test_type'] === 'non_technical'
        && (bool) ($validated['is_default'] ?? false);

    if ($isDefault && ! $validated['is_active']) {
        throw ValidationException::withMessages([
            'is_default' => 'An inactive option cannot be set as default.',
        ]);
    }

    DB::transaction(function () use ($validated, $isDefault) {
        if ($validated['test_type'] === 'technical') {
            $this->ensureSingleTechnicalOption(
                $validated['external_attendance_test_id']
            );
        }

        if ($isDefault) {
            // Clear only the default in this attendance test and category.
            ExternalAttendanceTestOption::where(
                'external_attendance_test_id',
                $validated['external_attendance_test_id']
            )
                ->where('category_id', $validated['category_id'])
                ->where('test_type', 'non_technical')
                ->update(['is_default' => 0]);
        }

        ExternalAttendanceTestOption::create([
            'external_attendance_test_id' =>
                $validated['external_attendance_test_id'],
            'category_id' => $validated['category_id'],
            'test_id' => $validated['test_id'],
            'display_name' => $validated['display_name'],
            'test_type' => $validated['test_type'],
            'is_default' => $isDefault,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);
    });

    return redirect()
        ->route('admin.attendance-test-options.index')
        ->with('success', 'Test option added successfully.');
}
    public function storeold(Request $request)
    {
        $validated = $request->validate([
            'external_attendance_test_id' => [
                'required',
                'integer',
                'exists:external_attendance_tests,id',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:attendance_test_categories,id',
            ],
            'test_id' => [
                'required',
                'integer',
                'exists:tests,id',
            ],
            'display_name' => 'required|string|max:150',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $this->validateOption(
            $validated['external_attendance_test_id'],
            $validated['category_id'],
            $validated['test_id']
        );

        DB::transaction(function () use ($validated) {
            $isDefault = (bool) ($validated['is_default'] ?? false);

            if ($isDefault) {
                ExternalAttendanceTestOption::where(
                    'external_attendance_test_id',
                    $validated['external_attendance_test_id']
                )->update(['is_default' => 0]);
            }

            ExternalAttendanceTestOption::create([
                'external_attendance_test_id' =>
                    $validated['external_attendance_test_id'],
                'category_id' => $validated['category_id'],
                'test_id' => $validated['test_id'],
                'display_name' => $validated['display_name'],
                'is_default' => $isDefault,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'],
            ]);
        });

        return redirect()
            ->route('admin.attendance-test-options.index')
            ->with('success', 'Test option added successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Option
    |--------------------------------------------------------------------------
    */

    public function edit(ExternalAttendanceTestOption $option)
    {
        $attendanceTests = ExternalAttendanceTest::orderBy('title')->get();

        $categories = AttendanceTestCategory::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $tests = Test::orderBy('title')->get();

        return view(
            'admin.attendance_test_options.edit',
            compact('option', 'attendanceTests', 'categories', 'tests')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Option
    |--------------------------------------------------------------------------
    */

    public function update(
    Request $request,
    ExternalAttendanceTestOption $option
) {
    $validated = $request->validate([
        'external_attendance_test_id' => [
            'required',
            'integer',
            'exists:external_attendance_tests,id',
        ],
        'category_id' => [
            'required',
            'integer',
            'exists:attendance_test_categories,id',
        ],
        'test_id' => [
            'required',
            'integer',
            'exists:tests,id',
        ],
        'display_name' => 'required|string|max:150',
        'test_type' => 'required|in:technical,non_technical',
        'is_default' => 'nullable|boolean',
        'sort_order' => 'nullable|integer|min:0',
        'is_active' => 'required|boolean',
    ]);

    $this->validateOption(
        $validated['external_attendance_test_id'],
        $validated['category_id'],
        $validated['test_id'],
        $option->id
    );

    $isDefault = $validated['test_type'] === 'non_technical'
        && (bool) ($validated['is_default'] ?? false);

    if ($isDefault && ! $validated['is_active']) {
        throw ValidationException::withMessages([
            'is_default' => 'An inactive option cannot be set as default.',
        ]);
    }

    DB::transaction(function () use ($validated, $option, $isDefault) {
        if ($validated['test_type'] === 'technical') {
            $this->ensureSingleTechnicalOption(
                $validated['external_attendance_test_id'],
                $option->id
            );
        }

        if ($isDefault) {
            // Clear only the default in the same category.
            ExternalAttendanceTestOption::where(
                'external_attendance_test_id',
                $validated['external_attendance_test_id']
            )
                ->where('category_id', $validated['category_id'])
                ->where('test_type', 'non_technical')
                ->where('id', '!=', $option->id)
                ->update(['is_default' => 0]);
        }

        $option->update([
            'external_attendance_test_id' =>
                $validated['external_attendance_test_id'],
            'category_id' => $validated['category_id'],
            'test_id' => $validated['test_id'],
            'display_name' => $validated['display_name'],
            'test_type' => $validated['test_type'],
            'is_default' => $isDefault,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);
    });

    return redirect()
        ->route('admin.attendance-test-options.index')
        ->with('success', 'Test option updated successfully.');
}
    public function updateold(
        Request $request,
        ExternalAttendanceTestOption $option
    ) {
        $validated = $request->validate([
            'external_attendance_test_id' => [
                'required',
                'integer',
                'exists:external_attendance_tests,id',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:attendance_test_categories,id',
            ],
            'test_id' => [
                'required',
                'integer',
                'exists:tests,id',
            ],
            'display_name' => 'required|string|max:150',
            'is_default' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
            'test_type' => 'required|in:technical,non_technical',
        ]);

        $this->validateOption(
            $validated['external_attendance_test_id'],
            $validated['category_id'],
            $validated['test_id'],
            $option->id
        );

        DB::transaction(function () use ($validated, $option) {
            $isDefault = (bool) ($validated['is_default'] ?? false);

            if ($isDefault) {
                ExternalAttendanceTestOption::where(
                    'external_attendance_test_id',
                    $validated['external_attendance_test_id']
                )
                    ->where('id', '!=', $option->id)
                    ->update(['is_default' => 0]);
            }

            $option->update([
                'external_attendance_test_id' =>
                    $validated['external_attendance_test_id'],
                'category_id' => $validated['category_id'],
                'test_id' => $validated['test_id'],
                'display_name' => $validated['display_name'],
                'is_default' => $isDefault,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $validated['is_active'],
                'test_type' => $validated['test_type'],
            ]);
        });

        return redirect()
            ->route('admin.attendance-test-options.index')
            ->with('success', 'Test option updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Option
    |--------------------------------------------------------------------------
    */

    public function destroy(ExternalAttendanceTestOption $option)
    {
        $option->delete();

        return redirect()
            ->route('admin.attendance-test-options.index')
            ->with('success', 'Test option deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Shared Validation
    |--------------------------------------------------------------------------
    */

    private function validateOption(
        int $attendanceTestId,
        int $categoryId,
        int $testId,
        ?int $ignoreOptionId = null
    ): void {
        AttendanceTestCategory::where('id', $categoryId)
            ->where('is_active', 1)
            ->firstOrFail();

        Test::where('id', $testId)
            ->where('is_active', 1)
            ->where('status', 'published')
            ->firstOrFail();

        $duplicate = ExternalAttendanceTestOption::where(
            'external_attendance_test_id',
            $attendanceTestId
        )
            ->where('category_id', $categoryId)
            ->where('test_id', $testId)
            ->when($ignoreOptionId, function ($query) use ($ignoreOptionId) {
                $query->where('id', '!=', $ignoreOptionId);
            })
            ->exists();

        if ($duplicate) {
            abort(
                422,
                'This test is already assigned to this category for the selected attendance form.'
            );
        }
    }

    private function ensureSingleTechnicalOption(
    int $attendanceTestId,
    ?int $ignoreOptionId = null
): void {
    $exists = ExternalAttendanceTestOption::where(
            'external_attendance_test_id',
            $attendanceTestId
        )
        ->where('test_type', 'technical')
        ->when($ignoreOptionId, function ($query) use ($ignoreOptionId) {
            $query->where('id', '!=', $ignoreOptionId);
        })
        ->exists();

    if ($exists) {
        throw ValidationException::withMessages([
            'test_type' =>
                'Only one Technical test can be configured for this attendance test.',
        ]);
    }
}
}