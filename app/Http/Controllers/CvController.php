<?php

// app/Http/Controllers/CvController.php (UPDATED)

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CvController extends Controller
{
    // ... (index method remains the same for filtering)
    public function index(Request $request)
    {
        $query = Cv::query();
        
        // 1. Filter by Technology
        if ($request->filled('technology')) {
            $query->where('technology', $request->input('technology'));
        }

        // 2. Filter by Experience Status
        if ($request->filled('status')) {
            $query->where('experience_status', $request->input('status'));
        }
        
        // 3. Filter by Hiring Status (New Filter)
        if ($request->filled('hiring_status')) {
            $query->where('hiring_status', $request->input('hiring_status'));
        }

        // $cvs = $query->latest()->get();
        $cvs = $query->latest()->paginate(100);
        
        $available_tech = Cv::select('technology')->distinct()->pluck('technology')->sort();
        $available_status = ['Fresher', 'Experienced'];
        $available_hiring_status = ['Looking', 'Not Looking', 'Open to Offers']; // For the new filter

        return view('cvs.index', compact('cvs', 'available_tech', 'available_status', 'available_hiring_status'));
    }

    public function create()
    {
        return view('cvs.create');
    }

    protected function validationRules($ignoreId = null)
    {
        return [
            'employee_name' => 'required|string|max:255',
            'technology' => 'required|string|max:100',
            'experience_status' => ['required', Rule::in(['Fresher', 'Experienced'])],
            
            // New Validation Rules
            'experience_years' => 'nullable|integer|min:0|max:99',
            'current_job_status' => 'nullable|string|max:150',
            'hiring_status' => ['required', Rule::in(['Looking', 'Not Looking', 'Open to Offers'])],
            'phone_number' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:100',
            'last_updated_at' => 'nullable|date',
            'file_name' => 'required|string|max:255',
            
            'gdrive_link' => 'required|url|max:2048', 
        ];
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate($this->validationRules());
        
        // Ensure Fresher CVs have 0 experience years
        if ($validated['experience_status'] === 'Fresher') {
            $validated['experience_years'] = 0;
        }

        Cv::create($validated);

        return redirect()->route('cvs.index')->with('success', 'CV record added successfully!');
    }

    public function show(Cv $cv)
    {
        return redirect()->away($cv->gdrive_link);
    }

    public function edit(Cv $cv)
    {
        return view('cvs.edit', compact('cv'));
    }

    public function update(Request $request, Cv $cv)
    {
        $validated = $request->validate($this->validationRules($cv->id));
        
        // Ensure Fresher CVs have 0 experience years
        if ($validated['experience_status'] === 'Fresher') {
            $validated['experience_years'] = 0;
        }

        $cv->update($validated);

        return redirect()->route('cvs.index')->with('success', 'CV record updated successfully!');
    }
    
    public function destroy(Cv $cv)
    {
        $cv->delete();
        return redirect()->route('cvs.index')->with('success', 'CV record moved to trash.');
    }
}