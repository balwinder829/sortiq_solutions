<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ===================== LIST =====================
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }


    // ===================== VIEW + REDIRECT =====================
    public function view($id)
    {
        $n = auth()->user()->notifications()->findOrFail($id);
        $n->markAsRead();

        $data = $n->data;
        $key  = $data['template_key'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | SALES NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        // 1️⃣ Lead Assigned → Go to Enquiry Detail Page
        if ($key === 'lead.assigned' && isset($data['lead_id'])) {
            return redirect()->route('sales.enquiries.show', $data['lead_id']);
        }

        // 2️⃣ Follow-ups Pending Today → Go to Enquiry List Page
        if ($key === 'sales.followups.today') {
            return redirect()->route('sales.enquiries.index')
                ->with('info', 'You have follow-ups pending today.');
        }

        // 3️⃣ Missed Follow-ups Yesterday → Go to Enquiry List Page
        if ($key === 'sales.followups.missed') {
            return redirect()->route('sales.enquiries.index')
                ->with('warning', 'You missed follow-ups yesterday.');
        }


        /*
        |--------------------------------------------------------------------------
        | TRAINER NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        // Batch Assigned → Trainer redirect to batch detail page
        if ($key === 'batch.assigned' && isset($data['batch_id'])) {
            return redirect()->route('batches.show', $data['batch_id']);
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        // Fee Pending Summary → Admin redirect to student list
        if ($key === 'fee.pending.summary') {
             // return redirect()->route('admin.pendingfees.list')->with('info', 'Here is the list of students with pending fee.');
            return redirect()->route('certificates.index', ['notification' => 'pending_fee']);
            // return redirect()->route('students.index')
                // ->with('info', 'Here is the list of students with pending fee.');
        }

         // Fee paid and certificate issued → Admin redirect to student list
        if ($key === 'bin.ready.summary') {
            return redirect()->route('admin.closinglists');
            return redirect()->route('certificates.index', ['notification' => 'bin_ready']);
        }


        // Notify when lead is coverted as student
        if ($key === 'student.registered.sales' && isset($data['student_id'])) {
            return redirect()->route('students.show', $data['student_id']);
        }

        // Notify admin when lead is coverted as student
        // if ($key === 'student.registered.summary') {
        //     return redirect()->route('students.index');
        // }

        if ($key === 'student.registered.summary') {
            return redirect()->route('students.index', ['notification' => 'registered_today']);
        }

        if ($key === 'upcoming.event') {
            return redirect()->route('upcoming-events.show', $data['event_id']
        );


            // dd($data['template_key'] ?? 'no template_key');


       


}

        if ($key === 'sales.leads.low.percent.admin') {

            // 👨‍💼 ADMIN → Salesperson detail page
            if (
                auth()->user()->role == 1 &&
                isset($data['meta']['sales_user_id'])
            ) {
                return redirect()->route(
                    'salespersons.show',
                    $data['meta']['sales_user_id']
                );
            }
        }

         if ($key === 'sales.leads.low.percent') {
            // dd('here2');
           
            // 🧑‍💼 SALES USER → Own enquiries
            return redirect()->route('sales.enquiries.index')
                ->with('warning', 'Your leads are running low.');
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT FALLBACK
        |--------------------------------------------------------------------------
        */

        return redirect()->back();
    }


    // ===================== FULL SHOW PAGE =====================
    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        return view('notifications.show', compact('notification'));
    }


    // ===================== MARK ONE READ (AJAX) =====================
    public function markRead($id)
    {
        $n = auth()->user()->notifications()->findOrFail($id);
        $n->markAsRead();

        return response()->json(['success' => true]);
    }


    // ===================== MARK ALL READ =====================
    public function markAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    public function byType($type)
{
    $notifications = Auth::user()
        ->notifications()
        ->where('data->template_key', $type)
        ->paginate(10);

    return view('notifications.index', compact('notifications'));
}

  /**
     * ✅ Mark ONE notification as read
     */
    public function clearOne($id)
    {
        Auth::user()
            ->notifications()
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }

    /**
     * ✅ Mark ALL unread notifications as read
     */
    public function clearAll()
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back();
    }

    /**
     * ✅ Mark ALL unread notifications of ONE TEMPLATE as read
     * (MariaDB-safe JSON query)
     */
    public function clearByTemplate(string $templateKey)
    {
        Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->whereRaw(
                "JSON_UNQUOTE(JSON_EXTRACT(data, '$.template_key')) = ?",
                [$templateKey]
            )
            ->update(['read_at' => now()]);

        return back()->with('success', 'Notifications marked as read.');
    }
}
