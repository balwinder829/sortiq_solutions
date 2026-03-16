<?php

namespace App\Http\Controllers;

use App\Models\InternshipRegistration;
use App\Mail\InternshipRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Models\College;
use App\Models\Course;
use App\Exports\InternshipRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;


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
        $this->middleware('auth');

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
            'phone'      => 'required|string|max:50',
            'page_type'      => 'nullable|string',
            'college'    => 'required|string|max:255',
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

        Mail::to($adminEmail)
            ->send(new InternshipRegistrationMail($registration));

        return back()->with('success', 'Registration submitted successfully.');
    }

   /**
     * GET /admin/internship-registrations
     */

   public function index(Request $request)
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
        return Excel::download(
            new InternshipRegistrationsExport($request),
            'internship_registrations_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
