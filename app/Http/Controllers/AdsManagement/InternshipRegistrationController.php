<?php

namespace App\Http\Controllers\AdsManagement;
use App\Http\Controllers\Controller;

use App\Models\InternshipRegistration;
use App\Mail\InternshipRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Models\College;
use App\Models\Course;
use App\Exports\InternshipRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\NotBlockedNumber;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Support\Str;


class InternshipRegistrationController extends Controller
{
    protected string $permissionPrefix = 'internship_registrations';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'export'         => 'view',
    ];

    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('auth')->except(['create', 'store']);

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }
    /**
     * Store – Frontend form submission
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            // 'phone'      => 'required|string|max:50',
            'phone' => ['required', 'string', new NotBlockedNumber],
            'page_type'      => 'nullable|string',
            // 'college'    => 'required|string|max:255',
            'college_name'    => 'required|string|max:255',
            'slug'       => 'required|string|max:255',
            'technology' => [
                'required',
                'string',
                'max:255',
                Rule::unique('internship_registrations')
                    ->where(fn ($q) => $q->where('email', $request->email))
            ],
            'message'    => 'nullable|string',
        ], [
            'technology.unique' => 'You have already applied for this technology using this email.'
        ]);

        $validated['ip_address'] = $request->ip();
        $registration = InternshipRegistration::create($validated);

        $adminEmail = config('app.admin_email', 'admin@example.com');

        // Mail::to($adminEmail)
        //     ->send(new InternshipRegistrationMail($registration));

        return back()->with('success', 'Registration submitted successfully.');
    }

   /**
     * GET /admin/internship-registrations
     */

   public function index(Request $request)
{
    if ($request->ajax()) {

        $query = InternshipRegistration::with([
            'collegeData' => fn($q) => $q->withTrashed(),
            'courseData' => fn($q) => $q->withTrashed()
        ])->latest();

        // College Filter
        if ($request->college) {
            $query->where('college_name', $request->college);
        }

        // Technology Filter
        if ($request->technology) {
            $query->where('technology', $request->technology);
        }

        // Slug Filter
        if ($request->slug) {
            $query->where('slug', $request->slug);
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filters
        |--------------------------------------------------------------------------
        */

        if ($request->date_filter) {

            switch ($request->date_filter) {

                case 'today':
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;

                case 'this_week':
                    $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;

                case 'last_week':
                    $query->whereBetween('created_at', [
                        now()->subWeek()->startOfWeek(),
                        now()->subWeek()->endOfWeek()
                    ]);
                    break;

                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;

                case 'custom':

                    if ($request->from_date && $request->to_date) {

                        $query->whereBetween('created_at', [
                            \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                            \Carbon\Carbon::parse($request->to_date)->endOfDay()
                        ]);
                    }

                    break;
            }
        }

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','full_name','email'],
            'searchable' => ['full_name','email','phone'],
        ], function ($row, $index, $start) {

            $actions = '
                <a href="' . route('internship-registrations.show', $row->id) . '" class="btn btn-sm">
                    <i class="fa fa-eye"></i>
                </a>

                <form action="' . route('internship-registrations.destroy', $row->id) . '" 
                      method="POST" 
                      style="display:inline-block;">

                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '

                    <button type="submit" class="btn btn-sm">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            ';

            $rowNum = $start + $index + 1;

            return [
                '<input type="checkbox" class="row-checkbox" value="'.$row->id.'">',
                $rowNum,
                e($row->full_name),
                e($row->email),
                e($row->phone ?? '-'),
                e(optional($row->collegeData)->FullName ?? $row->college_name ?? '-'),
                e(optional($row->courseData)->course_name ?? $row->technology ?? '-'),
                $row->created_at->format('d M Y'),
                $actions
            ];
        });
    }

    $colleges = InternshipRegistration::select('college_name')
        ->whereNotNull('college_name')
        ->distinct()
        ->orderBy('college_name')
        ->pluck('college_name');

    $technologies = InternshipRegistration::select('technology')
        ->whereNotNull('technology')
        ->distinct()
        ->orderBy('technology')
        ->pluck('technology');

    $slugs = InternshipRegistration::select('slug')
        ->distinct()
        ->pluck('slug');

    return view('pages_admin.index', compact('colleges','technologies','slugs'));
}

