<?php

// app/Http/Controllers/AcceptedLetterController.php
namespace App\Http\Controllers;

use App\Models\AcceptedLetter;
use Illuminate\Http\Request;
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
    public function index()
    {
        $letters = AcceptedLetter::latest()->get();
        return view('accepted_letters.index', compact('letters'));
    }

    public function create()
    {
        return view('accepted_letters.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'emp_code'  => 'nullable|string|max:255',
            'email' => 'required|email',
            'file'  => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('file')->store('accepted_letters', 'public');

        AcceptedLetter::create([
            'name'      => $request->name,
            'emp_code'      => $request->emp_code,
            'email'     => $request->email,
            'file_path' => $path,
        ]);

        return redirect()->route('accepted-letters.index')
            ->with('success', 'Accepted letter uploaded successfully');
    }

    public function edit(AcceptedLetter $accepted_letter)
    {
        return view('accepted_letters.edit', compact('accepted_letter'));
    }

    public function update(Request $request, AcceptedLetter $accepted_letter)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'emp_code'  => 'nullable|string|max:255',
            'email' => 'required|email',
            'file'  => 'nullable|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'emp_code']);

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
}
