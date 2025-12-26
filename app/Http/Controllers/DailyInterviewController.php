<?php

// app/Http/Controllers/DailyInterviewController.php

namespace App\Http\Controllers;

use App\Models\DailyInterview;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DailyInterviewController extends Controller
{
    protected function validationRules($ignoreId = null)
    {
        return [
            'candidate_name' => 'required|string|max:255',
            'mobile_no' => 'nullable|string|max:20',
            'technology' => 'nullable|string|max:100',
            
            'notice_period' => 'nullable|string|max:50',
            'exp_ctc' => 'nullable|string|max:50',
            'current_ctc' => 'nullable|string|max:50',
            
            'availability_datetime' => 'nullable|date',
            'joining_date' => 'nullable|date',
            
            'interview_status' => ['required', Rule::in(['Scheduled', 'Completed', 'No Show', 'Rejected', 'Offered'])],
            'interviewer_name' => 'nullable|string|max:100',
            'interview_type' => ['required', Rule::in(['Screening', 'Technical 1', 'Technical 2', 'HR Round', 'Final Round'])],
        ];
    }
     
    public function index(Request $request)
    {
        $query = DailyInterview::query();
        
        // 1. Standard Filters (Technology, Status, Type)
        if ($request->filled('technology')) {
            $query->where('technology', $request->input('technology'));
        }
        if ($request->filled('status')) {
            $query->where('interview_status', $request->input('status'));
        }
        if ($request->filled('type')) {
            $query->where('interview_type', $request->input('type'));
        }

        // 2. Date Range Filters (New Logic)
        
        $today = now()->startOfDay();
        
        // Filter by specific Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
            
            $query->whereBetween('availability_datetime', [$start_date, $end_date]);
            
        } elseif ($request->filled('date_filter')) {
            // Filter by Today/Tomorrow/Upcoming Quick Select
            switch ($request->input('date_filter')) {
                case 'today':
                    $query->whereDate('availability_datetime', $today);
                    break;
                
                case 'tomorrow':
                    $tomorrow = now()->addDay()->startOfDay();
                    $query->whereDate('availability_datetime', $tomorrow);
                    break;
                    
                case 'upcoming':
                    // Interviews scheduled from the start of today onwards
                    $query->where('availability_datetime', '>=', $today);
                    $query->orderBy('availability_datetime', 'asc');
                    break;
            }
        } else {
            // Default behavior: Show upcoming interviews if no filter is set
            $query->where('availability_datetime', '>=', $today);
            $query->orderBy('availability_datetime', 'asc');
        }

        $interviews = $query->latest('id')->paginate(100)->appends($request->except('page'));
        
        // Data for Filters
        $available_tech = DailyInterview::select('technology')->distinct()->pluck('technology')->filter()->sort();
        $available_status = ['Scheduled', 'Completed', 'No Show', 'Rejected', 'Offered'];
        $available_type = ['Screening', 'Technical 1', 'Technical 2', 'HR Round', 'Final Round'];
        $date_options = [
            'today' => 'Today',
            'tomorrow' => 'Tomorrow',
            'upcoming' => 'Upcoming (Default)'
        ];

        return view('daily_interviews.index', compact('interviews', 'available_tech', 'available_status', 'available_type', 'date_options'));
    }

    public function create()
    {
        return view('daily_interviews.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());
        DailyInterview::create($validated);
        return redirect()->route('daily-interviews.index')->with('success', 'Interview scheduled successfully!');
    }

    public function show(DailyInterview $dailyInterview)
    {
        return view('daily_interviews.show', ['interview' => $dailyInterview]);
    }

    public function edit(DailyInterview $dailyInterview)
    {
        return view('daily_interviews.edit', ['interview' => $dailyInterview]);
    }

    public function update(Request $request, DailyInterview $dailyInterview)
    {
        $validated = $request->validate($this->validationRules($dailyInterview->id));
        $dailyInterview->update($validated);
        return redirect()->route('daily-interviews.index')->with('success', 'Interview updated successfully!');
    }

    public function destroy(DailyInterview $dailyInterview)
    {
        $dailyInterview->delete();
        return redirect()->route('daily-interviews.index')->with('success', 'Interview record deleted.');
    }
}