public function index14may(Request $request)
{
    if ($request->ajax()) {

        $query = InternshipRegistration::with([
            'collegeData' => fn($q) => $q->withTrashed(),
            'courseData' => fn($q) => $q->withTrashed()
        ])
        ->latest();

        if ($request->college) {
            $query->where('college_name', $request->college);
        }

        if ($request->technology) {
            $query->where('technology', $request->technology);
        }

        if ($request->slug) {
            $query->where('slug', $request->slug);
        }

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','full_name','email'],
            'searchable' => ['full_name','email','phone'],
        ], function ($row, $index, $start) {

            $actions = '
                <a href="' . route('internship-registrations.show', $row->id) . '" class="btn btn-sm">
                    <i class="fa fa-eye"></i>
                </a>

                <form action="' . route('internship-registrations.destroy', $row->id) . '" 
                      method="POST" 
                      style="display:inline-block;"
                      >

                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '

                    <button type="submit" class="btn btn-sm">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            ';

            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                e($row->full_name),
                e($row->email),
                e($row->phone ?? '-'),
                e(optional($row->collegeData)->FullName ?? $row->college_name ?? '-'),
                e(optional($row->courseData)->course_name ?? $row->technology ?? '-'),
                $row->created_at->format('d M Y'),
                $actions
            ];
        });
    }

    // NORMAL VIEW (UNCHANGED)
    // $colleges = College::orderBy('college_display_name')->get();
    // $technologies = Course::orderBy('course_name')->get();
    $colleges = InternshipRegistration::select('college_name')
        ->whereNotNull('college_name')
        ->distinct()
        ->orderBy('college_name')
        ->pluck('college_name');

    $technologies = InternshipRegistration::select('technology')
        ->whereNotNull('technology')
        ->distinct()
        ->orderBy('technology')
        ->pluck('technology');
    $slugs = InternshipRegistration::select('slug')->distinct()->pluck('slug');

    return view('pages_admin.index', compact('colleges','technologies','slugs'));
}
   public function indexoldd(Request $request)
{
    // $query = InternshipRegistration::with(['collegeData', 'courseData']);

    $query = InternshipRegistration::with([
        'collegeData' => function ($q) {
            $q->withTrashed();
        },
        'courseData' => function ($q) {
            $q->withTrashed();
        }
    ]);


    if ($request->filled('college')) {
        $query->where('college', $request->college);
    }

    if ($request->filled('technology')) {
        $query->where('technology', $request->technology);
    }

    if ($request->filled('slug')) {
        $query->where('slug', $request->slug);
    }

    $limit = $request->limit && $request->limit <= 100
    ? (int) $request->limit
    : 20;

    $registrations = $query
        ->latest()
        ->paginate($limit)
        ->withQueryString();

    $colleges = College::orderBy('college_display_name')->get([
        'id',
        'college_display_name',
        'college_name',
        'college_short_name',
    ]);

    $technologies = Course::orderBy('course_name')->get([
        'id',
        'course_name'
    ]);

    $slugs = InternshipRegistration::select('slug')
    ->distinct()
    ->orderBy('slug')
    ->pluck('slug');

    return view(
        'pages_admin.index',
        compact('registrations', 'colleges', 'technologies', 'slugs')
    );
}
    // public function index(Request $request)
    // {
    //     $query = InternshipRegistration::query()
    //     ->with(['collegeData', 'courseData']); 

    //     // FILTERS
    //     if ($request->filled('page_type')) {
    //         $query->where('page_type', $request->page_type);
    //     }

    //     if ($request->filled('college')) {
    //         $query->where('college', 'like', '%' . $request->college . '%');
    //     }

    //     if ($request->filled('technology')) {
    //         $query->where('technology', 'like', '%' . $request->technology . '%');
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     $registrations = $query
    //         ->latest()
    //         ->paginate(20)
    //         ->withQueryString();

    //     // For dropdowns (distinct values)
    //     $pageTypes = InternshipRegistration::select('page_type')
    //         ->distinct()
    //         ->pluck('page_type');

    //   $colleges = InternshipRegistration::query()
    // ->join('colleges', 'internship_registrations.college', '=', 'colleges.id')
    // ->select(
    //     'colleges.id',
    //     'colleges.college_display_name'
    // )
    // ->distinct()
    // ->orderBy('colleges.college_display_name')
    // ->get();


    //     $technologies = InternshipRegistration::query()
    //     ->join('courses', 'internship_registrations.technology', '=', 'courses.id')
    //     ->select('courses.id', 'courses.course_name')
    //     ->distinct()
    //     ->orderBy('courses.course_name')
    //     ->get();

    //     return view(
    //         'pages_admin.index',
    //         compact('registrations', 'pageTypes', 'colleges', 'technologies')
    //     );
    // }

    /**
     * GET /admin/internship-registrations/{internship_registration}
     */
    public function show(InternshipRegistration $internship_registration)
    {
        return view(
            'pages_admin.show',
            compact('internship_registration')
        );
    }

    /**
     * DELETE /admin/internship-registrations/{internship_registration}
     */
    public function destroy(InternshipRegistration $internship_registration)
    {
        $internship_registration->delete();

        return back()->with('success', 'Registration deleted.');
    }

    /**
     * PATCH status update
     */
    public function updateStatus(
        Request $request,
        InternshipRegistration $internship_registration
    ) {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $internship_registration->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function export(Request $request)
    {   
          $fileName = 'internship_registrations';

        if ($request->college) {
            $fileName .= '_' . Str::slug($request->college, '_');
        }

        if ($request->technology) {
            $fileName .= '_' . Str::slug($request->technology, '_');
        }

        if ($request->slug) {
            $fileName .= '_' . Str::slug($request->slug, '_');
        }

        if ($request->status) {
            $fileName .= '_' . Str::slug($request->status, '_');
        }

        if ($request->limit) {
            $fileName .= '_limit_' . $request->limit;
        }

        $fileName .= '_' . now()->format('d_F') . '.xlsx';
        return Excel::download(
            new InternshipRegistrationsExport($request),
            $fileName
        );
    }
}
