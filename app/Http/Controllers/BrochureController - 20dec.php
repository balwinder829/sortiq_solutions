<?php
namespace App\Http\Controllers;

use App\Models\Brochure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrochureController extends Controller
{
    /* -------------------------
       INDEX
    ------------------------- */
    public function index(Request $request)
    {
        $query = Brochure::query();
        $now = now();

        if ($request->filter === 'active') {
            $query->where('is_active', true)
                ->where(function($q) use ($now){
                    $q->whereNull('start_at')->orWhere('start_at','<=',$now);
                })
                ->where(function($q) use ($now){
                    $q->whereNull('end_at')->orWhere('end_at','>=',$now);
                });
        }
        elseif ($request->filter === 'upcoming') {
            $query->whereNotNull('start_at')->where('start_at','>', $now);
        }
        elseif ($request->filter === 'expired') {
            $query->whereNotNull('end_at')->where('end_at','<', $now);
        }

        $brochures = $query->latest()->paginate(20);

        return view('brochures.index', compact('brochures'));
    }

    /* -------------------------
       CREATE VIEW
    ------------------------- */
    public function create()
    {
        return view('brochures.create');
    }

    /* -------------------------
       STORE
    ------------------------- */
   public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'required',
        'description' => 'nullable',
        'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:51200',
        'is_active' => 'nullable|boolean',
        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
    ]);

    // File exists? If not -> upload issue BEFORE controller
    if (!$request->hasFile('file')) {
        return back()
            ->withErrors(['file' => 'File failed to upload. Increase php.ini limits.'])
            ->withInput();
    }

    $file = $request->file('file');

    // Ensure tmp file is readable
    if (!is_readable($file->getPathname())) {
        return back()
            ->withErrors(['file' => 'Upload error: temp file not readable.'])
            ->withInput();
    }

    $mime = $file->getClientMimeType();
    // Determine type
    $ext = strtolower($file->getClientOriginalExtension());
    $fileType = $ext === 'pdf' ? 'pdf' : 'image';

    // Storage in secure folder
    $filename = time() . '-' . Str::random(8) . '.' . $ext;
    $securePath = storage_path('app/secure-brochures');

    // Ensure folder exists
    if (!is_dir($securePath)) {
        mkdir($securePath, 0777, true);
    }

    // MOVE FILE (safe version)
    $file->move($securePath, $filename);

    // Save DB
    Brochure::create([
        'title' => $data['title'],
        'description' => $data['description'],
        'file_name' => $filename,
        'file_type' => $fileType,
        'mime' => $mime,
        'is_active' => $data['is_active'] ?? true,
        'start_at' => $data['start_at'],
        'end_at' => $data['end_at'],
    ]);

    return redirect()
        ->route('brochures.index')
        ->with('success', 'Brochure created successfully.');
}

    public function qstore(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:51200',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $fileType = $ext === 'pdf' ? 'pdf' : 'image';

        $filename = time().'-'.Str::random(8).'.'.$ext;
        $securePath = storage_path('app/secure-brochures');

        if (!file_exists($securePath)) {
            mkdir($securePath, 0777, true);
        }

        $file->move($securePath, $filename);

        Brochure::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'file_name' => $filename,
            'file_type' => $fileType,
            'mime' => $mime,
            'is_active' => $data['is_active'] ?? true,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
        ]);

        return redirect()->route('brochures.index')
                         ->with('success', 'Brochure created successfully');
    }

    /* -------------------------
       EDIT VIEW
    ------------------------- */
    public function edit(Brochure $brochure)
    {
        return view('brochures.edit', compact('brochure'));
    }

    /* -------------------------
       UPDATE
    ------------------------- */
    public function update(Request $request, Brochure $brochure)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:51200',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        // replace file if new uploaded
        if ($request->hasFile('file')) {
            $oldPath = storage_path('app/secure-brochures/'.$brochure->file_name);
            if (file_exists($oldPath)) unlink($oldPath);

            $file = $request->file('file');
            $mime = $file->getClientMimeType();
            $ext = strtolower($file->getClientOriginalExtension());
            $fileType = $ext === 'pdf' ? 'pdf' : 'image';
            $filename = time().'-'.Str::random(8).'.'.$ext;

            $file->move(storage_path('app/secure-brochures'), $filename);

            $brochure->file_name = $filename;
            $brochure->file_type = $fileType;
            $brochure->mime = $mime;
        }

        $brochure->update($data);

        return redirect()->route('brochures.index')
                         ->with('success', 'Brochure updated');
    }

    /* -------------------------
       SECURE VIEW (preview)
    ------------------------- */
    public function preview($token)
    {       
        
        // $b = Brochure::where('share_token', $token)->where('is_active', 1)->firstOrFail();
        $b = Brochure::where('share_token', $token)
    ->where('is_active', 1)
    ->where(function ($q) {
        $now = now();
        $q->whereNull('start_at')
          ->orWhere('start_at', '<=', $now);
    })
    ->where(function ($q) {
        $now = now();
        $q->whereNull('end_at')
          ->orWhere('end_at', '>=', $now);
    })->firstOrFail();

        $path = storage_path('app/secure-brochures/'.$b->file_name);
        if (!file_exists($path)) abort(404);

        return response()->file($path);
    }

    public function view(Brochure $brochure)
    {
        $path = storage_path('app/secure-brochures/'.$brochure->file_name);

        if (!file_exists($path)) abort(404);

        return response()->file($path, [
            'Content-Type' => $brochure->mime
        ]);
    }

    /* -------------------------
       SECURE DOWNLOAD
    ------------------------- */
    public function download_old($token)
    {
        $b = Brochure::where('share_token', $token)->firstOrFail();

        $path = storage_path('app/secure-brochures/'.$b->file_name);
        if (!file_exists($path)) abort(404);

        $b->increment('download_count');

        return response()->download($path, $b->file_name);
    }

    public function download(Brochure $brochure)
    {
        // dd($brochure);
        $path = storage_path('app/secure-brochures/'.$brochure->file_name);

        if (!file_exists($path)) {
            abort(404, "File not found.");
        }

        return response()->download($path, $brochure->file_name, [
            'Content-Type' => $brochure->mime
        ]);
    }

//     public function publicShow($token)
// {
//     // dd($token);
//     $brochure = Brochure::where('share_token', $token)->firstOrFail();

//     return view('brochures.public-show', compact('brochure'));
// }


    /* -------------------------
       DELETE
    ------------------------- */
    public function destroy(Brochure $brochure)
    {
        $path = storage_path('app/secure-brochures/'.$brochure->file_name);
        if (file_exists($path)) unlink($path);

        $brochure->delete();

        return redirect()->route('brochures.index')->with('success', 'Deleted');
    }
}
