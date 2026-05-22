<?php
 
namespace App\Http\Controllers\Events;
use App\Http\Controllers\Controller;

use App\Models\UpcomingEvent;
use Illuminate\Http\Request;

class UpcomingEventController extends Controller
{
    protected string $permissionPrefix = 'upcoming_events';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'download'         => 'view',
        'sendEmail'         => 'view',
         

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
