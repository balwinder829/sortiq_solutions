<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    /* -------------------------
       INDEX
    ------------------------- */
    public function index(Request $request)
    {
        $query = CompanyProfile::query();
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

        $companyProfiles = $query->latest()->paginate(20);

        return view('company_profile.index', compact('companyProfiles'));
    }

    /* -------------------------
       CREATE VIEW
    ------------------------- */
    public function create()
    {
        return view('company_profile.create');
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

        if (!$request->hasFile('file')) {
            return back()->withErrors(['file' => 'File upload failed.'])->withInput();
        }

        $file = $request->file('file');

        if (!is_readable($file->getPathname())) {
            return back()->withErrors(['file' => 'Upload error: temp file unreadable'])->withInput();
        }

        $mime = $file->getClientMimeType();
        $ext = strtolower($file->getClientOriginalExtension());
        $fileType = $ext === 'pdf' ? 'pdf' : 'image';

        $filename = time() . '-' . Str::random(8) . '.' . $ext;
        $securePath = storage_path('app/secure-company-profiles');

        if (!is_dir($securePath)) {
            mkdir($securePath, 0777, true);
        }

        $file->move($securePath, $filename);

        CompanyProfile::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'file_name' => $filename,
            'file_type' => $fileType,
            'mime' => $mime,
            'is_active' => $data['is_active'] ?? true,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
        ]);

        return redirect()->route('company_profile.index')
                         ->with('success', 'Company Profile created successfully.');
    }

    /* -------------------------
       EDIT VIEW
    ------------------------- */
    public function edit(CompanyProfile $companyProfile)
    {
        return view('company_profile.edit', compact('companyProfile'));
    }

    /* -------------------------
       UPDATE
    ------------------------- */
    public function update(Request $request, CompanyProfile $companyProfile)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:51200',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        if ($request->hasFile('file')) {
            $oldPath = storage_path('app/secure-company-profiles/'.$companyProfile->file_name);
            if (file_exists($oldPath)) unlink($oldPath);

            $file = $request->file('file');
            $mime = $file->getClientMimeType();
            $ext = strtolower($file->getClientOriginalExtension());
            $fileType = $ext === 'pdf' ? 'pdf' : 'image';
            $filename = time().'-'.Str::random(8).'.'.$ext;

            $file->move(storage_path('app/secure-company-profiles'), $filename);

            $companyProfile->file_name = $filename;
            $companyProfile->file_type = $fileType;
            $companyProfile->mime = $mime;
        }

        $companyProfile->update($data);

        return redirect()->route('company_profile.index')
                         ->with('success', 'Company Profile updated successfully.');
    }

    /* -------------------------
       PREVIEW
    ------------------------- */
    public function preview_old($token)
    {
        $profile = CompanyProfile::where('share_token', $token)
    ->where('is_active', 1)
    ->where(function ($q) {
        $now = now();

        // Start date allowed if null OR already started
        $q->whereNull('start_at')
          ->orWhere('start_at', '<=', $now);
    })
    ->where(function ($q) {
        $now = now();

        // End date allowed if null OR still before end
        $q->whereNull('end_at')
          ->orWhere('end_at', '>=', $now);
    })
    ->first();
    if (!$profile) {
    return response()->view('errors.company_profile_blocked', [], 403);
}


        $path = storage_path('app/secure-company-profiles/'.$profile->file_name);
        if (!file_exists($path)) abort(404);

        return response()->file($path);
    }

    public function view(CompanyProfile $companyProfile)
    {
        $path = storage_path('app/secure-company-profiles/'.$companyProfile->file_name);
        if (!file_exists($path)) abort(404);

        return response()->file($path, [
            'Content-Type' => $companyProfile->mime
        ]);
    }

    /* -------------------------
       DOWNLOAD
    ------------------------- */
    public function download($token)
    {
        $companyProfile = CompanyProfile::where('share_token', $token)
            ->publiclyVisible()
            ->firstOrFail();

        $path = storage_path('app/secure-company-profiles/'.$companyProfile->file_name);
        if (!file_exists($path)) abort(404);

        $companyProfile->increment('download_count');

        return response()->download($path, $companyProfile->file_name, [
            'Content-Type' => $companyProfile->mime
        ]);
    }


     public function preview($token)
    {
        // dd('hw');
        $profile = CompanyProfile::where('share_token', $token)
            ->publiclyVisible()
            ->firstOrFail();

        $path = storage_path('app/secure-company-profiles/' . $profile->file_name);
        abort_if(!file_exists($path), 404);
         // if (!$profile) {
         //        return response()->view('errors.company_profile_blocked', [], 403);
         //    }
        $profile->increment('download_count');
        return response()->file($path, [
            'Content-Type' => $profile->mime
        ]);
    }

    /* -------------------------
       DELETE
    ------------------------- */
    public function destroy(CompanyProfile $companyProfile)
    {
        $path = storage_path('app/secure-company-profiles/'.$companyProfile->file_name);
        if (file_exists($path)) unlink($path);

        $companyProfile->delete();

        return redirect()->route('company_profile.index')->with('success', 'Deleted successfully.');
    }

    public function adminView(CompanyProfile $companyProfile)
    {
        $path = storage_path('app/secure-company-profiles/' . $companyProfile->file_name);

        abort_if(!file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => $companyProfile->mime
        ]);
    }

    public function adminDownload(CompanyProfile $companyProfile)
    {
        $path = storage_path('app/secure-company-profiles/' . $companyProfile->file_name);

        abort_if(!file_exists($path), 404);

        return response()->download(
            $path,
            $companyProfile->file_name,
            ['Content-Type' => $companyProfile->mime]
        );
    }
}
