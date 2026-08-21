<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Rules\NotBlockedNumber;

class VisitorRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:visitor_record.view')->only(['index']);
    }

    /**
     * Visitor Records List
     */
    public function index(Request $request)
    {
        $query = VisitorRecord::query();

        // Visitor Name
        if ($request->filled('visitor_name')) {
            $query->where('visitor_name', 'like', '%' . $request->visitor_name . '%');
        }

        // Mobile
        if ($request->filled('mobile')) {
            $query->where('mobile', 'like', '%' . $request->mobile . '%');
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Visit Date
        if ($request->filled('visit_date')) {
            $query->whereDate('visit_date', $request->visit_date);
        }

        $visitors = $query
            ->latest('id')
            ->get();

        return view('visitor_records.index', compact('visitors'));
    }

    /**
     * Edit Visitor
     */
    public function edit($id)
    {
        $visitor = VisitorRecord::findOrFail($id);

        return view('visitor_records.edit', compact('visitor'));
    }

    /**
     * Update Visitor
     */
    public function update(Request $request, $id)
    {
        $visitor = VisitorRecord::findOrFail($id);

        $validated = $request->validate([
            'visitor_name' => 'required|string|max:150',
            'mobile' => ['required', 'string', new NotBlockedNumber],
            'email' => 'nullable|email|max:150',
            'organization' => 'nullable|string|max:200',
            'purpose' => 'required|string|max:255',
            'person_to_meet' => 'nullable|string|max:150',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_time' => 'nullable',
            'message' => 'nullable|string',
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'visited',
                    'cancelled',
                ]),
            ],
            'admin_notes' => 'nullable|string',
        ]);

        $visitor->update($validated);

        return redirect()
            ->route('admin.visitor_records.index')
            ->with('success', 'Visitor record updated successfully!');
    }

    public function show($id)
    {
        $visitor = VisitorRecord::findOrFail($id);

        return view('visitor_records.show', compact('visitor'));
    }

    /**
     * Delete Visitor
     */
    public function destroy($id)
    {
        $visitor = VisitorRecord::findOrFail($id);

        $visitor->delete();

        return redirect()
            ->route('admin.visitor_records.index')
            ->with('success', 'Visitor record deleted successfully!');
    }
}