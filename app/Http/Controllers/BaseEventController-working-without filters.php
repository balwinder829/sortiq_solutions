<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventVideo;

class BaseEventController extends Controller
{
    protected string $eventType; // college, student, employee

    /* ---------------------------
        LIST EVENTS OF GIVEN TYPE
    ----------------------------*/
    public function index()
    {
        $events = Event::where('event_type', $this->eventType)
            ->with('images')
            ->latest()
            ->paginate(20);

        return view("events.{$this->eventType}.index", compact('events'));
    }

    public function create()
    {
        $routePrefix = $this->eventType;
        return view("events.{$this->eventType}.create", compact('routePrefix'));
        // return view("events.{$this->eventType}.create");
    }

    /* ---------------------------
        STORE EVENT
    ----------------------------*/
    public function store(Request $request)
    {
        // dd($this->eventType);
        $request->validate([
            'title'        => 'required|string',
            'description'  => 'nullable|string',
            'event_date'   => 'nullable|date',
            'media'        => 'required',
            'media.*'      => 'mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',
            'cover_image'  => 'required|string',
        ]);

        // Add event type
        $eventData = $request->only(['title','description','event_date']);
        $eventData['event_type'] = $this->eventType;

        $event = Event::create($eventData);

        $selectedCoverOriginal = $request->cover_image;
        $finalCoverPath = null;

        // Folders
        if (!file_exists(public_path('images/events'))) {
            mkdir(public_path('images/events'), 0777, true);
        }
        if (!file_exists(public_path('videos/events'))) {
            mkdir(public_path('videos/events'), 0777, true);
        }

        // Upload
        foreach ($request->file('media') as $file) {

            $ext = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $filename = time() . '-' . uniqid() . '.' . $ext;

            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);

            /* --- IMAGE --- */
            if ($isImage) {
                $file->move(public_path('images/events'), $filename);

                EventImage::create([
                    'event_id'   => $event->id,
                    'image_path' => "images/events/$filename",
                ]);

                if ($originalName == $selectedCoverOriginal) {
                    $finalCoverPath = "images/events/$filename";
                }
            }

            /* --- VIDEO --- */
            if ($isVideo) {
                $file->move(public_path('videos/events'), $filename);

                EventVideo::create([
                    'event_id'   => $event->id,
                    'video_path' => "videos/events/$filename",
                ]);
            }
        }

        // Cover image
        if ($finalCoverPath) {
            $event->update(['cover_image' => $finalCoverPath]);
        }

        return redirect()
            ->route("{$this->eventType}.events.index")
            ->with('success', 'Event created.');
    }

    /* ---------------------------
        EDIT EVENT
    ----------------------------*/
    public function edit(Event $event)
    {
        $event->load(['images','videos']);

        // Prevent accessing other type's events
        if ($event->event_type !== $this->eventType) abort(404);
        $routePrefix = $this->eventType;
        return view("events.{$this->eventType}.edit", compact('event', 'routePrefix'));

        // return view("events.{$this->eventType}.edit", compact('event'));
    }

    /* ---------------------------
        UPDATE EVENT
    ----------------------------*/
    public function update(Request $request, Event $event)
    {
        if ($event->event_type !== $this->eventType) abort(404);

        $request->validate([
            'title'        => 'required|string',
            'description'  => 'nullable|string',
            'event_date'   => 'nullable|date',
            'media.*'      => 'nullable|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi,webm|max:51200',
            'cover_image'  => 'nullable|string',
        ]);

        $event->update($request->only(['title','description','event_date']));

        $selectedCoverOriginal = $request->cover_image;
        $finalCoverPath = null;

        foreach ($request->file('media', []) as $file) {

            $ext = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $filename = time() . '-' . uniqid() . '.' . $ext;

            $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);

            /* Image */
            if ($isImage) {
                $file->move(public_path('images/events'), $filename);

                EventImage::create([
                    'event_id'   => $event->id,
                    'image_path' => "images/events/$filename",
                ]);

                if ($originalName == $selectedCoverOriginal) {
                    $finalCoverPath = "images/events/$filename";
                }
            }

            /* Video */
            if ($isVideo) {
                $file->move(public_path('videos/events'), $filename);

                EventVideo::create([
                    'event_id'   => $event->id,
                    'video_path' => "videos/events/$filename",
                ]);
            }
        }

        // Change cover if selected
        if ($finalCoverPath) {
            $event->update(['cover_image' => $finalCoverPath]);
        }

        return redirect()
            ->route("{$this->eventType}.events.index")
            ->with('success', 'Event updated.');
    }


    public function show(Event $event)
    {
        // Prevent accessing wrong type
        if ($event->event_type !== $this->eventType) {
            abort(404);
        }

        $event->load(['images', 'videos']);

        $routePrefix = $this->eventType;

        return view("events.{$this->eventType}.show", compact('event', 'routePrefix'));
    }

    /* ---------------------------
        DELETE EVENT
    ----------------------------*/
    public function destroy(Event $event)
    {
        if ($event->event_type !== $this->eventType) abort(404);

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
        $event = $eventImage->event;

        if ($event->event_type !== $this->eventType) abort(404);

        $event->update(['cover_image' => $eventImage->image_path]);

        return back()->with('success','Cover image updated.');
    }
}
