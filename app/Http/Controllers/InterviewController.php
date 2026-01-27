<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\InterviewRound;
use App\Models\Technology;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterviewController extends Controller
{
	public function index(Request $request)
{
    $query = Interview::with(['rounds.technologies']);

    // 🔹 Experience filter
    if ($request->filled('experience')) {
        $query->where('candidate_experience', 'like', '%' . $request->experience . '%');
    }

    // 🔹 Technology + Rating filter
    if ($request->filled('technology_id')) {
        $query->whereHas('rounds.technologies', function ($q) use ($request) {
            $q->where('technologies.id', $request->technology_id);

            if ($request->filled('rating')) {
                $q->where('interview_round_technology.rating', '>=', $request->rating);
            }
        });
    }
    // 🔹 Only rating filter (overall round rating)
    elseif ($request->filled('rating')) {
        $query->whereHas('rounds', function ($q) use ($request) {
            $q->where('rating', '>=', $request->rating);
        });
    }

    $interviews = $query->latest()->get();

    $technologies = Technology::orderBy('name')->get();

    return view('interviews.index', compact('interviews', 'technologies'));
}

    public function index1()
    {
        $interviews = Interview::latest()->get();
        return view('interviews.index', compact('interviews'));
    }

    public function create()
    {
        $technologies = Technology::where('is_active', 1)->get();
        return view('interviews.create', compact('technologies'));
    }

    public function store(Request $request)
{
    $request->validate([
        'candidate_name'   => 'required|string',
        'interviewer_name' => 'required|string',
        'candidate_contact' => 'required|string',
        'candidate_email' => 'required|string',
        'round_type'       => 'required|in:hr,technical,machine',
    ]);

    // 1️⃣ Create Interview (main record)
    $interview = Interview::create([
        'candidate_name'        => $request->candidate_name,
        'candidate_experience'  => $request->candidate_experience,
        'candidate_email'       => $request->candidate_email,
        'candidate_contact'     => $request->candidate_contact,
        'interviewer_name'      => $request->interviewer_name,
        'interview_date'        => $request->interview_date,
        'final_result'          => $request->final_result,
        'final_remarks'         => $request->final_remarks,
    ]);

    // 2️⃣ Create Interview Round (ONLY ONE ROUND)
    $round = InterviewRound::create([
        'interview_id' => $interview->id,
        'round_type'   => $request->round_type,
        'rating'       => $request->rounds['general']['rating'] ?? null,
        'remarks'      => $request->rounds['general']['remarks'] ?? null,
    ]);

    // 3️⃣ Attach technologies WITH rating (only for technical/machine)
    if (
        in_array($request->round_type, ['technical', 'machine']) &&
        !empty($request->rounds['technical']['technologies'])
    ) {
        foreach ($request->rounds['technical']['technologies'] as $techId => $techData) {

            if (!isset($techData['selected'])) {
                continue;
            }

            $round->technologies()->attach($techId, [
                'rating' => $techData['rating'] ?? null,
            ]);
        }
    }

    return redirect()->route('interviews.index')
        ->with('success', 'Interview added successfully');
}


    public function show(Interview $interview)
    {
        $interview->load('rounds.technologies');
        return view('interviews.show', compact('interview'));
    }

    public function edit(Interview $interview)
    {
        $interview->load('rounds.technologies');
        $technologies = Technology::where('is_active', 1)->get();

        return view('interviews.edit', compact('interview', 'technologies'));
    }

    public function update(Request $request, Interview $interview)
	{
	    $request->validate([
	        'candidate_name'   => 'required',
	        'interviewer_name' => 'required',
	        'candidate_email'  => 'nullable|email',
	    ]);

	    $interview->update($request->only([
	        'candidate_name',
	        'candidate_experience',
	        'candidate_contact',
	        'candidate_email',
	        'interviewer_name',
	        'interview_date',
	        'final_result',
	    ]));

	    return redirect()
	        ->route('interviews.show', $interview)
	        ->with('success', 'Candidate updated successfully');
	}


    public function update2(Request $request, Interview $interview)
	{
	    $request->validate([
	        'candidate_name'   => 'required',
	        'interviewer_name' => 'required',
	    ]);

	    $interview->update([
	        'candidate_name'       => $request->candidate_name,
	        'candidate_experience' => $request->candidate_experience,
	        'candidate_contact'    => $request->candidate_contact,
	        'candidate_email'      => $request->candidate_email,
	        'interviewer_name'     => $request->interviewer_name,
	        'interview_date'       => $request->interview_date,
	        'final_result'         => $request->final_result,
	        'final_remarks'        => $request->final_remarks,
	    ]);

	    return redirect()
	        ->route('interviews.show', $interview)
	        ->with('success', 'Interview updated successfully');
	}


    public function update_1(Request $request, Interview $interview)
	{
	    // ✅ Validation
	    $request->validate([
	        'candidate_name'   => 'required|string',
	        'interviewer_name' => 'required|string',
	        'round_type'       => 'required|in:hr,technical,machine',
	        'candidate_email'  => 'nullable|email',
	        'rounds.general.rating' => 'nullable|integer|min:0|max:10',
	    ]);

	    // 1️⃣ Update Interview (candidate + interviewer details)
	    $interview->update([
	        'candidate_name'        => $request->candidate_name,
	        'candidate_experience'  => $request->candidate_experience,
	        'candidate_contact'     => $request->candidate_contact,
	        'candidate_email'       => $request->candidate_email,
	        'interviewer_name'      => $request->interviewer_name,
	        'interview_date'        => $request->interview_date,
	        'final_result'          => $request->final_result,
	        'final_remarks'         => $request->final_remarks,
	    ]);

	    // 2️⃣ Remove old rounds (and pivot data automatically)
	    // $interview->rounds()->delete();

	    // 3️⃣ Create new round (ONLY ONE ROUND)
	    $round = InterviewRound::create([
	        'interview_id' => $interview->id,
	        'round_type'   => $request->round_type,
	        'rating'       => $request->rounds['general']['rating'] ?? null,
	        'remarks'      => $request->rounds['general']['remarks'] ?? null,
	    ]);

	    // 4️⃣ Attach technologies with rating (ONLY for technical/machine)
	    if (
	        in_array($request->round_type, ['technical', 'machine']) &&
	        !empty($request->rounds['technical']['technologies'])
	    ) {
	        foreach ($request->rounds['technical']['technologies'] as $techId => $techData) {

	            // selected flag decides save/remove
	            if (empty($techData['selected'])) {
	                continue;
	            }

	            $round->technologies()->attach($techId, [
	                'rating' => $techData['rating'] ?? null,
	            ]);
	        }
	    }

	    return redirect()->route('interviews.index')
	        ->with('success', 'Interview updated successfully');
	}


    public function destroy(Interview $interview)
    {
        $interview->delete();
        return back()->with('success', 'Interview deleted');
    }

    public function storeRound(Request $request, Interview $interview)
	{
	    $request->validate([
	        'round_type' => 'required|in:hr,technical,machine',
	        'round_date' => 'nullable|date',
	        'rounds.general.rating' => 'nullable|integer|min:0|max:10',
	    ]);

	    // 1️⃣ Create NEW round (do NOT touch old rounds)
	    $round = InterviewRound::create([
	        'interview_id' => $interview->id,
	        'round_type'   => $request->round_type,
	        'round_date'   => $request->round_date,
	        'rating'       => $request->rounds['general']['rating'] ?? null,
	        'remarks'      => $request->rounds['general']['remarks'] ?? null,
	    ]);

	    // 2️⃣ Attach technologies if required
	    if (
	        in_array($request->round_type, ['technical', 'machine']) &&
	        !empty($request->rounds['technical']['technologies'])
	    ) {
	        foreach ($request->rounds['technical']['technologies'] as $techId => $techData) {

	            if (empty($techData['selected'])) {
	                continue;
	            }

	            $round->technologies()->attach($techId, [
	                'rating' => $techData['rating'] ?? null,
	            ]);
	        }
	    }

	    return redirect()
	    ->route('interviews.show', $interview)
	    ->with('success', 'Interview round added successfully');
	}

	public function createRound(Interview $interview)
	{
	    // Load technologies for tech/machine rounds
	    $technologies = Technology::orderBy('name')->get();

	    return view('interviews.rounds.create', compact('interview', 'technologies'));
	}


}
