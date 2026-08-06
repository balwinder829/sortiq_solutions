<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Student;
use App\Models\EnquiryFollowup;
use App\Models\EnquiryActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\College;
use Illuminate\Support\Facades\Storage;

class SalesEnquiryController extends Controller
{
    // LIST ONLY ASSIGNED ENQUIRIES
    public function index17dec()
    {
        $enquiries = Enquiry::where('assigned_to', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('sales.enquiries.index', compact('enquiries'));
    }


public function index(Request $request)
{
    $userId = auth()->id();

    // $query = Enquiry::where('assigned_to', $userId);
    $query = Enquiry::with('collegeData')
        ->active()
        ->where('assigned_to', $userId);

    /* ===============================
       SEARCH (NAME / MOBILE)
    =============================== */
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('mobile', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('college')) {
        $query->where('college', $request->college);
    }

    /* ===============================
       LEAD STATUS FILTER
    =============================== */
    if ($request->filled('lead_status')) {
        $query->where('lead_status', $request->lead_status);
    }

    /* ===============================
       FOLLOW-UP STATUS FILTER
    =============================== */
    if ($request->filled('followup_filter')) {

        if ($request->followup_filter === 'today') {
            $query->whereDate('next_followup_at', today());
        }

        if ($request->followup_filter === 'overdue') {
            $query->whereDate('next_followup_at', '<', today());
        }

        if ($request->followup_filter === 'upcoming') {
            $query->whereDate('next_followup_at', '>', today());
        }

        if ($request->followup_filter === 'none') {
            $query->whereNull('next_followup_at');
        }
    }

    /* ===============================
       QUICK DATE FILTER (CREATED)
    =============================== */
    if ($request->filled('quick_date')) {

        switch ($request->quick_date) {

            case 'today':
                $query->whereDate('created_at', today());
                break;

            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;

            case 'last7':
                $query->whereBetween('created_at', [
                    now()->subDays(7)->startOfDay(),
                    now()->endOfDay()
                ]);
                break;

            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;

            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)
                      ->whereYear('created_at', now()->subMonth()->year);
                break;
        }
    }

    /* ===============================
       SORTING (LATEST FIRST)
    =============================== */
    $enquiries = $query
        ->orderByDesc('next_followup_at')
        ->orderByDesc('created_at')
        ->paginate(15)
        ->appends($request->query());


        // 🔥 Load colleges for dropdown
    // $colleges = College::orderBy('college_name')->get();
        $colleges = College::whereHas('enquiries', function ($q) use ($userId) {
        $q->where('assigned_to', $userId);
    })
    ->orderBy('college_name')
    ->get();

     $previousWhatsappUploadedFiles = collect(
            Storage::disk('public')->files('whatsapp-files')
        )->map(function ($file) {
            return [
                'path' => $file,
                'name' => basename($file),
            ];
        });
        
    return view('sales.enquiries.index', compact('enquiries','colleges', 'previousWhatsappUploadedFiles'));
}

    
    // SHOW ONLY ENQUIRY ASSIGNED TO SALES USER
    public function show(Enquiry $enquiry)
    {
        if ($enquiry->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized request');
        }

        return redirect()
            ->route('sales.enquiries.index')
            ->with('error', 'This enquiry has been closed.');

        $enquiry->load(['followups.user', 'activities.user']);

        $callStatuses = DB::table('call_statuses')
        ->orderBy('name')
        ->get();

        return view('sales.enquiries.show', compact('enquiry', 'callStatuses'));
    }

public function register(Request $request, Enquiry $enquiry)
{
    $request->validate([
        'amount_paid'    => 'required|numeric|min:0',
        'payment_mode'   => 'required',
        'payment_status' => 'required|in:partial,full',
        'payment_image'  => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    DB::transaction(function () use ($request, $enquiry) {

        /* ==============================
           1️⃣ HANDLE IMAGE UPLOAD
        ============================== */

        $folderPath = public_path('registrationslips');

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $image     = $request->file('payment_image');
        $fileName  = 'payment_' . $enquiry->id . '_' . time() . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();

        $image->move($folderPath, $fileName);

        $imagePath = 'registrationslips/' . $fileName;

        /* ==============================
           2️⃣ INSERT REGISTRATION
        ============================== */

        DB::table('registrations')->insert([
            'enquiry_id'     => $enquiry->id,
            'amount_paid'    => $request->amount_paid,
            'payment_mode'   => $request->payment_mode,
            'payment_status' => $request->payment_status,
            'payment_image'  => $imagePath,
            'collected_by'   => auth()->id(),
            'registered_at'  => now(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        /* ==============================
           3️⃣ UPDATE ENQUIRY SNAPSHOT
        ============================== */

        $enquiry->update([
            'lead_status'    => 'registered',
            'registered_at'  => now(),
            'registered_by'  => auth()->id(),
        ]);

        /* ==============================
           4️⃣ CREATE STUDENT (IF NOT EXISTS)
        ============================== */

        // $studentExists = Student::where('contact', $enquiry->mobile)
        //     ->orWhere('email_id', $enquiry->email)
        //     ->exists();

        // if (! $studentExists) {

            // $student = Student::create([
            //     'student_name' => $enquiry->name,
            //     'f_name'       => '',
            //     'email_id'     => $enquiry->email,
            //     'contact'      => $enquiry->mobile,
            //     'college_name' => $enquiry->college,
            //     'reg_fees'     => $request->amount_paid,
            //     'enquiry_id'   => $enquiry->id,
            //     'created_by'   => Auth::id(),
            // ]);

            // Notify assigned sales user
            // $salesUser = $enquiry->assignedTo;
            // if ($salesUser) {
            //     $salesUser->notify(
            //         new \App\Notifications\StudentRegisteredSalesNotification($student)
            //     );
            // }
        // }

        /* ==============================
           5️⃣ ACTIVITY LOG
        ============================== */

        EnquiryActivity::create([
            'enquiry_id' => $enquiry->id,
            'user_id'    => auth()->id(),
            'type'       => 'registration',
            'details'    => "Registered with ₹{$request->amount_paid} via {$request->payment_mode}",
        ]);
    });

    return back()->with('success', 'Student registered successfully.');
}


    public function storeFollowup(Request $request, Enquiry $enquiry)
    {
        if ($enquiry->assigned_to !== Auth::id()) {
            abort(403, 'Unauthorized request');
        }

        if ($enquiry->lead_status === 'registered') {
            return back()->with('error', 'This lead is already registered and locked.');
        }

        return redirect()
            ->route('sales.enquiries.index')
            ->with('error', 'This enquiry has been closed.');

        $request->validate([
            'status' => 'required|in:new,followup,registered,closed',
            'call_status' => 'required',
            'note' => 'nullable|string',
            'next_followup_date' => 'nullable|date'
        ]);

        // 1️⃣ CREATE FOLLOW-UP RECORD
        $followup = EnquiryFollowup::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => Auth::id(),
            'status' => $request->status,
            'call_status' => $request->call_status,
            'note' => $request->note,
            'next_followup_date' => $request->next_followup_date,
        ]);

        // 2️⃣ UPDATE ENQUIRY SNAPSHOT (🔥 THIS WAS MISSING)
        $oldLeadStatus = $enquiry->lead_status;

        $snapshot = [
            'lead_status'       => $request->status,
            'last_call_status'  => $request->call_status,
            'last_contacted_at' => now(),
            'next_followup_at'  => $request->next_followup_date,
        ];

        // If REGISTERED
        // if ($request->status === 'registered') {
        //     $snapshot['registered_at'] = now();
        //     $snapshot['registered_by'] = Auth::id();
        //     $snapshot['is_converted']  = 1;
        // }

        $enquiry->update($snapshot);

        // 3️⃣ LOG FOLLOW-UP ACTIVITY
        EnquiryActivity::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => Auth::id(),
            'type' => 'followup',
            'details' => "Call: {$request->call_status}. Note: {$request->note}",
        ]);

        // 4️⃣ LOG STATUS CHANGE (ONLY IF CHANGED)
        if ($oldLeadStatus !== $request->status) {
            EnquiryActivity::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => Auth::id(),
                'type' => 'status_change',
                'old_value' => $oldLeadStatus,
                'new_value' => $request->status,
                'details' => "Lead status changed: {$oldLeadStatus} → {$request->status}",
            ]);
        }

        return back()->with('success', 'Follow-up recorded successfully.');
    }

}
