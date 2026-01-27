<?php

namespace App\Http\Controllers;

use App\Models\InterviewRound;
use App\Models\Technology;
use Illuminate\Http\Request;

class InterviewRoundController extends Controller
{
    public function edit(InterviewRound $round)
    {
        $round->load('technologies');
        $technologies = Technology::orderBy('name')->get();

        return view('interviews.rounds.edit', compact('round', 'technologies'));
    }

    public function update(Request $request, InterviewRound $round)
    {
        $request->validate([
            'round_type' => 'required|in:hr,technical,machine',
            'rating'     => 'nullable|integer|min:0|max:10',
        ]);

        // Update round details
        $round->update([
            'round_type' => $request->round_type,
            'round_date' => $request->round_date,
            'rating'     => $request->rating,
            'remarks'    => $request->remarks,
        ]);

        // Reset technologies safely
        $round->technologies()->detach();

        if (
            in_array($request->round_type, ['technical', 'machine']) &&
            $request->filled('technologies')
        ) {
            foreach ($request->technologies as $techId => $tech) {
                if (!empty($tech['selected'])) {
                    $round->technologies()->attach($techId, [
                        'rating' => $tech['rating'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('interviews.show', $round->interview_id)
            ->with('success', 'Interview round updated successfully');
    }
}
