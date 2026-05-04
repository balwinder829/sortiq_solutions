<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventVideo;
use Illuminate\Http\Request;
// use Intervention\Image\Facades\Image;    
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('images')->latest()->paginate(20);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'title'        => 'required|string',
        'description'  => 'nullable|string',
        'event_date'   => 'nullable|date',

        'media'        => 'required', // at least one media file required
        'media.*'      => 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',

        'cover_image'  => 'required|string', // user must pick a cover image
    ]);

    $event = Event::create($request->only(['title','description','event_date']));

    // Store original selected filename (from input)
    $selectedCoverOriginalName = $request->cover_image;
    $finalCoverPath = null;


    /* ---------- Ensure folders exist ---------- */
    if (!file_exists(public_path('images/events'))) {
        mkdir(public_path('images/events'), 0777, true);
    }

    if (!file_exists(public_path('videos/events'))) {
        mkdir(public_path('videos/events'), 0777, true);
    }


    /* ---------- PROCESS MEDIA FILES ---------- */
    if ($request->hasFile('media')) {

        foreach ($request->file('media') as $file) {

            $ext = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName(); // to match cover
            $filename = time() . '-' . uniqid() . '.' . $ext;

            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);


            /* ---------- IMAGE UPLOAD + COMPRESSION ---------- */
            if ($isImage) {
                // $img = \Intervention\Image\Facades\Image::make($file->getRealPath());
                // $img = Image::make($file->getRealPath());
                // $img = Image::read($file->getRealPath());

                // $img->resize(1600, 1600, function ($c) {
                //     $c->aspectRatio();
                //     $c->upsize();
                // });

                // $img->save(public_path('images/events/'.$filename), 75);
                $file->move(public_path('images/events'), $filename);

                EventImage::create([
                    'event_id'   => $event->id,
                    'image_path' => 'images/events/' . $filename
                ]);

                // MATCH selected cover image with original filename
                if ($originalName == $selectedCoverOriginalName) {
                    $finalCoverPath = 'images/events/' . $filename;
                }
            }


            /* ---------- VIDEO UPLOAD ---------- */
            if ($isVideo) {
                $file->move(public_path('videos/events'), $filename);

                EventVideo::create([
                    'event_id'   => $event->id,
                    'video_path' => 'videos/events/' . $filename
                ]);
            }
        }
    }


    /* ---------- SET FINAL COVER IMAGE ---------- */
    if ($finalCoverPath) {
        $event->update(['cover_image' => $finalCoverPath]);
    } else {
        // If somehow user selected a video (should never happen)
        return back()->withErrors(['cover_image' => 'Cover image must be an image file.']);
    }


    return redirect()->route('events.index')->with('success','Event created.');
}


    public function show(Event $event)
    {
        $event->load(['images','videos']);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $event->load(['images','videos']);
        return view('events.edit', compact('event'));
    }

