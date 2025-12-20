<?php

namespace App\Http\Controllers;

use App\Models\PartTimeJob;
use Illuminate\Http\Request;

class PartTimeJobController extends Controller
{
   public function index(Request $request)
	{
	    $jobs = PartTimeJob::query();

	    if ($request->job_type) {
	        $jobs->where('job_type', 'like', '%' . $request->job_type . '%');
	    }

	    if ($request->shift) {
	        $jobs->where('shift', 'like', '%' . $request->shift . '%');
	    }

	    if ($request->location) {
	        $jobs->where('location', 'like', '%' . $request->location . '%');
	    }

	    if ($request->status) {
	        $jobs->where('status', $request->status);
	    }

	    $jobs = $jobs->orderBy('id', 'desc')->get();

	    return view('part_time_jobs.index', compact('jobs'));
	}


    public function create()
    {
        return view('part_time_jobs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        PartTimeJob::create($request->all());

        return redirect()->route('part-time-jobs.index')
            ->with('success', 'Part-time job added successfully');
    }

    /* ✅ SHOW METHOD (IMPORTANT) */
    public function show($id)
    {
        $job = PartTimeJob::findOrFail($id);
        return view('part_time_jobs.show', compact('job'));
    }

    public function edit($id)
    {
        $job = PartTimeJob::findOrFail($id);
        return view('part_time_jobs.edit', compact('job'));
    }

    public function update(Request $request, $id)
    {
        $job = PartTimeJob::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $job->update($request->all());

        return redirect()->route('part-time-jobs.index')
            ->with('success', 'Part-time job updated successfully');
    }

    public function destroy($id)
    {
        PartTimeJob::findOrFail($id)->delete();

        return redirect()->route('part-time-jobs.index')
            ->with('success', 'Part-time job deleted successfully');
    }
}
