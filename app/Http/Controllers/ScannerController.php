<?php

namespace App\Http\Controllers;

use App\Models\Scanner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use File;

class ScannerController extends Controller
{
    /**
     * List scanners
     */
    public function index()
    {
        $scanners = Scanner::latest()->get();
        return view('scanners.index', compact('scanners'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('scanners.create');
    }

    /**
     * Store scanner
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'required|image|max:2048',
            'source'      => 'nullable|string|max:50',
            'source_url'  => 'nullable|url',
            'description' => 'nullable|string',
            'is_active'   => 'nullable',
            'is_public'   => 'nullable',
        ]);

        // ✅ Ensure upload directory exists
        $uploadPath = public_path('uploads/scanners');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Upload image
        $imageName = Str::uuid() . '.' . $request->image->extension();
        $request->image->move($uploadPath, $imageName);

        $data['image_path'] = 'uploads/scanners/' . $imageName;
        $data['is_active']  = $request->has('is_active');
        $data['is_public']  = $request->has('is_public');

        if ($data['is_public']) {
            $data['share_token'] = Str::uuid();
        }

        Scanner::create($data);

        return redirect()->route('scanners.index')
            ->with('success', 'Scanner created successfully');
    }

    /**
     * Show edit form
     */
    public function edit(Scanner $scanner)
    {
        return view('scanners.edit', compact('scanner'));
    }

    /**
     * Update scanner
     */
    public function update(Request $request, Scanner $scanner)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|max:2048',
            'source'      => 'nullable|string|max:50',
            'source_url'  => 'nullable|url',
            'description' => 'nullable|string',
            'is_active'   => 'nullable',
            'is_public'   => 'nullable',
        ]);

        // ✅ Ensure upload directory exists
        $uploadPath = public_path('uploads/scanners');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        if ($request->hasFile('image')) {

            // Delete old image
            if ($scanner->image_path && File::exists(public_path($scanner->image_path))) {
                File::delete(public_path($scanner->image_path));
            }

            // Upload new image
            $imageName = Str::uuid() . '.' . $request->image->extension();
            $request->image->move($uploadPath, $imageName);
            $data['image_path'] = 'uploads/scanners/' . $imageName;
        }

        $data['is_active'] = $request->has('is_active');
        $data['is_public'] = $request->has('is_public');

        // Share token handling
        if ($data['is_public'] && !$scanner->share_token) {
            $data['share_token'] = Str::uuid();
        }

        if (!$data['is_public']) {
            $data['share_token'] = null;
        }

        $scanner->update($data);

        return redirect()->route('scanners.index')
            ->with('success', 'Scanner updated successfully');
    }

    public function show(Scanner $scanner)
	{
	    return view('scanners.show', compact('scanner'));
	}

    /**
     * Delete scanner
     */
    public function destroy(Scanner $scanner)
    {
        // if ($scanner->image_path && File::exists(public_path($scanner->image_path))) {
        //     File::delete(public_path($scanner->image_path));
        // }

        $scanner->delete();

        return redirect()->route('scanners.index')
            ->with('success', 'Scanner deleted successfully');
    }
}