public function update(Request $request, Event $event)
{
    $request->validate([
        'title'        => 'required|string',
        'description'  => 'nullable|string',
        'event_date'   => 'nullable|date',

        // MEDIA IS OPTIONAL ON UPDATE
        'media.*'      => 'nullable|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',

        // cover_image optional (only if new images selected)
        'cover_image'  => 'nullable|string',
    ]);

    // Update basic info
    $event->update($request->only(['title','description','event_date']));

    // Set chosen cover (original name of uploaded file)
    $selectedCoverOriginalName = $request->cover_image;
    $finalCoverPath = null;

    // Ensure folders exist
    if (!file_exists(public_path('images/events'))) {
        mkdir(public_path('images/events'), 0777, true);
    }
    if (!file_exists(public_path('videos/events'))) {
        mkdir(public_path('videos/events'), 0777, true);
    }

    // Create Image Manager
    $manager = new ImageManager(new Driver());


    /* ---------------------------------------------------
        PROCESS NEW MEDIA FILES (IF ANY UPLOADED)
    --------------------------------------------------- */
    if ($request->hasFile('media')) {

        foreach ($request->file('media') as $file) {

            $ext = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $filename = time() . '-' . uniqid() . '.' . $ext;

            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);


            /* ---------- IMAGE HANDLING ---------- */
            if ($isImage) {

                // $img = $manager->read($file->getRealPath());

                // Compress only
                // $img->save(public_path('images/events/'.$filename), 75);
                $file->move(public_path('images/events'), $filename);
                EventImage::create([
                    'event_id'   => $event->id,
                    'image_path' => 'images/events/' . $filename
                ]);

                // If user selected it as new cover
                if ($originalName == $selectedCoverOriginalName) {
                    $finalCoverPath = 'images/events/' . $filename;
                }
            }


            /* ---------- VIDEO HANDLING ---------- */
            if ($isVideo) {
                $file->move(public_path('videos/events'), $filename);

                EventVideo::create([
                    'event_id'   => $event->id,
                    'video_path' => 'videos/events/' . $filename
                ]);
            }
        }
    }


    /* ---------------------------------------------------
        APPLY NEW COVER IMAGE (ONLY IF USER SELECTED ONE)
    --------------------------------------------------- */
    if ($finalCoverPath) {
        $event->update(['cover_image' => $finalCoverPath]);
    }


    return redirect()
        ->route('events.index', $event->id)
        ->with('success','Event updated.');
}
public function update1(Request $request, Event $event)
{
    $request->validate([
        'title'        => 'required|string',
        'description'  => 'nullable|string',
        'event_date'   => 'nullable|date',

        // Media is optional in edit
        'media.*'      => 'nullable|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',

        // Cover image optional in edit (user may keep existing)
        'cover_image'  => 'nullable|string',
    ]);

    $event->update($request->only(['title','description','event_date']));

    $selectedCoverOriginal = $request->cover_image;
    $finalCoverPath = null;

    // Ensure folders exist
    if (!file_exists(public_path('images/events'))) {
        mkdir(public_path('images/events'), 0777, true);
    }

    if (!file_exists(public_path('videos/events'))) {
        mkdir(public_path('videos/events'), 0777, true);
    }

    // Create Image Manager
    $manager = new ImageManager(new Driver());


    /* ---------------------------------------------------
        HANDLE NEW MEDIA UPLOADS
    --------------------------------------------------- */
    if ($request->hasFile('media')) {

        foreach ($request->file('media') as $file) {

            $ext = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $filename = time() . '-' . uniqid() . '.' . $ext;

            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);


            /* ---------------------------
                PROCESS IMAGE FILE
            ----------------------------*/
            if ($isImage) {
                // $img = $manager->read($file->getRealPath());

                // Compress while keeping original dimensions
                // $img->save(public_path('images/events/'.$filename), 75);
                $file->move(public_path('images/events'), $filename);
                EventImage::create([
                    'event_id'   => $event->id,
                    'image_path' => 'images/events/' . $filename
                ]);

                // Check if user selected this as new cover
                if ($originalName == $selectedCoverOriginal) {
                    $finalCoverPath = 'images/events/' . $filename;
                }
            }


            /* ---------------------------
                PROCESS VIDEO FILE
            ----------------------------*/
            if ($isVideo) {
                $file->move(public_path('videos/events'), $filename);

                EventVideo::create([
                    'event_id'   => $event->id,
                    'video_path' => 'videos/events/' . $filename
                ]);
            }
        }
    }


    /* ---------------------------------------------------
        APPLY COVER IMAGE CHANGE (IF USER PICKED NEW ONE)
    --------------------------------------------------- */
    if ($finalCoverPath) {
        $event->update(['cover_image' => $finalCoverPath]);
    }

    return redirect()
        ->route('events.show', $event->id)
        ->with('success', 'Event updated.');
}


    public function destroy(Event $event)
    {
        foreach ($event->images as $img) {
            @unlink(public_path($img->image_path));
            $img->delete();
        }

        foreach ($event->videos as $video) {
            @unlink(public_path($video->video_path));
            $video->delete();
        }

        $event->delete();

        return back()->with('success','Event deleted.');
    }

    public function deleteImage(EventImage $image)
    {
        @unlink(public_path($image->image_path));
        $image->delete();
        return back()->with('success','Image removed.');
    }

    public function deleteVideo(EventVideo $video)
    {
        @unlink(public_path($video->video_path));
        $video->delete();
        return back()->with('success','Video removed.');
    }

    public function setCover(EventImage $eventImage)
    {
        $eventImage->event->update([
            'cover_image' => $eventImage->image_path
        ]);

        return back()->with('success','Cover image updated.');
    }
}
