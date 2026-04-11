<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\State;
use App\Models\District;
use App\Models\Student;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\CollegesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\CollegeResolver;
use App\Imports\CollegesImport;
use App\Exports\CollegeStudentsExport;
use Illuminate\Validation\Rule;


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

    public function index14fev()
    {
        // $colleges = College::all();
        // $colleges = College::with(['state','district'])->get();

        $activeSessionId = session('admin_session_id');

         $colleges = College::with(['state', 'district'])
        ->withCount([
            'students as students_count' => function ($query) use ($activeSessionId) {
                $query->where('session', $activeSessionId);
            }
        ])
        ->orderBy('college_name', 'asc')
        ->get();
        
        // $colleges = College::with(['state','district'])
        //     ->withCount('students')
        //     ->orderBy('college_name', 'asc')
        //     ->get();
        $states = State::orderBy('name')->get();

$districtsGrouped = District::select('districts.id','districts.name','districts.state_id','states.name as state_name')
    ->join('states', 'states.id', '=', 'districts.state_id')
    ->orderBy('districts.name')
    ->get()
    ->groupBy('state_id');

        return view('colleges.index', compact('colleges', 'states', 'districtsGrouped'));
    }

    public function create()
    {   
        $states = State::orderBy('name')->get();
        return view('colleges.create', compact('states'));
        // return view('colleges.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'college_name' => 'required|string|max:255',
    //     ]);

    //     College::create($request->all());

    //     return redirect()->route('colleges.index')
    //                      ->with('success', 'College created successfully.');
    // }

 public function index()
    {
        $states = State::orderBy('name')->get();

        $districtsGrouped = District::select('districts.id', 'districts.name', 'districts.state_id', 'states.name as state_name')
            ->join('states', 'states.id', '=', 'districts.state_id')
            ->orderBy('districts.name')
            ->get()
            ->groupBy('state_id');

        return view('colleges.index', compact('states', 'districtsGrouped'));
    }

    /**
     * Server-side DataTables: returns only the current page of rows (no full load).
     */
    public function data(Request $request)
    {
        $activeSessionId = session('admin_session_id');

        $query = College::query()
            ->with(['state', 'district'])
            ->withCount([
                'students as students_count' => function ($q) use ($activeSessionId) {
                    $q->where('session', $activeSessionId);
                }
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

        // 👇 ADD HERE
        if ($request->filled('student_filter')) {

            if ($request->student_filter == 'zero') {
                $query->having('students_count', '=', 0);
            }

            if ($request->student_filter == 'more') {
                $query->having('students_count', '>', 0);
            }
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

        $orderCol = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';

        $orderable = [
            0 => 'id',
            1 => 'college_name',
            2 => 'state',
            3 => 'district',
            4 => 'students_count',
            5 => 'college_type',
            6 => 'offer_training',
            7 => 'training_in_year'
        ];

        $orderField = $orderable[$orderCol] ?? null;

        /*
        Priority:
        1. student_filter (asc/desc)
        2. DataTables column sorting
        3. Default = students_count DESC (High → Low)
        */

        if ($request->filled('student_filter') && in_array($request->student_filter, ['asc', 'desc'])) {

            $query->orderBy('students_count', $request->student_filter);

        } elseif ($orderField) {

            if ($orderField === 'college_name' || $orderField === 'id') {
                $query->orderBy('colleges.' . $orderField, $orderDir);

            } elseif ($orderField === 'students_count') {
                $query->orderBy('students_count', $orderDir);

            } elseif ($orderField === 'state') {
                $query->orderByRaw('(SELECT name FROM states WHERE states.id = colleges.state_id) ' . $orderDir);

            } elseif ($orderField === 'district') {
                $query->orderByRaw('(SELECT name FROM districts WHERE districts.id = colleges.district_id) ' . $orderDir);
            }elseif ($orderField === 'college_type') {
                $query->orderBy('colleges.college_type', $orderDir);

            } elseif ($orderField === 'offer_training') {
                $query->orderBy('colleges.offer_training', $orderDir);
            }
            elseif ($orderField === 'training_in_year') {
                $query->orderBy('colleges.training_in_year', $orderDir);
            }

        } else {

            // 🔥 DEFAULT ORDER WHEN PAGE LOADS
            $query->orderBy('students_count', 'desc');
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
                $rowNum,
                $college->college_name,
                $college->state->name ?? '-',
                $college->district->name ?? '-',
                '<a href="' . route('common_filtered_student', ['college_name' => $college->id]) . '" class="text-decoration-none"><span class="badge bg-success">' . $college->students_count . '</span></a>',
                $collegeType,
                $training,
                $college->training_in_year,
                
                '<div class="mb-2">' .
                    '<a href="' . route('colleges.edit', $college->id) . '" class="btn btn-sm" data-bs-toggle="tooltip" title="Edit"><i class="fa fa-edit"></i></a> ' .
                    '<form action="' . route('colleges.destroy', $college->id) . '" method="POST" style="display:inline;">' .
                    csrf_field() . method_field('DELETE') .
                    '<button type="submit" class="btn btn-sm" data-swal-confirm="Are you sure?" data-bs-toggle="tooltip" title="Delete"><i class="fa fa-trash"></i></button>' .
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
                             ->where('district_id', $request->district_id);
            }),
        ],
        'college_display_name' => 'nullable|string|max:255',
        'state_id'             => 'required|exists:states,id',
        'district_id'          => 'required|exists:districts,id',
        'college_type'          => 'required',
        'offer_training'          => 'required',
        'training_in_year'          => 'required',
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
         return view('colleges.edit', compact('college','states','districts'));
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
                                 ->where('district_id', $request->district_id);
                })
                ->ignore($id),
        ],
        'college_display_name'  => 'required|string|max:255',
        'college_short_name'  => 'required|string|max:255',
        'state_id'              => 'required|exists:states,id',
        'district_id'           => 'required|exists:districts,id',
        'college_type'          => 'required',
        'offer_training'          => 'required',
        'training_in_year'          => 'required',
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
    $exists = College::withTrashed()
        ->where('clean_name', $cleanName)
        ->where('state_id', $data['state_id'])
        ->where('district_id', $data['district_id'])
        ->where('id', '!=', $college->id)
        ->exists();

    if ($exists) {
        return back()
            ->withErrors([
                'college_name' =>
                    'This college already exists in the selected state and district.'
            ])
            ->withInput();
    }

    /** Update record */
    $college->update([
        'college_name'         => $data['college_name'],
        'college_display_name' => $data['college_display_name'], // user-entered
        'clean_name'           => $cleanName,
        'college_short_name'           => $shortname,
        'slug'                 => $slug,
        'state_id'             => $data['state_id'],
        'district_id'          => $data['district_id'],
        'college_type'          => $data['college_type'],
        'offer_training'          => $data['offer_training'],
        'training_in_year'          => $data['training_in_year'],
    ]);
