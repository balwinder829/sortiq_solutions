<?php

// app/Http/Controllers/AcceptedLetterController.php
namespace App\Http\Controllers;

use App\Models\AcceptedLetter;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class AcceptedLetterController extends Controller
{
    protected string $permissionPrefix = 'accepted_letters';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }
    // public function index()
    // {
    //     $letters = AcceptedLetter::latest()->get();
    //     return view('accepted_letters.index', compact('letters'));
    // }

    public function index()
    {
        $letters = AcceptedLetter::with('employee')
            ->latest()
            ->get();

        return view('accepted_letters.index', compact('letters'));
    }

    public function create()
    {
        $employees = Employee::orderBy('emp_name', 'asc')
            ->select('id', 'emp_name', 'emp_code')
            ->get();

        return view('accepted_letters.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'emp_id'  => 'required',
             'employee_id' => 'required|exists:employees,id',
            'file'  => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('file')->store('accepted_letters', 'public');

        AcceptedLetter::create([
            'employee_id' => $request->employee_id,
            'file_path' => $path,
        ]);

        return redirect()->route('accepted-letters.index')
            ->with('success', 'Accepted letter uploaded successfully');
    }

    public function edit(AcceptedLetter $accepted_letter)
    {
        $employees = Employee::orderBy('emp_name', 'asc')
        ->select('id', 'emp_name', 'emp_code')
        ->get();

        return view('accepted_letters.edit', compact('accepted_letter', 'employees'));
    }

    public function update(Request $request, AcceptedLetter $accepted_letter)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'file'  => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['employee_id']);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($accepted_letter->file_path);
            $data['file_path'] = $request->file('file')
                ->store('accepted_letters', 'public');
        }

        $accepted_letter->update($data);

        return redirect()->route('accepted-letters.index')
            ->with('success', 'Accepted letter updated successfully');
    }

    public function download(AcceptedLetter $accepted_letter)
    {
        return Storage::disk('public')->download($accepted_letter->file_path);
    }

    public function destroy(AcceptedLetter $accepted_letter)
    {
        // Delete file from storage
        if ($accepted_letter->file_path && 
            \Storage::disk('public')->exists($accepted_letter->file_path)) {

            \Storage::disk('public')->delete($accepted_letter->file_path);
        }

        $accepted_letter->delete();

        return redirect()->route('accepted-letters.index')
            ->with('success', 'Accepted letter deleted successfully');
    }
}
