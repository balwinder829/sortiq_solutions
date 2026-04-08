<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification; // ✅ IMPORTANT

class NotificationController extends Controller
{
    // ===================== LIST =====================
    public function index()
    {   
        $activeSessionNo = session('admin_session_id');

        // 🔹 TRAINER
        if (Auth::guard('trainer')->check()) {
            $trainer = Auth::guard('trainer')->user();

            $notifications = Notification::where('notifiable_id', $trainer->id)
                ->where('notifiable_type', get_class($trainer))
                ->when($activeSessionNo, function ($q) use ($activeSessionNo) {
                    $q->where(function ($query) use ($activeSessionNo) {
                        $query->where('session_id', $activeSessionNo)
                              ->orWhereNull('session_id');
                    });
                })
                ->latest()
                ->paginate(20);

            return view('notifications.index', compact('notifications'));
        }

        // 🔹 SALES STAFF
        if (Auth::guard('sales_staff')->check()) {
            $sales_staff = Auth::guard('sales_staff')->user();

            $notifications = Notification::where('notifiable_id', $sales_staff->id)
                ->where('notifiable_type', get_class($sales_staff))
                ->when($activeSessionNo, function ($q) use ($activeSessionNo) {
                    $q->where(function ($query) use ($activeSessionNo) {
                        $query->where('session_id', $activeSessionNo)
                              ->orWhereNull('session_id');
                    });
                })
                ->latest()
                ->paginate(20);

            return view('notifications.index', compact('notifications'));
        }

        // 🔹 ADMIN / USER
        if (Auth::check()) {
            if (!in_array(Auth::user()->role, [1, 2, 3])) {
                abort(403);
            }

            $notifications = Notification::where('notifiable_id', Auth::id())
                ->where('notifiable_type', \App\Models\User::class)
                ->when($activeSessionNo, function ($q) use ($activeSessionNo) {
                    $q->where(function ($query) use ($activeSessionNo) {
                        $query->where('session_id', $activeSessionNo)
                              ->orWhereNull('session_id');
                    });
                })
                ->latest()
                ->paginate(20);

            return view('notifications.index', compact('notifications'));
        }

        abort(403);
    }


    // ===================== VIEW + REDIRECT =====================
    public function view($id)
    {
        $n = Notification::where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->firstOrFail();

        $n->update(['read_at' => now()]);

        $data = $n->data;
        $key  = $data['template_key'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | SALES NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        if ($key === 'lead.assigned' && isset($data['lead_id'])) {
            return redirect()->route('sales.enquiries.show', $data['lead_id']);
        }

        if ($key === 'sales.followups.today') {
            return redirect()->route('sales.enquiries.index')
                ->with('info', 'You have follow-ups pending today.');
        }

        if ($key === 'sales.followups.missed') {
            return redirect()->route('sales.enquiries.index')
                ->with('warning', 'You missed follow-ups yesterday.');
        }

        if ($key === 'admin.interviews.today') {
            return redirect()
                ->route('daily-interviews.index', ['date_filter' => 'today'])
                ->with('info', 'Here are today’s scheduled interviews.');
        }

        if ($key === 'workshop.reminder.week') {
            return redirect()
                ->route('workshops.index', ['range' => 'upcoming'])
                ->with('info', 'Here are upcoming workshops.');
        }

        if ($key === 'workshop.reminder.two_days') {
            return redirect()
                ->route('workshops.index', ['range' => 'upcoming'])
                ->with('info', 'Here are upcoming workshops.');
        }

        /*
        |--------------------------------------------------------------------------
        | TRAINER NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        if ($key === 'batch.assigned' && isset($data['batch_id'])) {
            return redirect()->route('batches.show', $data['batch_id']);
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        if ($key === 'fee.pending.summary') {
            return redirect()->route('certificates.index', ['notification' => 'pending_fee']);
        }

        if ($key === 'bin.ready.summary') {
            return redirect()->route('admin.closinglists');
        }

        if ($key === 'student.registered.sales' && isset($data['student_id'])) {
            return redirect()->route('students.show', $data['student_id']);
        }

        if ($key === 'student.registered.summary') {
            return redirect()->route('students.index', ['notification' => 'registered_today']);
        }

        if ($key === 'upcoming.event') {
            return redirect()->route('upcoming-events.show', $data['event_id']);
        }

        if ($key === 'sales.leads.low.percent.admin') {
            if (auth()->user()->role == 1 && isset($data['meta']['sales_user_id'])) {
                return redirect()->route(
                    'salespersons.show',
                    $data['meta']['sales_user_id']
                );
            }
        }

        if ($key === 'sales.leads.low.percent') {
            return redirect()->route('sales.enquiries.index')
                ->with('warning', 'Your leads are running low.');
        }

        return redirect()->back();
    }


    // ===================== FULL SHOW PAGE =====================
    public function show($id)
    {
        $notification = Notification::where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->firstOrFail();

        return view('notifications.show', compact('notification'));
    }


    // ===================== MARK ONE READ (AJAX) =====================
    public function markRead($id)
    {
        Notification::where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }


    // ===================== MARK ALL READ =====================
    public function markAll()
    {
        Notification::where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }


    public function byType($type)
    {
        $notifications = Notification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', \App\Models\User::class)
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.template_key')) = ?", [$type])
            ->paginate(10);

        return view('notifications.index', compact('notifications'));
    }


    // ===================== CLEAR ONE =====================
    public function clearOne($id)
    {
        Notification::where('id', $id)
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }


    // ===================== CLEAR ALL =====================
    public function clearAll()
    {
        Notification::where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }


    // ===================== CLEAR BY TEMPLATE =====================
    public function clearByTemplate(string $templateKey)
    {
        Notification::where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(data, '$.template_key')) = ?",
                [$templateKey]
            )
            ->update(['read_at' => now()]);

        return back()->with('success', 'Notifications marked as read.');
    }
}