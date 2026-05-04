<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\CallCampaign;
use App\Models\CallLog;
use Illuminate\Support\Facades\DB;
use App\Http\DataTables\DataTablesServerSide;
use App\Models\State;
use App\Models\District;

class CollegeCallController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:college_calls.view')->only(['index','logs','view']);
        $this->middleware('permission:college_calls.create')->only(['create','store','retryByCollege']);
    }
    /*
    |--------------------------------------------------
    | 1. INDEX (DATATABLE)
    |--------------------------------------------------
    */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $activeSessionId = session('admin_session_id');

            $query = College::with(['state','district']);

            /*
            |---------------- FILTERS ----------------
            */

            if ($request->college_id) {
                $query->where('id', $request->college_id);
            }

            if ($request->state_id) {
                $query->where('state_id', $request->state_id);
            }

            if ($request->district_id) {
                $query->where('district_id', $request->district_id);
            }

            if ($request->call_status == 'connected') {
                $query->whereHas('callLogs', function ($q) use ($activeSessionId) {
                    $q->where('session_id', $activeSessionId)
                      ->where('status', 'connected');
                });
            }

            if ($request->call_status == 'not_called') {
                $query->whereDoesntHave('callLogs', function ($q) use ($activeSessionId) {
                    $q->where('session_id', $activeSessionId);
                });
            }

            if ($request->call_status == 'failed') {
                $query->whereHas('callLogs', function ($q) use ($activeSessionId) {
                    $q->where('session_id', $activeSessionId)
                      ->where('status', '!=', 'connected');
                });
            }

            /*
            |---------------- DATE FILTER ----------------
            */

            if (!$request->range) {

                if ($request->date_from) {
                    $query->whereHas('callLogs', function ($q) use ($request, $activeSessionId) {
                        $q->where('session_id', $activeSessionId)
                          ->whereDate('called_at', '>=', $request->date_from);
                    });
                }

                if ($request->date_to) {
                    $query->whereHas('callLogs', function ($q) use ($request, $activeSessionId) {
                        $q->where('session_id', $activeSessionId)
                          ->whereDate('called_at', '<=', $request->date_to);
                    });
                }
            }

            if ($request->range) {

                $query->whereHas('callLogs', function ($q) use ($request, $activeSessionId) {

                    $q->where('session_id', $activeSessionId);

                    switch ($request->range) {

                        case 'today':
                            $q->whereDate('called_at', today());
                            break;

                        case 'yesterday':
                            $q->whereDate('called_at', today()->subDay());
                            break;

                         case 'current_week_past':
                            $q->whereBetween('sent_at', [
                                now()->startOfWeek(),
                                now()
                            ]);
                            break;

                        case 'last_week':
                            $q->whereBetween('sent_at', [
                                now()->subWeek()->startOfWeek(),
                                now()->subWeek()->endOfWeek()
                            ]);
                            break;

                        case 'last_month':
                            $q->whereBetween('sent_at', [
                                now()->subMonth()->startOfMonth(),
                                now()->subMonth()->endOfMonth()
                            ]);
                            break;

                        case 'last_30_days':
                            $q->whereBetween('sent_at', [
                                now()->subDays(30),
                                now()
                            ]);
                            break;
                    }
                });
            }

            return DataTablesServerSide::response($request, $query, [
                'orderable'  => ['id'],
                'searchable' => ['college_name'],
            ], function ($college) use ($activeSessionId) {

                /*
                |------------- CALL COUNT -------------
                */
                // $callCount = CallLog::where('college_id', $college->id)
                //     ->where('session_id', $activeSessionId)
                //     ->count();

                $callCount = CallLog::where('college_id', $college->id)
                    ->where('session_id', $activeSessionId)
                    ->count();

                $callCountHtml = '<a href="'.route('admin.college-calls.logs',$college->id).'" target="_blank" class="fw-bold">'.$callCount.'</a>';

                /*
                |------------- CALLED TO -------------
                */
                $types = CallLog::where('college_id', $college->id)
                    ->where('session_id', $activeSessionId)
                    ->select('type')
                    ->distinct()
                    ->pluck('type')
                    ->toArray();

                if (empty($types)) {
                    $calledTo = '-';
                } elseif (count($types) == 2) {
                    $calledTo = '<span class="badge bg-info">Both</span>';
                } else {
                    $calledTo = '<span class="badge bg-primary">'.strtoupper($types[0]).'</span>';
                }

                /*
                |------------- STATUS -------------
                */
                $latest = CallLog::where('college_id', $college->id)
                    ->where('session_id', $activeSessionId)
                    ->latest()
                    ->first();

                if (!$latest) {
                    $status = '<span class="badge bg-secondary">Not Called</span>';
                } else {
                    $color = $latest->status == 'connected' ? 'success' : 'danger';
                    $status = '<span class="badge bg-'.$color.'">'.ucfirst($latest->status).'</span>';
                }

                /*
                |------------- ACTIONS -------------
                */
                $failedCount = CallLog::where('college_id', $college->id)
                    ->where('session_id', $activeSessionId)
                    ->where('status', '!=', 'connected')
                    ->count();

                $totalCount = CallLog::where('college_id', $college->id)
                    ->where('session_id', $activeSessionId)
                    ->count();

                if ($failedCount > 0) {
                    $actions = '<button class="btn btn-warning btn-sm retry-single" data-id="'.$college->id.'">Retry</button>';
                } elseif ($totalCount == 0) {
                    $actions = '<button class="btn btn-primary btn-sm call-single" data-id="'.$college->id.'">Call</button>';
                } else {
                    $actions = '<button class="btn btn-primary btn-sm call-single" data-id="'.$college->id.'">Call</button>';
                }

                $checkbox = '<input type="checkbox" class="record_checkbox" value="'.$college->id.'">';

                return [
                    $checkbox,
                    $college->id,
                    e($college->full_name),
                    $callCountHtml,
                    $calledTo,
                    $status,
                    $actions
                ];
            });
        }

        $colleges = College::orderBy('college_name')->get();
        $states = State::orderBy('name')->get();

        $districtsGrouped = District::with('state')
            ->orderBy('name')
            ->get()
            ->groupBy('state_id');

        return view('college_calls.index', compact('colleges','states','districtsGrouped'));
    }

    /*
    |--------------------------------------------------
    | 2. STORE SELECTION
    |--------------------------------------------------
    */
    public function storeSelection(Request $request)
    {
        $ids = $request->ids ?? [];

        if (empty($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No colleges selected'
            ]);
        }

        session(['selected_call_colleges' => $ids]);

        return response()->json(['status' => true]);
    }

    /*
    |--------------------------------------------------
    | 3. CREATE PAGE
    |--------------------------------------------------
    */
    public function create()
    {
        $selectedIds = session('selected_call_colleges', []);

        if (empty($selectedIds)) {
            return redirect()->route('admin.college-calls.index')
                ->with('error', 'Please select colleges first');
        }

        $colleges = College::with('hod')
            ->whereIn('id', $selectedIds)
            ->get();

        return view('college_calls.create', compact('colleges'));
    }

    /*
    |--------------------------------------------------
    | 4. STORE (LOG CALLS)
    |--------------------------------------------------
    */
    public function store(Request $request)
    {
        $activeSessionId = session('admin_session_id');
        $selectedColleges = session('selected_call_colleges', []);

        if (empty($selectedColleges)) {
            return back()->with('error', 'No colleges selected');
        }

        DB::beginTransaction();

        try {

            $campaign = CallCampaign::create([
                'purpose' => $request->purpose,
                'notes' => $request->notes,
                'session_id' => $activeSessionId,
                'total_calls' => 0,
                'connected_count' => 0,
                'not_connected_count' => 0,
            ]);

            $total = 0;

            $colleges = College::with('hod')->whereIn('id', $selectedColleges)->get();

            foreach ($colleges as $college) {

                if (!$college->hod) continue;

                $hod = $college->hod;

                $types = $request->types[$college->id] ?? [];

                foreach ($types as $type) {

                    if ($type == 'hod') {
                        $number = $hod->hod_contact;
                        $name = $hod->hod_name;
                    } else {
                        $number = $hod->tpo_contact;
                        $name = $hod->tpo_name;
                    }

                    if (!$number) continue;

                    CallLog::create([
                        'campaign_id' => $campaign->id,
                        'session_id' => $activeSessionId,
                        'college_id' => $college->id,
                        'hod_id' => $hod->id,
                        'contact_number' => $number,
                        'recipient_name' => $name,
                        'type' => $type,
                        'status' => 'connected', // ✅ default as requested
                        'called_at' => now(),
                        'notes' => $request->notes,
                    ]);

                    $total++;
                }
            }

            $campaign->update([
                'total_calls' => $total,
                'connected_count' => $total
            ]);

            DB::commit();

            session()->forget('selected_call_colleges');

            return redirect()->route('admin.college-calls.index')
                ->with('success', 'Calls logged successfully!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------
    | 5. RETRY (COLLEGE)
    |--------------------------------------------------
    */
    public function retryByCollege(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id'
        ]);

        $activeSessionId = session('admin_session_id');

        session(['selected_call_colleges' => [$request->college_id]]);

        return response()->json([
            'status' => true,
            'redirect' => route('admin.college-calls.create')
        ]);
    }

    /*
    |--------------------------------------------------
    | CALL LOGS (PER COLLEGE)
    |--------------------------------------------------
    */
    public function logs($collegeId)
    {
        $activeSessionId = session('admin_session_id');

        $college = College::findOrFail($collegeId);

        $logs = CallLog::with(['campaign'])
            ->where('college_id', $collegeId)
            ->where('session_id', $activeSessionId)
            ->latest()
            ->paginate(20);

        return view('college_calls.logs', compact('logs','college'));
    }


    /*
    |--------------------------------------------------
    | VIEW DETAIL
    |--------------------------------------------------
    */
    public function view($id)
    {
        $log = CallLog::with(['college','hod','campaign'])->findOrFail($id);

        return view('college_calls.view', compact('log'));
    }
}