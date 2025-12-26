<?php

namespace App\Http\Controllers;

use App\Models\Brochure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrochureController extends Controller
{
    /* =========================
       INDEX (ADMIN)
    ========================= */
    public function index(Request $request)
    {
        $query = Brochure::query();
        $now = now();

        if ($request->filter === 'active') {
            $query->publiclyVisible();
        } elseif ($request->filter === 'upcoming') {
            $query->whereNotNull('start_at')->where('start_at', '>', $now);
        } elseif ($request->filter === 'expired') {
            $query->whereNotNull('end_at')->where('end_at', '<', $now);
        }

        $brochures = $query->latest()->paginate(20);

        return view('brochures.index', compact('brochures'));
    }

    /* =========================
       CREATE
    ========================= */
    public function create()
    {
        return view('brochures.create');
    }

    /* =========================
       STORE
    ========================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:51200',
            'is_active'   => 'nullable|boolean',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $fileType = $ext === 'pdf' ? 'pdf' : 'image';

        $filename = time() . '-' . Str::random(10) . '.' . $ext;
        $securePath = storage_path('app/secure-brochures');

        if (!is_dir($securePath)) {
            mkdir($securePath, 0755, true);
        }

        $file->move($securePath, $filename);

        Brochure::create([
            'title'       => $data['title'],
            'description' => $data['description'],
            'file_name'   => $filename,
            'file_type'   => $fileType,
            'mime'        => $file->getClientMimeType(),
            'is_active'   => $data['is_active'] ?? true,
            'start_at'    => $data['start_at'],
            'end_at'      => $data['end_at'],
        ]);

        return redirect()
            ->route('brochures.index')
            ->with('success', 'Brochure created successfully.');
    }

    /* =========================
       EDIT
    ========================= */
    public function edit(Brochure $brochure)
    {
        return view('brochures.edit', compact('brochure'));
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, Brochure $brochure)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:51200',
            'is_active'   => 'nullable|boolean',
            'start_at'    => 'nullable|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
        ]);

        if ($request->hasFile('file')) {
            $oldPath = storage_path('app/secure-brochures/' . $brochure->file_name);
            if (file_exists($oldPath)) unlink($oldPath);

            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $fileType = $ext === 'pdf' ? 'pdf' : 'image';
            $filename = time() . '-' . Str::random(10) . '.' . $ext;

            $file->move(storage_path('app/secure-brochures'), $filename);

            $brochure->update([
                'file_name' => $filename,
                'file_type' => $fileType,
                'mime'      => $file->getClientMimeType(),
            ]);
        }

        $brochure->update($data);

        return redirect()
            ->route('brochures.index')
            ->with('success', 'Brochure updated successfully.');
    }

    /* =========================
       PREVIEW (PUBLIC, TOKEN)
    ========================= */
    public function preview($token)
    {
        // dd('hw');
        $brochure = Brochure::where('share_token', $token)
            ->publiclyVisible()
            ->firstOrFail();

        $path = storage_path('app/secure-brochures/' . $brochure->file_name);
        abort_if(!file_exists($path), 404);
        $brochure->increment('download_count');
        return response()->file($path, [
            'Content-Type' => $brochure->mime
        ]);
    }

    /* =========================
       DOWNLOAD (PUBLIC, TOKEN)
    ========================= */
    public function download($token)
    {
        $brochure = Brochure::where('share_token', $token)
            ->publiclyVisible()
            ->firstOrFail();

        $path = storage_path('app/secure-brochures/' . $brochure->file_name);
        abort_if(!file_exists($path), 404);

        $brochure->increment('download_count');

        return response()->download($path, $brochure->file_name, [
            'Content-Type' => $brochure->mime
        ]);
    }

    /* =========================
       DELETE
    ========================= */
    public function destroy(Brochure $brochure)
    {
        $path = storage_path('app/secure-brochures/' . $brochure->file_name);
        if (file_exists($path)) unlink($path);

        $brochure->delete();

        return redirect()
            ->route('brochures.index')
            ->with('success', 'Brochure deleted successfully.');
    }

    public function adminView(Brochure $brochure)
    {
        $path = storage_path('app/secure-brochures/' . $brochure->file_name);

        abort_if(!file_exists($path), 404);

        return response()->file($path, [
            'Content-Type' => $brochure->mime
        ]);
    }

    public function adminDownload(Brochure $brochure)
    {
        $path = storage_path('app/secure-brochures/' . $brochure->file_name);

        abort_if(!file_exists($path), 404);

        return response()->download(
            $path,
            $brochure->file_name,
            ['Content-Type' => $brochure->mime]
        );
    }

}
