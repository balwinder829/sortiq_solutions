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

class CollegeController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:colleges.view')->only('index');
        $this->middleware('permission:colleges.create')->only(['create','store']);
        $this->middleware('permission:colleges.edit')->only(['edit','update']);
        $this->middleware('permission:colleges.delete')->only('destroy');
        $this->middleware('permission:colleges.import')->only('showImport');
    }

    public function index()
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


public function store(Request $request)
{
    $data = $request->validate([
        'college_name'         => 'required|string|max:255',
        'college_display_name' => 'nullable|string|max:255',
        'state_id'             => 'required|exists:states,id',
        'district_id'          => 'required|exists:districts,id',
    ]);

    /** ---------------------------------
     * Centralized college handling
     * --------------------------------- */
     $college = app(CollegeResolver::class)->resolveWithLocation(
        $data['college_name'],
        $data['state_id'],
        $data['district_id'],
        $data['college_display_name'] // 👈 user-entered
    );

    return redirect()
        ->route('colleges.index')
        ->with('success', 'College saved successfully.');
}

public function store15dec(Request $request)
{
    $data = $request->validate([
        'college_name' => 'required|string|max:255',
        'college_display_name' => 'required|string|max:255',
        'state_id' => 'required|exists:states,id',
        'district_id' => 'required|exists:districts,id',
    ]);

    $cleanName = College::clean($request->college_name);

    // Check duplicate using clean_name
    // if (College::where('clean_name', $cleanName)->withTrashed()->exists()) {
    //     return back()->withErrors(['college_name' => 'College already exists.'])->withInput();
    // }

    $exists = College::withTrashed()
        ->where('clean_name', $cleanName)
        ->where('state_id', $data['state_id'])
        ->where('district_id', $data['district_id'])
        ->exists();

    if ($exists) {
        return back()
            ->withErrors([
                'college_name' => 'This college already exists in the selected state and district.'
            ])
            ->withInput();
    }

    College::create([
        'college_name' => $request->college_name,
        'college_display_name' => $request->college_display_name,
        'clean_name'   => $cleanName,
        'slug'         => null, // model will generate slug
        'state_id'  => $request->state_id,
        'district_id'  => $request->district_id,
    ]);

    return redirect()->route('colleges.index')->with('success', 'College added successfully.');
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

    // public function update(Request $request, College $college)
    // {
    //     $request->validate([
    //         'college_name' => 'required|string|max:255',
    //     ]);

    //     $college->update($request->all());

    //     return redirect()->route('colleges.index')->with('success', 'College updated successfully.');
    // }
public function update(Request $request, $id)
{
    $data = $request->validate([
        'college_name'          => 'required|string|max:255',
        'college_display_name'  => 'required|string|max:255',
        'state_id'              => 'required|exists:states,id',
        'district_id'           => 'required|exists:districts,id',
    ]);

    $college = College::findOrFail($id);

    /** Resolve clean_name + slug from service */
    $resolver  = app(\App\Services\CollegeResolver::class);
    $cleanName = $resolver->makeCleanName($data['college_name']);
    $slug      = $resolver->makeSlug($data['college_name']);

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
        'slug'                 => $slug,
        'state_id'             => $data['state_id'],
        'district_id'          => $data['district_id'],
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

    public function exportExcel()
    {
        return Excel::download(new CollegesExport, 'colleges.xlsx');
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
}
