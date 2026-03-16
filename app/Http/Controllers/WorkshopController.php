<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\College;
use App\Models\StudentSession;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;
use App\Exports\WorkshopsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\State;
use App\Models\District;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('permission:workshop.view')->only('index');
        $this->middleware('permission:workshop.create')->only(['create','store']);
        $this->middleware('permission:workshop.edit')->only(['edit','update']);
        $this->middleware('permission:workshop.delete')->only('destroy');
        // $this->middleware('permission:colleges.import')->only('showImport');
    }

    public function index(Request $request)
    {
        $colleges = College::orderBy('college_name','asc')->get();
        $states = State::orderBy('name')->get();

        $districtsGrouped = District::with('state')
            ->orderBy('name')
            ->get()
            ->groupBy('state_id');

        return view('workshops.index', compact('colleges','states','districtsGrouped'));
    }

    public function index2(Request $request)
    {
        $colleges = College::orderBy('college_name','asc')->get();
        $states = State::orderBy('name')->get();
        $districts = District::orderBy('name')->get();

        return view('workshops.index', compact('colleges','states','districts'));
    }

    public function data(Request $request)
    {   
        $activeSessionNo = session('admin_session_id');
        // load college relation
        $query = Workshop::with('college')
                    ->where('session', $activeSessionNo);

        if($request->college_id){
            $query->where('college_id',$request->college_id);
        }

        // STATE
        if ($request->state_id) {
            $query->whereHas('college', function ($q) use ($request) {
                $q->where('state_id', $request->state_id);
            });
        }

        // DISTRICT
        if ($request->district_id) {
            $query->whereHas('college', function ($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        }

        // DEGREE / DIPLOMA
        if ($request->college_type !== null && $request->college_type !== '') {
            $query->whereHas('college', function ($q) use ($request) {
                $q->where('college_type', $request->college_type);
            });
        }

        if($request->status){
            $query->where('status',$request->status);
        }

        if($request->date){
            $query->whereDate('date',$request->date);
        }

        // 👇 custom date filters
        if($request->range == 'today'){
            $query->whereDate('date', today());
        }

        if($request->range == 'upcoming'){
            $query->whereDate('date','>', today());
        }

        if($request->range == 'past'){
            $query->whereDate('date','<', today());
        }

        $query->orderBy('date', 'desc');
        
        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','title','date','status'], // FIXED
            'searchable' => ['title','status'],            
        ], function ($workshop, $index, $start) {

            // status badge
            $statusBadge = '<span class="badge bg-secondary">'.e(ucfirst($workshop->status)).'</span>';

            // actions
            $actions  = '<a href="' . route('workshops.edit', $workshop->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
            $actions .= '<form action="' . route('workshops.destroy', $workshop->id) . '" method="POST" style="display:inline-block;">'
                        . csrf_field()
                        . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?">
                            <i class="fa fa-trash"></i>
                        </button>
                        </form>';

            return [
                $workshop->id,                                // ID
                e($workshop->title),                          // Title (using name column)
                e(optional($workshop->college)->FullName),   // College
                $workshop->date?->format('d M Y'),
                $statusBadge,                                // Status
                e($workshop->type),                          // Title (using name column)
                $actions,                                    // Actions
            ];
        });
    }

    public function dataold(Request $request)
    {
        
        $query = Workshop::query();

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id', 'title'],
            'searchable' => ['title'],
        ], function ($course, $index, $start) {
            $studentLink = '<a href="' . route('common_filtered_student', ['technology' => $course->id]) . '" class="text-decoration-none"><span class="badge bg-success"></span></a>';
            $actions = '<a href="' . route('courses.edit', $course->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
            $actions .= '<form action="' . route('courses.destroy', $course->id) . '" method="POST" style="display:inline-block;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?"><i class="fa fa-trash"></i></button></form>';
            return [
                $course->id,
                e($course->title),
                $studentLink,
                $actions,
            ];
        });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $colleges = College::orderBy('college_name','asc')->get();
        return view('workshops.create', compact('colleges','activeSession'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_id'      => 'required|integer',
            'status'          => 'required|string',
            'duration'          => 'required',
            'tp_hod_no'       => 'required|string',
            'type'       => 'required',
            'event_type'       => 'required',
            // 'college_type'       => 'required',
            'description'       => 'nullable',
            'name'            => 'required|string|max:255',
            'title'           => 'required|string|max:255',
            'date'            => 'required|date',
        ]);

        $activeSessionNo = session('admin_session_id');
        $validated['session'] = $activeSessionNo;
        Workshop::create($validated);

        return redirect()
            ->route('workshops.index')
            ->with('success','Workshop created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop)
    {
        return view('workshops.show', compact('workshop'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $workshop = Workshop::findOrFail($id);
         $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $colleges = College::orderBy('college_name','asc')->get();

        return view('workshops.edit', compact('workshop','colleges','activeSession'));
    }


    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
    {
        $workshop = Workshop::findOrFail($id);

        $validated = $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'status'     => 'required|in:done,decided,meeting,hold,cancel',
            'duration'          => 'required',
            'tp_hod_no'  => 'required|digits:10',
            'name'       => 'required|string|max:255',
            'type'       => 'required',
            'event_type'       => 'required',
            'description'       => 'nullable',
            'title'      => 'required|string|max:255',
            'date'       => 'required|date',
            // 'college_type'       => 'required',
        ]);

        $activeSessionNo = session('admin_session_id');
        $validated['session'] = $activeSessionNo;

        $workshop->update($validated);

        return redirect()
            ->route('workshops.index')
            ->with('success','Workshop updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop)
    {
        $workshop->delete();

        return redirect()
            ->route('workshops.index')
            ->with('success','Workshop deleted successfully');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new WorkshopsExport($request),
            'workshops.xlsx'
        );
    }
}
