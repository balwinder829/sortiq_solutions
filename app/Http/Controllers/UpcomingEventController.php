<?php
 
namespace App\Http\Controllers;

use App\Models\UpcomingEvent;
use Illuminate\Http\Request;

class UpcomingEventController extends Controller
{
    public function index()
    {
        $upcomingEvents = UpcomingEvent::latest()->get();
        return view('upcoming-events.index', compact('upcomingEvents'));
    }

    public function create()
    {
        return view('upcoming-events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'event_date' => 'required|date',
        ]);

        UpcomingEvent::create([
            'name'        => $request->name,
            'description' => $request->description,
            'event_date'  => $request->event_date,
            'notify'      => $request->boolean('notify'),
        ]);

        return redirect()
            ->route('upcoming-events.index')
            ->with('success', 'Upcoming event created successfully.');
    }

    public function edit(UpcomingEvent $event)
    {
        return view('upcoming-events.edit', compact('event'));
    }

    public function update(Request $request, UpcomingEvent $event)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'event_date' => 'required|date',
        ]);

        $event->update([
            'name'        => $request->name,
            'description' => $request->description,
            'event_date'  => $request->event_date,
            'notify'      => $request->boolean('notify'),
        ]);

        return redirect()
            ->route('upcoming-events.index')
            ->with('success', 'Upcoming event updated successfully.');
    }

    public function dismiss(UpcomingEvent $event)
    {
        $event->update([
            'dismissed' => true,
            'notify'    => false
        ]);

        return back()->with('success', 'Upcoming event notification dismissed.');
    }

    public function enable(UpcomingEvent $event)
    {
        $event->update([
            'notify'    => true,
            'dismissed' => false
        ]);

        return back()->with('success', 'Upcoming event notification enabled.');
    }

    // 📅 Calendar view
    public function calendar()
    {
        $events = UpcomingEvent::select(
            'id',
            'name as title',
            'event_date as start'
        )->get();

        return view('upcoming-events.calendar', [
            'events' => $events->toArray()
        ]);
    }
    
    public function show(UpcomingEvent $event)
    {
        return view('upcoming-events.show', compact('event'));
    }

    public function destroy(UpcomingEvent $event)
    {
        // Optional: prevent deleting past events
        // if ($event->event_date->isPast()) {
        //     return back()->with('error', 'Past events cannot be deleted.');
        // }

        $event->delete();

        return redirect()
            ->route('upcoming-events.index')
            ->with('success', 'Upcoming event deleted successfully.');
    }
}
