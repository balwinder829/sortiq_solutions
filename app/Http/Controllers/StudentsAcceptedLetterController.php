<?php

namespace App\Http\Controllers;

use App\Models\StudentsAcceptedLetter;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentsAcceptedLetterController extends Controller
{
    protected string $permissionPrefix = 'accepted_letters';

    protected array $permissionMap = [
        'index'   => 'view',
        'show'    => 'view',
        'create'  => 'create',
        'store'   => 'create',
        'edit'    => 'edit',
        'update'  => 'edit',
        'destroy' => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /**
     * Display listing
     */
    public function index()
    {
        $letters = StudentsAcceptedLetter::latest()->get();

        return view('students_accepted_letters.index', compact('letters'));
    }

    /**
     * Show create form
     */
    public function create()
    {   
        $activeSessionNo = session('admin_session_id');
        $students = Student::where('session', $activeSessionNo)
        ->orderBy('student_name', 'asc')
        ->get();
        return view('students_accepted_letters.create', compact('students'));
    }

    /**
     * Store new record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255',
            'email'      => 'nullable|email',
            'file'       => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('file')->store('accepted_letters', 'public');

        StudentsAcceptedLetter::create([
            'student_id' => $validated['student_id'],
            'email'      => $validated['email'],
            'file_path'  => $path,
        ]);

        return redirect()
            ->route('student-accepted-letters.index')
            ->with('success', 'Accepted letter uploaded successfully');
    }

    /**
     * Show single record
     */
    public function show(StudentsAcceptedLetter $letter)
    {
        return view('students_accepted_letters.show', compact('letter'));
    }

    /**
     * Show edit form
     */
    public function edit(StudentsAcceptedLetter $letter)
    {   
         $activeSessionNo = session('admin_session_id');
            $students = Student::where('session', $activeSessionNo)
            ->orderBy('student_name', 'asc')
            ->get();
        return view('students_accepted_letters.edit', compact('letter','students'));
    }

    /**
     * Update record
     */
    public function update(Request $request, StudentsAcceptedLetter $letter)
    {
        $validated = $request->validate([
            'student_id' => 'required|string|max:255',
            'email'      => 'required|email',
            'file'       => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'student_id' => $validated['student_id'],
            'email'      => $validated['email'],
        ];

        if ($request->hasFile('file')) {

            // delete old file if exists
            if ($letter->file_path) {
                Storage::disk('public')->delete($letter->file_path);
            }

            $data['file_path'] = $request->file('file')
                ->store('accepted_letters', 'public');
        }

        $letter->update($data);

        return redirect()
            ->route('student-accepted-letters.index')
            ->with('success', 'Accepted letter updated successfully');
    }

    /**
     * Delete record
     */
    public function destroy(StudentsAcceptedLetter $letter)
    {
        if ($letter->file_path) {
            Storage::disk('public')->delete($letter->file_path);
        }

        $letter->delete();

        return redirect()
            ->route('student-accepted-letters.index')
            ->with('success', 'Accepted letter deleted successfully');
    }

    /**
     * Download file
     */
    public function download(StudentsAcceptedLetter $letter)
    {
        return Storage::disk('public')->download($letter->file_path);
    }
}
