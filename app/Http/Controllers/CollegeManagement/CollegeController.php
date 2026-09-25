<?php

namespace App\Http\Controllers\CollegeManagement;
use App\Http\Controllers\Controller;

use App\Models\College;
use App\Models\State;
use App\Models\District;
use App\Models\Student;
use App\Models\CollegeDepartment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\CollegesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\CollegeResolver;
use App\Imports\CollegesImport;
use App\Exports\CollegeStudentsExport;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CollegeController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:colleges.view')->only('index');
        $this->middleware('permission:colleges.create')->only(['create','store']);
        $this->middleware('permission:colleges.edit')->only(['edit','update']);
        $this->middleware('permission:colleges.delete')->only('destroy');
        $this->middleware('permission:colleges.import')->only('import');
    }

    public function create()
    {   
        $states = State::orderBy('name')->get();
        $collegeDepartments = CollegeDepartment::active()
        ->orderBy('sort_order')
        ->get();
        return view('colleges.create', compact('states','collegeDepartments'));
        // return view('colleges.create');
    }

    

 public function index()
    {
        $states = State::orderBy('name')->get();

        $districtsGrouped = District::select('districts.id', 'districts.name', 'districts.state_id', 'states.name as state_name')
            ->join('states', 'states.id', '=', 'districts.state_id')
            ->orderBy('districts.name')
            ->get()
            ->groupBy('state_id');

        $collegeDepartments = CollegeDepartment::active()
        ->orderBy('sort_order')
        ->get();

        return view('colleges.index', compact('states', 'districtsGrouped','collegeDepartments'));
    }

    /**
     * Server-side DataTables: returns only the current page of rows (no full load).
     */
    public function data(Request $request)
    {

        $activeSessionId = session('admin_session_id');
        $status = $request->get('status', 'active');
        $query = College::query()
            ->with(['state', 'district'])
            ->where('colleges.status', $status)
            // ->withCount([
            //     'students as students_count' => function ($q) use ($activeSessionId) {
            //         $q->where('session', $activeSessionId);
            //     }
            // ]);

            ->withCount([
                // Total students
                'students as students_count' => function ($q) use ($activeSessionId) {
                    $q->where('session', $activeSessionId);
                },

                // Confirmation students
                'students as confirmation_students_count' => function ($q) use ($activeSessionId) {
                    $q->where('session', $activeSessionId)
                      ->where('certificate_status', 0);
                },

                // Certificate students
                'students as certificate_students_count' => function ($q) use ($activeSessionId) {
                    $q->where('session', $activeSessionId)
                      ->whereIn('certificate_status', [1, 2])
                        ->where('send_to_close', 0);
                },

                // Dropout
                // 'students as dropout_students_count' => function ($q) use ($activeSessionId) {
                //     $q->where('session', $activeSessionId)
                //       ->where('certificate_status', 4);
                // },
            ]);

        // State filter (by state name from dropdown)
        if ($request->filled('state_name')) {
            $query->whereHas('state', fn ($q) => $q->where('name', $request->state_name));
        }
        if ($request->filled('district_name')) {
            $query->whereHas('district', fn ($q) => $q->where('name', $request->district_name));
        }

        // College Type filter
        if ($request->filled('college_type')) {
            $query->where('college_type', $request->college_type);
        }

        // Training filter
        if ($request->filled('offer_training')) {
            $query->where('offer_training', $request->offer_training);
        }
        if ($request->call_status !== null && $request->call_status !== '') {
            $query->where('call_status', $request->call_status);
        }

        // Important
        if ($request->filled('is_important')) {
            $query->where('is_important', $request->is_important);
        }

        // Ownership
        if ($request->filled('ownership_type')) {
            $query->where('ownership_type', $request->ownership_type);
        }

        // Connection
        if ($request->filled('connection_type')) {
            $query->where('connection_type', $request->connection_type);
        }

        // Department (JSON)
        // if ($request->filled('department')) {
        //     $query->whereJsonContains('departments', $request->department);
        // }

        // Department filter - multiple departments
       // Department - Multiple

        \Log::info('COLLEGE FILTER DEBUG', [
            'college_type' => $request->college_type,
            'departments' => $request->input('departments'),
        ]);
        if (!empty($request->input('departments')) && is_array($request->input('departments'))) {

            $departments = array_values(array_filter(
                $request->input('departments'),
                fn ($department) => $department !== null && $department !== ''
            ));

            if (!empty($departments)) {
                $query->where(function ($q) use ($departments) {

                    foreach ($departments as $department) {
                        $q->orWhereJsonContains('departments', $department);
                    }

                });
            }
        }
        // Training Schedule Filters

        if ($request->filled('training_21_days')) {
            $query->where('training_schedule->21_days', $request->training_21_days);
        }

        if ($request->filled('training_45_days')) {
            $query->where('training_schedule->45_days', $request->training_45_days);
        }

        if ($request->filled('training_6_months')) {
            $query->where('training_schedule->6_months', $request->training_6_months);
        }

        // Training In
        if ($request->filled('training_in')) {
            $query->where(
                'training_in',
                $request->training_in
            );
        }

        // Training Times in Year
        if ($request->filled('training_in_year')) {
            $query->where(
                'training_in_year',
                $request->training_in_year
            );
        }

        // Whom to Connect
        if ($request->filled('connected_to')) {
            $query->where(
                'connected_to',
                $request->connected_to
            );
        }

        // Reference By
        if ($request->filled('reference_by')) {

            $query->where(
                'reference_by',
                'like',
                '%' . $request->reference_by . '%'
            );
        }

        // Contact Person
        // if ($request->filled('contact_person')) {

        //     $query->where(
        //         'contact_person',
        //         'like',
        //         '%' . $request->contact_person . '%'
        //     );
        // }
        // 👇 ADD HERE
        if ($request->filled('student_filter')) {

            if ($request->student_filter == 'zero') {
                $query->having('students_count', '=', 0);
            }

            if ($request->student_filter == 'more') {
                $query->having('students_count', '>', 0);
            }
        }else{
            // $query->orderBy('updated_at', 'desc');
        }

        $total = $query->count();

        // DataTables search (global)
        if ($request->filled('search.value')) {
            $term = $request->input('search.value');
            $query->where(function ($q) use ($term) {
                $q->where('colleges.college_name', 'like', '%' . $term . '%')
                    ->orWhereHas('state', fn ($sq) => $sq->where('name', 'like', '%' . $term . '%'))
                    ->orWhereHas('district', fn ($sq) => $sq->where('name', 'like', '%' . $term . '%'));
            });
        }

        $filteredTotal = $query->count();

       

        /* ================= ORDERING SECTION ================= */

        // $orderCol = $request->input('order.0.column');
        // $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $orderCol = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        $orderable = [
            0 => 'id',
            1 => 'college_id',
            2 => 'college_name',
            3 => 'state',
            4 => 'district',
            5 => 'students_count',
            6 => 'confirmation_students_count',
            7 => 'certificate_students_count',
            // 8 => 'dropout_students_count',
            8 => 'college_type',
            9 => 'offer_training',
            10 => 'training_in_year'
        ];

        $orderField = $orderable[$orderCol] ?? null;

        /*
        |--------------------------------------------------------------------------
        | ORDERING
        |--------------------------------------------------------------------------
        |
        | Priority:
        | 1. student_filter sorting
        | 2. DataTable column sorting
        | 3. Default latest updated
        |
        */

        if (
            $request->filled('student_filter') &&
            in_array($request->student_filter, ['asc', 'desc'])
        ) {

            // Student count sorting
            $query->orderBy('students_count', $request->student_filter);

        } elseif ($orderField) {

            // DataTable column sorting

            switch ($orderField) {

                case 'id':
                    $query->orderBy('colleges.id', $orderDir);
                    break;

                case 'college_id':
                    $query->orderBy('colleges.id', $orderDir);
                    break;

                case 'college_name':
                    $query->orderBy('colleges.college_name', $orderDir);
                    break;

                case 'students_count':
                    $query->orderBy('students_count', $orderDir);
                    break;

                case 'state':
                    $query->orderByRaw(
                        '(SELECT name FROM states WHERE states.id = colleges.state_id) ' . $orderDir
                    );
                    break;

                case 'district':
                    $query->orderByRaw(
                        '(SELECT name FROM districts WHERE districts.id = colleges.district_id) ' . $orderDir
                    );
                    break;

                case 'college_type':
                    $query->orderBy('colleges.college_type', $orderDir);
                    break;

                case 'offer_training':
                    $query->orderBy('colleges.offer_training', $orderDir);
                    break;

                case 'training_in_year':
                    $query->orderBy('colleges.training_in_year', $orderDir);
                    break;

                default:
                    $query->latest('colleges.updated_at');
            }

        } else {

            // DEFAULT ORDER
            $query->latest('colleges.updated_at');
        }
       

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 50);
        if ($length < 1 || $length > 100) {
            $length = 50;
        }

        $colleges = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($colleges as $index => $college) {
            
            $rowNum = $start + $index + 1;
            // $collegeType = $college->college_type == 0 ? 'Degree' : 'Diploma';
            $collegeType = $college->college_type_label;
            $training = $college->offer_training == 1 ? 'Yes' : 'No';
            $statusToggle = '
                <label class="switch">
                    <input type="checkbox" class="toggle-status"
                        data-id="'.$college->id.'"
                        '.($college->call_status ? 'checked' : '').'>
                    <span class="slider round"></span>
                </label>';
            $data[] = [
                '<input type="checkbox"
                class="record_checked"
                value="'.$college->id.'">',
                $rowNum,
                $college->id,
                // $college->college_name,
                $college->is_important
                    ? '<span class="text-warning" title="Important College">⭐</span> ' . e($college->college_name)
                    : e($college->college_name),
                $college->state->name ?? '-',
                $college->district->name ?? '-',
                '<a href="' . route('common_filtered_student', ['college_name' => $college->id]) . '" class="text-decoration-none"><span class="badge bg-success">' . $college->students_count . '</span></a>',

                // Total
                

                    // Confirmation
                    '<a href="' . route('students.index', [
                        'college_name' => $college->id
                    ]) . '" class="text-decoration-none">
                        <span class="badge bg-info">'
                            . $college->confirmation_students_count .
                        '</span>
                    </a>',

                    // Certificate
                    '<a href="' . route('certificates.index', [
                        'college_name' => $college->id
                        
                    ]) . '" class="text-decoration-none">
                        <span class="badge bg-primary">'
                            . $college->certificate_students_count .
                        '</span>
                    </a>',

                    // Dropout
                    // '<a href="' . route('dropout-students.index', [
                    //     'college_name' => $college->id
                    // ]) . '" class="text-decoration-none">
                    //     <span class="badge bg-danger">'
                    //         . $college->dropout_students_count .
                    //     '</span>
                    // </a>',
                $collegeType,
                $training,
                $college->training_in_year,
                
                '<div class="mb-2">' .
                    '<a href="' . route('colleges.edit', $college->id) . '" class="btn btn-sm" data-bs-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> ' .
                        '<form action="' . route('colleges.destroy', $college->id) . '" method="POST" class="college-delete-form" style="display:inline;">' .
                        csrf_field() . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" title="Delete">' .
                        '<i class="fa fa-trash"></i>' .
                        '</button>' .
                        '</form></div>',
            ];
        }

        return response()->json([
            'draw'            => (int) $request->input('draw', 1),
            'recordsTotal'    => $total,
            'recordsFiltered' => $filteredTotal,
            'data'            => $data,
        ]);
    }



