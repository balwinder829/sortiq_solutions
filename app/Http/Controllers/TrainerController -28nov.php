<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\StudentSession;
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index()
    {
         $sessions = StudentSession::all();
    $colleges = \App\Models\College::all();
    $courses  = \App\Models\Course::all();
    $batches  = \App\Models\Batch::all();
    $users    = \App\Models\User::all();
    $departments = \App\Models\Department::all();
        $trainers = Trainer::latest()->paginate(10);
        return view('trainers.index', compact('trainers', 'sessions', 'colleges','batches', 'courses', 'departments'));
    }

    public function create()
    {
        $sessions = StudentSession::all();
    $colleges = \App\Models\College::all();
    $courses  = \App\Models\Course::all();
    $batches  = \App\Models\Batch::all();
    $users    = \App\Models\User::all();
    $department = \App\Models\Department::all();
        return view('trainers.create', compact('sessions', 'colleges','batches', 'courses', 'department'));

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trainer_name' => 'required|string|max:100',
            'gender'       => 'required|in:male,female',
            'phone'        => 'required|string|max:20',
            'email'        => 'required|email|unique:trainers,email',
            'technology'   => 'required',
            // 'department'   => 'required|string|max:50',
        ]);

        Trainer::create($validated);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer added successfully!');
    }

public function edit(Trainer $trainer)
{
    $sessions = StudentSession::all();
    $colleges = \App\Models\College::all();
    $courses  = \App\Models\Course::all();
    $batches  = \App\Models\Batch::all();
    $department = \App\Models\Department::all();

    return view('trainers.edit', compact(
        'trainer', 'sessions', 'colleges', 'batches', 'courses', 'department'
    ));
}


    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'trainer_name' => 'required|string|max:100',
            'gender'       => 'required|in:male,female',
            'phone'        => 'required|string|max:20',
            'email'        => 'required|email|unique:trainers,email,' . $trainer->id,
            'technology'   => 'required|max:100',
            // 'department'   => 'required|string|max:50',
        ]);

        $trainer->update($validated);

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer updated successfully!');
    }

    public function destroy(Trainer $trainer)
    {
        $trainer->delete();
        return redirect()->route('trainers.index')
            ->with('success', 'Trainer deleted successfully!');
    }

    public function batchesAjax($id)
    {
        $trainer = Trainer::with('batches')->findOrFail($id);

        return view('trainers.batches-table', compact('trainer'));
    }

    public function importForm()
    {
        return view('trainers.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            $importer = new \App\Imports\TrainersImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $request->file('file'));

            $errors = [];

            // Duplicate phone errors
            if (!empty($importer->duplicatePhones)) {
                foreach ($importer->duplicatePhones as $msg) {
                    $errors[] = $msg;
                }
            }

            if (!empty($errors)) {
                return back()
                    ->with('success', 'Trainers imported with some warnings.')
                    ->withErrors($errors);
            }

            return back()->with('success', 'Trainers imported successfully!');

        } catch (\Throwable $e) {
            return back()->withErrors([
                'Import failed: ' . $e->getMessage()
            ]);
        }
    }


}
