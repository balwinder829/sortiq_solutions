<?php

namespace App\Http\Controllers;

use App\Models\Placement;
use App\Models\PlacementImage;
use App\Models\PlacementVideo;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    /* ============================================================
       INDEX
    ============================================================ */
    public function index()
    {
        $placements = Placement::latest()->get();
        return view('placements.index', compact('placements'));
    }

    /* ============================================================
       CREATE
    ============================================================ */
    public function create()
    {
        return view('placements.create');
    }

    /* ============================================================
       STORE
    ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'media'       => 'required',
            'media.*'     => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi',
            'cover_image' => 'required'
        ]);

        $placement = Placement::create([
            'name'        => $request->name,
            'description' => $request->description
        ]);

        /* ---------------------------------------------------------
           Upload Media
        --------------------------------------------------------- */
        foreach ($request->file('media') as $file) {

            $filename = time() . "_" . $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {

                $file->move(public_path('placement/images'), $filename);

                $img = PlacementImage::create([
                    'placement_id' => $placement->id,
                    'path'         => 'placement/images/' . $filename
                ]);

                // SET COVER IMAGE
                if ($request->cover_image == $file->getClientOriginalName()) {
                    $placement->update([
                        'cover_image' => $img->path
                    ]);
                }

            } else {

                $file->move(public_path('placement/videos'), $filename);

                PlacementVideo::create([
                    'placement_id' => $placement->id,
                    'path'         => 'placement/videos/' . $filename
                ]);
            }
        }

        return redirect()->route('placements.index')
                         ->with('success', 'Placement created successfully!');
    }

    /* ============================================================
       EDIT
    ============================================================ */
    public function edit(Placement $placement)
    {
        $placement->load('images', 'videos');
        return view('placements.edit', compact('placement'));
    }

    /* ============================================================
       UPDATE
    ============================================================ */
    public function update(Request $request, Placement $placement)
    {
        $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'media.*'     => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi',
        ]);

        $placement->update([
            'name'        => $request->name,
            'description' => $request->description
        ]);

        /* Upload New Media */
        if ($request->hasFile('media')) {

            foreach ($request->file('media') as $file) {
                $filename = time() . "_" . $file->getClientOriginalName();
                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {

                    $file->move(public_path('placement/images'), $filename);

                    PlacementImage::create([
                        'placement_id' => $placement->id,
                        'path'         => 'placement/images/' . $filename
                    ]);

                } else {

                    $file->move(public_path('placement/videos'), $filename);

                    PlacementVideo::create([
                        'placement_id' => $placement->id,
                        'path'         => 'placement/videos/' . $filename
                    ]);
                }
            }
        }

        return redirect()->route('placements.index')
                         ->with('success', 'Placement updated successfully!');
    }


    public function show(Placement $placement)
{
        $placement->load('images', 'videos');
        return view('placements.show', compact('placement'));
    }


    /* ============================================================
       DELETE PLACEMENT
    ============================================================ */
    public function destroy(Placement $placement)
    {
        $placement->delete();
        return redirect()->route('placements.index')
                         ->with('success', 'Placement deleted!');
    }

    /* ============================================================
       AJAX DELETE IMAGE
    ============================================================ */
    public function deleteImage($id)
    {
        $image = PlacementImage::findOrFail($id);

        if (file_exists(public_path($image->path))) {
            unlink(public_path($image->path));
        }

        $image->delete();
        return response()->json(['success' => true]);
    }

    /* ============================================================
       AJAX DELETE VIDEO
    ============================================================ */
    public function deleteVideo($id)
    {
        $video = PlacementVideo::findOrFail($id);

        if (file_exists(public_path($video->path))) {
            unlink(public_path($video->path));
        }

        $video->delete();
        return response()->json(['success' => true]);
    }

    /* ============================================================
       AJAX SET COVER IMAGE
    ============================================================ */
    public function setCover($id)
    {
        $image = PlacementImage::findOrFail($id);

        $image->placement->update([
            'cover_image' => $image->path
        ]);

        return response()->json(['success' => true]);
    }
}