public function store(Request $request)
{
    $data = $request->validate([
        'college_name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('colleges')->where(function ($query) use ($request) {
                return $query->where('state_id', $request->state_id)
                             ->where('district_id', $request->district_id)
                             ->whereNull('deleted_at'); 
            }),
        ],
        'college_display_name' => 'nullable|string|max:255',
        'state_id'             => 'required|exists:states,id',
        'district_id'          => 'required|exists:districts,id',
        'college_type'          => 'required',
        'offer_training'          => 'required',
        'training_in_year'          => 'nullable',
        'is_important' => 'required|boolean',
        'departments' => 'nullable|array',
        'departments.*' => 'string',
        'ownership_type' => 'required|in:0,1',
        'connection_type' => 'required|in:0,1',
        'seminar_count'        => 'nullable|integer|min:0',
        'placement_count'      => 'nullable|integer|min:0',
        'connected_to'         => 'nullable|string|max:100',
        'training_in'     => 'nullable|in:Degree,Diploma,Both',
        'reference_by'    => 'nullable|string|max:255',
        'contact_person'  => 'nullable|string|max:255',
        'training_schedule'          => 'nullable|array',
        'training_schedule.21_days'  => 'nullable|integer|min:0|max:8',
        'training_schedule.45_days'  => 'nullable|integer|min:0|max:8',
        'training_schedule.6_months' => 'nullable|integer|min:0|max:8',
        'training_months' => 'nullable',
    ], [
        'college_name.unique' => 'This college already exists in the selected district.'
    ]);

    /** ---------------------------------
     * Centralized college handling
     * --------------------------------- */
    //  $college = app(CollegeResolver::class)->resolveWithLocation(
    //     $data['college_name'],
    //     $data['state_id'],
    //     $data['district_id'],
    //     $data['college_display_name'] // 👈 user-entered
    // );

    /*
     * Normalize training schedule.
     *
     * Always store all three durations,
     * even if the user doesn't select anything.
     */
    $data['training_schedule'] = [
        '21_days'  => (int) ($data['training_schedule']['21_days'] ?? 0),
        '45_days'  => (int) ($data['training_schedule']['45_days'] ?? 0),
        '6_months' => (int) ($data['training_schedule']['6_months'] ?? 0),
    ];

    $data['training_in_year'] = 0;

    $college = app(CollegeResolver::class)->resolveWithLocation($data);
    return redirect()
        ->route('colleges.index')
        ->with('success', 'College saved successfully.');
}

 

    public function show(College $college)
    {
        return view('colleges.show', compact('college'));
    }

    public function edit(College $college)
    {   
         $states = State::orderBy('name')->get();
         // districts for the selected state (so edit form can pre-load)
        $districts = $college->state ? $college->state->districts()->orderBy('name')->get() : collect();

        $collegeDepartments = CollegeDepartment::active()
            ->orderBy('sort_order')
            ->get();
         return view('colleges.edit', compact('college','states','districts','collegeDepartments'));
    }

   
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'college_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colleges')
                    ->where(function ($query) use ($request) {
                        return $query->where('state_id', $request->state_id)
                                     ->where('district_id', $request->district_id)
                                     ->whereNull('deleted_at'); 
                    })
                    ->ignore($id),
            ],
            'college_display_name'  => 'required|string|max:255',
            'college_short_name'  => 'required|string|max:255',
            'state_id'              => 'required|exists:states,id',
            'district_id'           => 'required|exists:districts,id',
            'college_type'          => 'required',
            'offer_training'          => 'required',
            'training_in_year'          => 'nullable',
            'is_important'   => 'nullable|boolean',
            'departments'    => 'nullable|array',
            'departments.*'  => 'string',
            'ownership_type' => 'nullable|in:0,1',
            'connection_type'=> 'nullable|in:0,1',
            'seminar_count' => 'nullable|integer|min:0',
            'placement_count' => 'nullable|integer|min:0',
            'connected_to' => 'nullable|string|max:100',
            'training_in'     => 'nullable|in:Degree,Diploma,Both',
            'reference_by'    => 'nullable|string|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'training_schedule'          => 'nullable|array',
            'training_schedule.21_days'  => 'nullable|integer|min:0|max:8',
            'training_schedule.45_days'  => 'nullable|integer|min:0|max:8',
            'training_schedule.6_months' => 'nullable|integer|min:0|max:8',
            'training_months' => 'nullable',

        ], [
            'college_name.unique' => 'This college already exists in the selected district.'
        ]);

        $college = College::findOrFail($id);

        /** Resolve clean_name + slug from service */
        $resolver  = app(\App\Services\CollegeResolver::class);
        $cleanName = $resolver->makeCleanName($data['college_name']);
        $slug      = $resolver->makeSlug($data['college_name']);
        $shortname = $data['college_short_name'];
        /** Duplicate check (exclude current college) */
        // $exists = College::withTrashed()
        //     ->where('clean_name', $cleanName)
        //     ->where('state_id', $data['state_id'])
        //     ->where('district_id', $data['district_id'])
        //     ->where('id', '!=', $college->id)
        //     ->exists();

        // if ($exists) {
        //     return back()
        //         ->withErrors([
        //             'college_name' =>
        //                 'This college already exists in the selected state and district.'
        //         ])
        //         ->withInput();
        // }

        /** Update record */

        $data['training_schedule'] = [
            '21_days'  => (int) ($data['training_schedule']['21_days'] ?? 0),
            '45_days'  => (int) ($data['training_schedule']['45_days'] ?? 0),
            '6_months' => (int) ($data['training_schedule']['6_months'] ?? 0),
        ];

        $college->update([
            'college_name'         => $data['college_name'],
            'college_display_name' => $data['college_display_name'], // user-entered
            'clean_name'           => $cleanName,
            'college_short_name'           => $shortname,
            'slug'                 => $slug,
            'state_id'             => $data['state_id'],
            'district_id'          => $data['district_id'],
            'college_type'         => $data['college_type'],
            'offer_training'       => $data['offer_training'],
            // 'training_in_year'     => $data['training_in_year'],
            'is_important'         => $data['is_important'] ?? 0,
            'departments'          => $data['departments'] ?? [],
            'ownership_type'       => $data['ownership_type'] ?? 0,
            'connection_type'      => $data['connection_type'] ?? 0,
            'seminar_count'        => $data['seminar_count'] ?? 0,
            'placement_count'      => $data['placement_count'] ?? 0,
            'connected_to'         => $data['connected_to'] ?? null,
            'training_in'          => $data['training_in'] ?? null,
            'reference_by'         => $data['reference_by'] ?? null, 'contact_person' => $data['contact_person'] ?? null,
            'training_schedule'    => $data['training_schedule'],
            'training_months'   => $data['training_months'] ?? null,
        ]);
    // dd($college);
        return redirect()
            ->route('colleges.index')
            ->with('success', 'College updated successfully.');
    }

    public function shift(Request $request)
    {
        $colleges = College::query()
            ->orderBy('college_name')
            ->get([
                'id',
                'college_name',
                'college_display_name',
                'college_short_name',
                'state_id',
                'district_id',
            ]);

        if ($request->ajax() && $request->filled('source_college_id')) {

            $sourceId = (int) $request->source_college_id;

            $source = College::findOrFail($sourceId);

            return response()->json([
                'success' => true,
                'college' => [
                    'id' => $source->id,
                    'name' => $source->college_name,
                ],
                'counts' => $this->getCollegeShiftCounts($sourceId),
            ]);
        }

        return view('colleges.shift', compact('colleges'));
    }

    private function collegeShiftMap()
    {
        return [

            // college_id
            'college_call_logs' => [
                'column' => 'college_id',
                'name' => 'College Call Logs',
            ],

            'college_email_recipients' => [
                'column' => 'college_id',
                'name' => 'College Email Recipients',
            ],

            'college_call_statuses' => [
                'column' => 'college_id',
                'name' => 'College Call Statuses',
            ],

            'college_email_statuses' => [
                'column' => 'college_id',
                'name' => 'College Email Statuses',
            ],

            'events' => [
                'column' => 'college_id',
                'name' => 'Events',
            ],

            'external_attendance_links' => [
                'column' => 'college_id',
                'name' => 'External Attendance Links',
            ],

            'external_attendance_submissions' => [
                'column' => 'college_id',
                'name' => 'External Attendance Submissions',
            ],

            'external_attendance_tests' => [
                'column' => 'college_id',
                'name' => 'External Attendance Tests',
            ],

            'hard_data' => [
                'column' => 'college_id',
                'name' => 'Hard Data',
            ],

            'manual_data' => [
                'column' => 'college_id',
                'name' => 'Manual Data',
            ],

            'student_pending_registration' => [
                'column' => 'college_id',
                'name' => 'Pending Student Registration',
            ],

            'student_tests' => [
                'column' => 'college_id',
                'name' => 'Student Tests',
            ],

            'hods' => [
                'column' => 'college_id',
                'name' => 'HOD / TPO Records',
            ],

            'mous' => [
                'column' => 'college_id',
                'name' => 'MOUs',
            ],

            'workshops' => [
                'column' => 'college_id',
                'name' => 'Workshops',
            ],

            'tests' => [
                'column' => 'college_id',
                'name' => 'Tests',
            ],

            'test_links' => [
                'column' => 'college_id',
                'name' => 'Test Links',
            ],

            // college column stores College ID
            'enquiries' => [
                'column' => 'college',
                'name' => 'Enquiries',
            ],

            'joining_students' => [
                'column' => 'college',
                'name' => 'Joining Students',
            ],

            // 'leads' => [
            //     'column' => 'college',
            //     'name' => 'Leads',
            // ],

            // 'student_custom_letters' => [
            //     'column' => 'college',
            //     'name' => 'Student Custom Letters',
            // ],

            // college_name stores College ID
            'students_detail' => [
                'column' => 'college_name',
                'name' => 'Students',
            ],

            'placements' => [
                'column' => 'college_name',
                'name' => 'Placements',
            ],

            // college stores College ID.
            // college_name is the actual name and MUST NOT be changed.
            'internship_registrations' => [
                'column' => 'college',
                'name' => 'Internship Registrations',
            ],
        ];
    }

    private function getCollegeShiftCounts($collegeId)
    {
        $counts = [];

        foreach ($this->collegeShiftMap() as $table => $config) {

            $count = DB::table($table)
                ->where($config['column'], $collegeId)
                ->count();

            $counts[] = [
                'table' => $table,
                'name' => $config['name'],
                'count' => $count,
            ];
        }

        return $counts;
    }

    public function processShift(Request $request)
    {
        $data = $request->validate([
            'source_college_id' => [
                'required',
                'integer',
                'exists:colleges,id',
            ],

            'target_college_id' => [
                'required',
                'integer',
                'exists:colleges,id',
                'different:source_college_id',
            ],

            'verified' => [
                'required',
                'accepted',
            ],
        ], [
            'verified.accepted' =>
                'Please verify the source college, target college and records before shifting.',
        ]);

        $sourceId = (int) $data['source_college_id'];
        $targetId = (int) $data['target_college_id'];

        $source = College::findOrFail($sourceId);
        $target = College::findOrFail($targetId);

        /*
        |--------------------------------------------------------------------------
        | Keep target College record completely untouched.
        |--------------------------------------------------------------------------
        */

        $countsBefore = $this->getCollegeShiftCounts($sourceId);

        $totalRecords = collect($countsBefore)->sum('count');

        try {

            DB::transaction(function () use (
                $sourceId,
                $targetId
            ) {

                foreach ($this->collegeShiftMap() as $table => $config) {

                    DB::table($table)
                        ->where($config['column'], $sourceId)
                        ->update([
                            $config['column'] => $targetId,
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Verify source is now completely empty.
                |--------------------------------------------------------------------------
                */

                foreach ($this->collegeShiftMap() as $table => $config) {

                    $remaining = DB::table($table)
                        ->where($config['column'], $sourceId)
                        ->count();

                    if ($remaining > 0) {

                        throw new \RuntimeException(
                            'Shift verification failed in table: ' . $table
                        );
                    }
                }
            });

        } catch (\Throwable $e) {

            return redirect()
                ->route('colleges.shift')
                ->withInput()
                ->with(
                    'error',
                    'College shift failed. No records were changed. ' . $e->getMessage()
                );
        }

        return redirect()
            ->route('colleges.index')
            ->with(
                'success',
                'College "' . $source->college_name .
                '" was successfully shifted to "' .
                $target->college_name .
                '". ' .
                $totalRecords .
                ' dependent records were shifted.'
            );
    }


    public function destroy(College $college)
    {
        $collegeId = $college->id;

        /*
        |--------------------------------------------------------------------------
        | Table => college columns + soft delete setting
        |--------------------------------------------------------------------------
        |
        | All these columns contain the COLLEGE ID.
        |
        | soft_delete:
        | true  = check only active records (deleted_at IS NULL)
        | false = count all records
        |
        */

        $checks = [

            // -------------------------------------------------
            // College Call / Email
            // -------------------------------------------------

            // 'college_call_logs' => [
            //     'columns' => ['college_id'],
            //     'soft_delete' => false,
            //     'name' => 'College Call Logs',
            // ],

            // 'college_email_recipients' => [
            //     'columns' => ['college_id'],
            //     'soft_delete' => false,
            //     'name' => 'College Email Recipients',
            // ],

            // -------------------------------------------------
            // Events
            // -------------------------------------------------

            'events' => [
                'columns' => ['college_id'],
                'soft_delete' => false,
                'name' => 'Events',
            ],

            // -------------------------------------------------
            // External Attendance
            // -------------------------------------------------

            'external_attendance_links' => [
                'columns' => ['college_id'],
                'soft_delete' => true,
                'name' => 'External Attendance Links',
            ],

            'external_attendance_submissions' => [
                'columns' => ['college_id'],
                'soft_delete' => false,
                'name' => 'External Attendance Submissions',
            ],

            'external_attendance_tests' => [
                'columns' => ['college_id'],
                'soft_delete' => true,
                'name' => 'External Attendance Tests',
            ],

            // -------------------------------------------------
            // Student Data
            // -------------------------------------------------

            'hard_data' => [
                'columns' => ['college_id', 'college_name'],
                'soft_delete' => true,
                'name' => 'Hard Data',
            ],

            'manual_data' => [
                'columns' => ['college_id', 'college_name'],
                'soft_delete' => true,
                'name' => 'Manual Data',
            ],

            'student_pending_registration' => [
                'columns' => [
                    'college_id',
                    'college_name_input'
                ],
                'soft_delete' => false,
                'name' => 'Pending Student Registration',
            ],

            'student_tests' => [
                'columns' => ['college_id'],
                'soft_delete' => false,
                'name' => 'Student Tests',
            ],

            'students_detail' => [
                'columns' => ['college_name'],
                'soft_delete' => true,
                'name' => 'Students',
            ],

            // -------------------------------------------------
            // College Management
            // -------------------------------------------------

            'hods' => [
                'columns' => ['college_id'],
                'soft_delete' => true,
                'name' => 'HOD / TPO Records',
            ],

            'mous' => [
                'columns' => ['college_id'],
                'soft_delete' => true,
                'name' => 'MOUs',
            ],

            'workshops' => [
                'columns' => ['college_id'],
                'soft_delete' => false,
                'name' => 'Workshops',
            ],

            // -------------------------------------------------
            // Tests
            // -------------------------------------------------

            'tests' => [
                'columns' => ['college_id'],
                'soft_delete' => true,
                'name' => 'Tests',
            ],

            'test_links' => [
                'columns' => ['college_id'],
                'soft_delete' => true,
                'name' => 'Test Links',
            ],

            // -------------------------------------------------
            // Enquiries
            // -------------------------------------------------

            'enquiries' => [
                'columns' => ['college'],
                'soft_delete' => true,
                'name' => 'Enquiries',
            ],

            // -------------------------------------------------
            // Joining Students
            // -------------------------------------------------

            'joining_students' => [
                'columns' => ['college'],
                'soft_delete' => true,
                'name' => 'Joining Students',
            ],

            // -------------------------------------------------
            // Student Custom Letters
            // -------------------------------------------------

            // 'student_custom_letters' => [
            //     'columns' => ['college'],
            //     'soft_delete' => true,
            //     'name' => 'Student Custom Letters',
            // ],

            // -------------------------------------------------
            // Placements
            // -------------------------------------------------

            'placements' => [
                'columns' => ['college_name'],
                'soft_delete' => true,
                'name' => 'Placements',
            ],

            // -------------------------------------------------
            // Internship Registrations
            // -------------------------------------------------

            'internship_registrations' => [
                'columns' => [
                    'college',
                    'college_name'
                ],
                'soft_delete' => true,
                'name' => 'Internship Registrations',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | Check all tables
        |--------------------------------------------------------------------------
        */

        $usedIn = [];

        foreach ($checks as $table => $config) {

            foreach ($config['columns'] as $column) {

                $query = DB::table($table)
                    ->where($column, $collegeId);

                if ($config['soft_delete']) {
                    $query->whereNull('deleted_at');
                }

                $count = $query->count();

                if ($count > 0) {

                    // Friendly name => count
                    $usedIn[$config['name']] = $count;

                    break;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Records found - DON'T DELETE
        |--------------------------------------------------------------------------
        */

        if (!empty($usedIn)) {

            return redirect()
                ->route('colleges.index')
                ->with('delete_error', [
                    'college' => $college->college_name,
                    'records' => $usedIn,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | No records found - DELETE COLLEGE
        |--------------------------------------------------------------------------
        */

        $college->delete();

        return redirect()
            ->route('colleges.index')
            ->with('success', 'College deleted successfully.');
    }

 public function destroy_27aug(College $college)
{
    $collegeId = $college->id;

    $checks = [

        // college_id
        // 'college_call_logs' => ['college_id'],
        // 'college_email_recipients' => ['college_id'],
        'events' => ['college_id'],

        'external_attendance_links' => ['college_id'],
        'external_attendance_submissions' => ['college_id'],
        'external_attendance_tests' => ['college_id'],

        'hard_data' => ['college_id', 'college_name'],
        'hods' => ['college_id'],
        'manual_data' => ['college_id', 'college_name'],
        'mous' => ['college_id'],

        'student_pending_registration' => [
            'college_id',
            'college_name_input'
        ],

        'student_tests' => ['college_id'],
        'tests' => ['college_id'],
        'test_links' => ['college_id'],
        'workshops' => ['college_id'],

        // college stored in a differently named column
        'enquiries' => ['college'],
        
        'joining_students' => ['college'],
        'student_custom_letters' => ['college'],

        // college ID stored in college_name
        'students_detail' => ['college_name'],
        'placements' => ['college_name'],

        // both columns contain college ID
        'internship_registrations' => [
            'college',
            'college_name'
        ],
    ];

    $usedIn = [];

    foreach ($checks as $table => $columns) {

        foreach ($columns as $column) {

            $count = DB::table($table)
                ->where($column, $collegeId)
                ->count();

            if ($count > 0) {

                $usedIn[$table] = $count;

                // Don't check the second column
                // once this table has been found.
                break;
            }
        }
    }

    if (!empty($usedIn)) {

        return redirect()
            ->route('colleges.index')
            ->with('delete_error', [
                'college' => $college->college_name,
                'records' => $usedIn,
            ]);
    }

    $college->delete();

    return redirect()
        ->route('colleges.index')
        ->with('success', 'College deleted successfully.');
}

public function destroy_final(College $college)
{
    $collegeId   = $college->id;
    $collegeName = trim($college->college_name);

    /*
    |--------------------------------------------------------------------------
    | table => column
    |--------------------------------------------------------------------------
    */
    $checks = [

        // =========================
        // college_id
        // =========================

        'college_call_logs' => 'college_id',
        'college_email_recipients' => 'college_id',
        'events' => 'college_id',
        'external_attendance_links' => 'college_id',
        'external_attendance_submissions' => 'college_id',
        'external_attendance_tests' => 'college_id',
        'hard_data' => 'college_id',
        'hods' => 'college_id',
        'mous' => 'college_id',
        'student_pending_registration' => 'college_id',
        'student_tests' => 'college_id',
        'tests' => 'college_id',
        'test_links' => 'college_id',
        'workshops' => 'college_id',

        // =========================
        // college_name
        // =========================

        'students_detail' => 'college_name',
        'placements' => 'college_name',

        // =========================
        // college
        // =========================

        'leads' => 'college',
        'joining_students' => 'college',
        'student_custom_letters' => 'college',

        // =========================
        // college_name_input
        // =========================

        'student_pending_registration' => 'college_name_input',

        // =========================
        // internship registrations
        // =========================

        'internship_registrations' => 'college',
        'internship_registrations' => 'college_name',
    ];

    $usedIn = [];

    foreach ($checks as $table => $column) {

        /*
         * Decide whether this column stores the College ID
         * or the College Name.
         */
        $value = $column === 'college_id'
            ? $collegeId
            : $collegeName;

        $count = DB::table($table)
            ->where($column, $value)
            ->count();

        if ($count > 0) {
            $usedIn[$table . '.' . $column] = $count;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | College is being used somewhere
    |--------------------------------------------------------------------------
    */
    if (!empty($usedIn)) {

        return redirect()
            ->route('colleges.index')
            ->with('delete_error', [
                'college' => $collegeName,
                'records' => $usedIn,
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | No records found - safe to delete
    |--------------------------------------------------------------------------
    */
    $college->delete();

    return redirect()
        ->route('colleges.index')
        ->with('success', 'College deleted successfully.');
}
    public function destroy12(College $college)
{
    $collegeId = $college->id;

    $checks = [
        'College Call Logs' => DB::table('college_call_logs'),
        'College Email Recipients' => DB::table('college_email_recipients'),
        'Events' => DB::table('events'),
        'External Attendance Links' => DB::table('external_attendance_links'),
        'External Attendance Submissions' => DB::table('external_attendance_submissions'),
        'External Attendance Tests' => DB::table('external_attendance_tests'),
        'Hard Data' => DB::table('hard_data'),
        'HODs' => DB::table('hods'),
        'MOUs' => DB::table('mous'),
        'Student Pending Registration' => DB::table('student_pending_registration'),
        'Student Tests' => DB::table('student_tests'),
        'Tests' => DB::table('tests'),
        'Test Links' => DB::table('test_links'),
        'Workshops' => DB::table('workshops'),
    ];

    $usedIn = [];

    foreach ($checks as $module => $table) {
        $count = $table->where('college_id', $collegeId)->count();

        if ($count > 0) {
            $usedIn[$module] = $count;
        }
    }

    if (!empty($usedIn)) {
        $message = 'Cannot delete "' . $college->college_name . '". Records exist in: ';

        $details = [];

        foreach ($usedIn as $module => $count) {
            $details[] = $module . ' (' . $count . ')';
        }

        $message .= implode(', ', $details);

        return redirect()
            ->route('colleges.index')
            ->with('error', $message);
    }

    $college->delete();

    return redirect()
        ->route('colleges.index')
        ->with('success', 'College deleted successfully.');
}

    public function destroy_26aug(College $college)
    {
        $college->delete();

        return redirect()->route('colleges.index')
                         ->with('success', 'College deleted successfully.');
    }

    // public function exportExcel()
    // {
    //     return Excel::download(new CollegesExport, 'colleges.xlsx');
    // }

    // public function exportExcel(Request $request)
    // {
    //     return Excel::download(
    //         new CollegesExport(
    //             $request->state_name,
    //             $request->district_name,
    //             $request->student_filter,
    //             $request->college_type,
    //             $request->offer_training,
    //             $request->call_status
    //         ),
    //         'colleges.xlsx'
    //     );
    // }

    // use App\Models\College;
public function exportExcel(Request $request)
{
    $fileNameParts = ['colleges'];

    if (!empty($request->state_name)) {
        $fileNameParts[] = $request->state_name;
    }

    if (!empty($request->district_name)) {
        $fileNameParts[] = $request->district_name;
    }

    // College Type
    if ($request->college_type !== null && $request->college_type !== '') {

        $types = College::TYPES;

        if (isset($types[$request->college_type])) {
            $fileNameParts[] = strtolower($types[$request->college_type]);
        }
    }

    // Student Filter
    if (!empty($request->student_filter)) {
        $fileNameParts[] = $request->student_filter;
    }

    // Training
    if ($request->offer_training !== null && $request->offer_training !== '') {

        $fileNameParts[] = $request->offer_training == 1
            ? 'training_yes'
            : 'training_no';
    }

    // Training In
    if (!empty($request->training_in)) {
        $fileNameParts[] = strtolower($request->training_in);
    }

    // Training Times
    if ($request->training_in_year !== null && $request->training_in_year !== '') {
        $fileNameParts[] = 'training_' . $request->training_in_year . '_times';
    }

    // Whom to Connect
    if (!empty($request->connected_to)) {
        $fileNameParts[] = strtolower($request->connected_to);
    }

    // Reference By
    if (!empty($request->reference_by)) {
        $fileNameParts[] = 'ref_' . $request->reference_by;
    }

    // Contact Person
    if (!empty($request->contact_person)) {
        $fileNameParts[] = 'contact_' . $request->contact_person;
    }

    // Departments
    // Departments
    if ($request->has('departments') && is_array($request->departments)) {

        $departments = array_values(array_filter(
            $request->departments,
            fn ($department) => $department !== null && $department !== ''
        ));

        if (!empty($departments)) {
            // Only show number of selected departments in filename
            $fileNameParts[] = 'departments_' . count($departments);
        }
    }

    // Clean unwanted values
    $fileNameParts = array_filter($fileNameParts, function ($value) {
        return $value !== null
            && $value !== ''
            && $value !== 'undefined';
    });

    $fileName = implode('_', $fileNameParts);

    $fileName = preg_replace(
        '/[^A-Za-z0-9_\-]/',
        '_',
        $fileName
    );

    $fileName .= '_' . now()->format('d_F') . '.xlsx';

    return Excel::download(
        new CollegesExport(
            $request->only([
                'state_name',
                'district_name',
                'student_filter',
                'college_type',
                'offer_training',
                'call_status',
                'is_important',
                'ownership_type',
                'connection_type',

                // New filters
                'departments',
                'training_in',
                'training_in_year',
                'connected_to',
                'reference_by',
                'contact_person',
                'status',
                'training_21_days', 
                'training_45_days', 
                'training_6_months',
            ])
        ),
        $fileName
    );
}
public function exportExcel21sep(Request $request)
{
    $fileNameParts = ['colleges'];

    if (!empty($request->state_name)) {
        $fileNameParts[] = $request->state_name;
    }

    if (!empty($request->district_name)) {
        $fileNameParts[] = $request->district_name;
    }

    // ✅ FIX: college_type key → label
    if ($request->college_type !== null && $request->college_type !== '') {
        $types = College::TYPES;

        if (isset($types[$request->college_type])) {
            $fileNameParts[] = strtolower($types[$request->college_type]);
        }
    }

    if (!empty($request->student_filter)) {
        $fileNameParts[] = $request->student_filter;
    }

    // training fix
    if ($request->offer_training !== null && $request->offer_training !== '') {
        $fileNameParts[] = $request->offer_training == 1 
            ? 'training_yes' 
            : 'training_no';
    }

    // clean unwanted values
    $fileNameParts = array_filter($fileNameParts, function ($value) {
        return $value !== null && $value !== '' && $value !== 'undefined';
    });

    $fileName = implode('_', $fileNameParts);
    $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $fileName);

    $fileName .= '_' . now()->format('d_F') . '.xlsx';

    return Excel::download(
        new CollegesExport(
            $request->only([
                'state_name',
                'district_name',
                'student_filter',
                'college_type',
                'offer_training',
                'call_status',
                'is_important',
                'ownership_type',
                'connection_type',
                'department',
            ])
        ),
        $fileName
    );
}

    public function importColleges(Request $request, CollegeResolver $resolver)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);

        $import = new CollegesImport($resolver);

        Excel::import($import, $request->file('file'));

        return back()->with([
            'success' => "College import completed.",
            'import_summary' => [
                'created' => $import->created,
                'skipped' => $import->skipped,
            ],
            'skipped_colleges' => $import->skippedRows,
        ]);
    }

    public function showImport()
    {
        return view('colleges.import');
    }

    public function students(College $college)
    {
        $students = Student::with('sessionData')
            ->where('college_name', $college->id)
            ->orderBy('student_name', 'asc')
            ->get()
            ->map(function ($student) {
                return [
                    'student_name' => $student->student_name,
                    'sno'          => $student->sno,
                    'session_id'   => $student->session,
                    'session_name' => optional($student->sessionData)->session_name,
                ];
            });

        return response()->json($students);
    }

    public function exportStudentsExcel(College $college)
    {
        return Excel::download(
            new \App\Exports\CollegeStudentsExport($college->id),
            $college->college_name . '_students.xlsx'
        );
    }

    public function toggleStatus(Request $request, $id)
    {
        $college = College::findOrFail($id);
        $college->call_status = $request->status;
        $college->save();

        return response()->json(['success' => true]);
    }

    public function bulkUpdate2(Request $request)
    {
        dd( $request);

    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids'               => 'required|string',
            'college_type'      => 'nullable',
            'offer_training'    => 'nullable',
            'training_in_year'  => 'nullable',
            'is_important'      => 'nullable|in:0,1',
            'ownership_type'    => 'nullable|in:0,1',
            'connection_type'   => 'nullable|in:0,1',
            'departments'       => 'nullable|array',
            'departments.*'     => 'string',
            'status' => 'nullable|in:active,closed,blocked',
            'training_in'       => 'nullable|in:Degree,Diploma,Both',
            'connected_to'      => 'nullable|in:HOD,TPO,Principal,other,Both',
            'reference_by'      => 'nullable|string|max:255',
            'training_months'   => 'nullable',
        ]);

        $ids = array_filter(explode(',', $request->ids));

        if (empty($ids)) {
            return redirect()
                ->route('colleges.index')
                ->with('error', 'Please select at least one college.');
        }

        $updateData = [];

        // College Type
        if ($request->college_type !== null && $request->college_type !== '') {
            $updateData['college_type'] = $request->college_type;
        }

        // Offer Training
        if ($request->offer_training !== null && $request->offer_training !== '') {
            $updateData['offer_training'] = $request->offer_training;
        }

        // Training Per Year
        if ($request->training_in_year !== null && $request->training_in_year !== '') {
            $updateData['training_in_year'] = $request->training_in_year;
        }

        // Important College
        if ($request->is_important !== null && $request->is_important !== '') {
            $updateData['is_important'] = $request->is_important;
        }

        // Government / Private
        if ($request->ownership_type !== null && $request->ownership_type !== '') {
            $updateData['ownership_type'] = $request->ownership_type;
        }

        // Old / New Connection
        if ($request->connection_type !== null && $request->connection_type !== '') {
            $updateData['connection_type'] = $request->connection_type;
        }

        // Departments
        if ($request->has('departments')) {
            $updateData['departments'] = $request->departments ?? [];
        }

        // Status
        if ($request->status !== null && $request->status !== '') {
            $updateData['status'] = $request->status;
        }

         // Training In
        if ($request->training_in !== null && $request->training_in !== '') {
            $updateData['training_in'] = $request->training_in;
        }

        // Training Months
        if ($request->training_months !== null && $request->training_months !== '') {
            $updateData['training_months'] = $request->training_months;
        }
        
         // Whom to Connect
        if ($request->connected_to !== null && $request->connected_to !== '') {
            $updateData['connected_to'] = $request->connected_to;
        }

        // Reference By
        if ($request->reference_by !== null && $request->reference_by !== '') {
            $updateData['reference_by'] = $request->reference_by;
        }

        if (empty($updateData)) {
            return redirect()
                ->route('colleges.index')
                ->with('error', 'Please select at least one field to update.');
        }

        College::whereIn('id', $ids)->update($updateData);

        return redirect()
            ->route('colleges.index')
            ->with('success', count($ids) . ' college(s) updated successfully.');
    }
}