// dd($college);
    return redirect()
        ->route('colleges.index')
        ->with('success', 'College updated successfully.');
}

    public function update15jan(Request $request, $id)
{
     $data = $request->validate([
        'college_name' => 'required|string|max:255',
        'college_display_name' => 'required|string|max:255',
        'state_id' => 'required|exists:states,id',
        'district_id' => 'required|exists:districts,id',
    ]);

    $college = College::findOrFail($id);

    $cleanName = College::clean($request->college_name);

    // Check duplicates except current ID
    // $exists = College::where('clean_name', $cleanName)
    //                  ->where('id', '!=', $id)
    //                  ->withTrashed()
    //                  ->exists();

    // if ($exists) {
    //     return back()->withErrors(['college_name' => 'College already exists.'])->withInput();
    // }

     $exists = College::withTrashed()
        ->where('clean_name', $cleanName)
        ->where('state_id', $data['state_id'])
        ->where('district_id', $data['district_id'])
        ->where('id', '!=', $college->id)
        ->exists();

    if ($exists) {
        return back()
            ->withErrors([
                'college_name' => 'This college already exists in the selected state and district.'
            ])
            ->withInput();
    }


    // Reset slug to regenerate if college name changed
    $college->update([
        'college_name' => $request->college_name,
        'college_display_name' => $request->college_display_name,
        'clean_name'   => $cleanName,
        'slug'         => null,
        'state_id'  => $request->state_id,
        'district_id'  => $request->district_id,
    ]);

    return redirect()->route('colleges.index')->with('success', 'College updated successfully.');
}




    public function destroy(College $college)
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

    $fileName .= '_' . now()->format('Ymd_His') . '.xlsx';

    return Excel::download(
        new CollegesExport(
            $request->state_name,
            $request->district_name,
            $request->student_filter,
            $request->college_type,
            $request->offer_training,
            $request->call_status
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
}
