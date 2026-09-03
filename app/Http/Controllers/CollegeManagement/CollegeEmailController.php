<?php

namespace App\Http\Controllers\CollegeManagement;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Models\EmailPurpose;
use App\Models\EmailSender;
use App\Jobs\SendCollegeEmailJob;
use Illuminate\Support\Facades\DB;
use App\Http\DataTables\DataTablesServerSide;
use App\Models\State;
use App\Models\District;

class CollegeEmailController extends Controller
{

     public function __construct()
    {
        $this->middleware('permission:college_emails.view')->only(['index','logs','view']);
        $this->middleware('permission:college_emails.create')->only(['create','store','retryByCollege']);
    }

    /*
    |--------------------------------------------------
    | 1. COLLEGE INDEX (NEW MAIN PAGE)
    |--------------------------------------------------
    */

    public function index(Request $request)
{
    if ($request->ajax()) {

        $activeSessionId = session('admin_session_id');

        $query = College::with(['state', 'district']);

        /*
        |------------------------------------------
        | FILTER: Email Status
        |------------------------------------------
        */

        if ($request->college_id) {
            $query->where('id', $request->college_id);
        }

        if ($request->state_id) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->district_id) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->college_type !== null && $request->college_type !== '') {
            $query->where('college_type', $request->college_type);
        }

        if ($request->email_status == 'sent') {
            $query->whereHas('emailRecipients', function ($q) use ($activeSessionId) {
                $q->where('session_id', $activeSessionId)
                  ->where('status', 'sent');
            });
        }

        if ($request->email_status == 'failed') {
            $query->whereHas('emailRecipients', function ($q) use ($activeSessionId) {
                $q->where('session_id', $activeSessionId)
                  ->where('status', 'failed');
            });
        }

        if ($request->email_status == 'not_sent') {
            $query->whereDoesntHave('emailRecipients', function ($q) use ($activeSessionId) {
                $q->where('session_id', $activeSessionId);
            });
        }

        // if ($request->date_from) {
        //     $query->whereHas('emailRecipients', function ($q) use ($request, $activeSessionId) {
        //         $q->where('session_id', $activeSessionId)
        //           ->whereDate('sent_at', '>=', $request->date_from);
        //     });
        // }

        // if ($request->date_to) {
        //     $query->whereHas('emailRecipients', function ($q) use ($request, $activeSessionId) {
        //         $q->where('session_id', $activeSessionId)
        //           ->whereDate('sent_at', '<=', $request->date_to);
        //     });
        // }

        if (!$request->range) {

            if ($request->date_from) {
                $query->whereHas('emailRecipients', function ($q) use ($request, $activeSessionId) {
                    $q->where('session_id', $activeSessionId)
                      ->whereDate('sent_at', '>=', $request->date_from);
                });
            }

            if ($request->date_to) {
                $query->whereHas('emailRecipients', function ($q) use ($request, $activeSessionId) {
                    $q->where('session_id', $activeSessionId)
                      ->whereDate('sent_at', '<=', $request->date_to);
                });
            }

        }

        if ($request->range) {

            $query->whereHas('emailRecipients', function ($q) use ($request, $activeSessionId) {

                $q->where('session_id', $activeSessionId);

                switch ($request->range) {

                    case 'today':
                        $q->whereDate('sent_at', today());
                        break;

                    case 'yesterday':
                        $q->whereDate('sent_at', today()->subDay());
                        break;

                    case 'current_week_past':
                        $q->whereBetween('sent_at', [
                            now()->startOfWeek(),
                            now()
                        ]);
                        break;

                    case 'last_week':
                        $q->whereBetween('sent_at', [
                            now()->subWeek()->startOfWeek(),
                            now()->subWeek()->endOfWeek()
                        ]);
                        break;

                    case 'last_month':
                        $q->whereBetween('sent_at', [
                            now()->subMonth()->startOfMonth(),
                            now()->subMonth()->endOfMonth()
                        ]);
                        break;

                    case 'last_30_days':
                        $q->whereBetween('sent_at', [
                            now()->subDays(30),
                            now()
                        ]);
                        break;
                }
            });
        }

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id'],
            'searchable' => ['college_name'],
        ], function ($college, $index, $start) use ($activeSessionId) {

            /*
            |------------------------------------------
            | EMAIL COUNT
            |------------------------------------------
            */
            $emailCount = EmailRecipient::where('college_id', $college->id)
                ->where('session_id', $activeSessionId)
                ->count();

            // $emailCount = EmailRecipient::where('college_id', $college->id)
            //     ->where('session_id', $activeSessionId)
            //     ->count();

            if ($emailCount > 0) {
                $emailCount = '<a href="'.route('admin.college-emails.logs', $college->id).'"  target="_blank"  class="text-primary fw-bold">'
                    .$emailCount.
                    '</a>';
            }
            /*
            |------------------------------------------
            | SENT TO (HOD / TPO / BOTH)
            |------------------------------------------
            */
            $types = EmailRecipient::where('college_id', $college->id)
                ->where('session_id', $activeSessionId)
                ->select('type')
                ->distinct()
                ->pluck('type')
                ->toArray();

            if (empty($types)) {
                $sentTo = '-';
            } elseif (count($types) == 2) {
                $sentTo = '<span class="badge bg-info">Both</span>';
            } else {
                $sentTo = '<span class="badge bg-primary">'.strtoupper($types[0]).'</span>';
            }

            /*
            |------------------------------------------
            | STATUS (LATEST)
            |------------------------------------------
            */
            $latest = EmailRecipient::where('college_id', $college->id)
                ->where('session_id', $activeSessionId)
                ->latest()
                ->first();

            if (!$latest) {
                $status = '<span class="badge bg-secondary">Not Sent</span>';
            } else {
                $color = $latest->status == 'sent' ? 'success' : ($latest->status == 'failed' ? 'danger' : 'secondary');
                $status = '<span class="badge bg-'.$color.'">'.ucfirst($latest->status).'</span>';
            }

            /*
            |------------------------------------------
            | CHECKBOX
            |------------------------------------------
            */
            $checkbox = '<input type="checkbox" class="record_checkbox" value="'.$college->id.'">';

            /*
            |------------------------------------------
            | ACTIONS
            |------------------------------------------
            */

            $failedCount = EmailRecipient::where('college_id', $college->id)
                ->where('session_id', $activeSessionId)
                ->where('status', 'failed')
                ->count();

            $totalCount = EmailRecipient::where('college_id', $college->id)
                ->where('session_id', $activeSessionId)
                ->count();

            if ($failedCount > 0) {

                $actions = '
                    <button class="btn btn-sm btn-warning retry-single" data-id="'.$college->id.'">
                        Retry
                    </button>
                ';

            } elseif ($totalCount == 0) {

                $actions = '
                    <button class="btn btn-sm btn-primary send-single" data-id="'.$college->id.'">
                        Send
                    </button>
                ';

            } else {

                // $actions = '<span class="badge bg-success">Completed</span>';
                $actions = '
                    <button class="btn btn-sm btn-primary send-single" data-id="'.$college->id.'">
                        Send
                    </button>
                ';
            }
            // $actions = '
            //     <button class="btn btn-sm btn-primary send-single" data-id="'.$college->id.'">
            //         Send
            //     </button>

            //     <button class="btn btn-sm btn-warning retry-single" data-id="'.$college->id.'">
            //         Retry
            //     </button>
            // ';
            $rowNum = $start + $index + 1;

            return [
                $checkbox,
                $rowNum,
                e($college->full_name),
                $emailCount,
                $sentTo,
                $status,
                $actions
            ];
        });
    }

    $colleges = College::orderBy('college_name','asc')->get();
    $states = State::orderBy('name')->get();

    $districtsGrouped = District::with('state')
        ->orderBy('name')
        ->get()
        ->groupBy('state_id');

    return view('college_emails.index', compact('colleges','states','districtsGrouped'));
}
    public function index2(Request $request)
    {
        if ($request->ajax()) {

            $activeSessionId = session('admin_session_id');

            $query = College::with(['state', 'district'])
                ->withCount([
                    'emailRecipients as email_count' => function ($q) use ($activeSessionId) {
                        $q->where('session_id', $activeSessionId);
                    }
                ])
                ->with([
                    'latestEmailForSession' => function ($q) use ($activeSessionId) {
                        $q->where('session_id', $activeSessionId);
                    }
                ]);

            return datatables()->of($query)

                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="record_checkbox" value="' . $row->id . '">';
                })

                ->addColumn('college_name', function ($row) {
                    return $row->full_name;
                })

                ->addColumn('email_count', function ($row) {
                    return $row->email_count ?? 0;
                })

                ->addColumn('sent_to', function ($row) use ($activeSessionId) {

                    $types = EmailRecipient::where('college_id', $row->id)
                        ->where('session_id', $activeSessionId)
                        ->select('type')
                        ->distinct()
                        ->pluck('type')
                        ->toArray();

                    if (empty($types)) return '-';
                    if (count($types) == 2) return 'Both';

                    return strtoupper($types[0]);
                })

                ->addColumn('status', function ($row) {

                    if (!$row->latestEmailForSession) return '-';

                    $status = $row->latestEmailForSession->status;

                    $color = match ($status) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'secondary'
                    };

                    return '<span class="badge bg-' . $color . '">' . ucfirst($status) . '</span>';
                })

                ->addColumn('action', function ($row) {

                    return '
                        <button class="btn btn-sm btn-primary send-single" data-id="' . $row->id . '">
                            Send Email
                        </button>

                        <button class="btn btn-sm btn-warning retry-single" data-id="' . $row->id . '">
                            Retry
                        </button>
                    ';
                })

                ->rawColumns(['checkbox', 'status', 'action'])
                ->make(true);
        }

        return view('college_emails.index');
    }

    /*
    |--------------------------------------------------
    | 2. STORE SELECTED COLLEGES (SESSION)
    |--------------------------------------------------
    */
    public function storeSelection(Request $request)
    {
        $ids = $request->ids ?? [];

        if (empty($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'No colleges selected'
            ]);
        }

        session(['selected_colleges' => $ids]);

        return response()->json(['status' => true]);
    }

    /*
    |--------------------------------------------------
    | 3. CREATE PAGE (REFINEMENT)
    |--------------------------------------------------
    */
    public function create()
    {
        $selectedIds = session('selected_colleges', []);

        if (empty($selectedIds)) {
            return redirect()->route('admin.college-emails.index')
                ->with('error', 'Please select colleges first');
        }

        $colleges = College::with(['hod.emails'])
            ->whereIn('id', $selectedIds)
            ->get();

        $purposes = EmailPurpose::where('is_active', 1)->get();
        $senders  = EmailSender::where('is_active', 1)->get();

        return view('college_emails.create', compact('colleges', 'purposes', 'senders'));
    }

    /*
    |--------------------------------------------------
    | 4. STORE (FINAL SEND)
    |--------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'purpose_id' => 'required|exists:college_email_purposes,id',
            'sender_id' => 'required|exists:college_email_senders,id',
            'subject' => 'required|string|max:500',
        ]);

        $activeSessionId = session('admin_session_id');
        $selectedColleges = session('selected_colleges', []);

        if (empty($selectedColleges)) {
            return back()->with('error', 'No colleges selected');
        }

        DB::beginTransaction();

        try {
            $template = 'college_emails.college_visit';
            // $template = $request->template ?? 'college_emails.college_visit';

            $campaign = EmailCampaign::create([
                'purpose_id' => $request->purpose_id,
                'sender_id' => $request->sender_id,
                'session_id' => $activeSessionId,
                'subject' => $request->subject,
                'body' => $request->body,
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
            ]);

            $totalRecipients = 0;

            $colleges = College::with('hod')->whereIn('id', $selectedColleges)->get();

            foreach ($colleges as $college) {

                if (!$college->hod) continue;

                $hod = $college->hod;

                $types = $request->types[$college->id] ?? [];

                foreach ($types as $type) {

                    if ($type === 'hod') {
                        $emailObj = $hod->firstHodEmail;
                        $name = $hod->hod_name;
                    } else {
                        $emailObj = $hod->firstTpoEmail;
                        $name = $hod->tpo_name;
                    }

                    if (!$emailObj || !$emailObj->email) continue;

                    $recipient = EmailRecipient::create([
                        'campaign_id' => $campaign->id,
                        'session_id' => $activeSessionId,
                        'college_id' => $college->id,
                        'hod_id' => $hod->id,
                        'hod_email_id' => $emailObj->id,
                        'email' => $emailObj->email,
                        'recipient_name' => $name,
                        'type' => $type,
                        'status' => 'pending',
                        'meta' => [
                            'template' => $template
                        ],
                    ]);

                    $totalRecipients++;
                }
            }

            $campaign->update([
                'total_recipients' => $totalRecipients
            ]);

            DB::commit();

            // 🔥 Instant Send (your existing logic)
            $recipients = EmailRecipient::where('campaign_id', $campaign->id)->get();

            foreach ($recipients as $recipient) {
                try {
                    (new SendCollegeEmailJob($recipient))->handle();
                    sleep(1);
                } catch (\Exception $e) {
                    // log optional
                }
            }

            session()->forget('selected_colleges');

            return redirect()->route('admin.college-emails.index')
                ->with('success', 'Emails sent successfully!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------
    | 5. CAMPAIGNS LIST
    |--------------------------------------------------
    */
    public function campaigns()
    {
        $activeSessionId = session('admin_session_id');

        $campaigns = EmailCampaign::with('purpose', 'sender')
            ->where('session_id', $activeSessionId)
            ->latest()
            ->paginate(20);

        return view('college_emails.campaigns', compact('campaigns'));
    }

    /*
    |--------------------------------------------------
    | 6. RETRY FAILED (CAMPAIGN)
    |--------------------------------------------------
    */
    public function retry(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:college_email_campaigns,id'
        ]);

        $activeSessionId = session('admin_session_id');

        $recipients = EmailRecipient::where('campaign_id', $request->campaign_id)
            ->where('session_id', $activeSessionId)
            ->where('status', 'failed')
            ->get();

        foreach ($recipients as $recipient) {

            $recipient->update([
                'status' => 'pending',
                'error_message' => null
            ]);

            try {
                (new SendCollegeEmailJob($recipient))->handle();
                sleep(1);
            } catch (\Exception $e) {
                // log
            }
        }

        return back()->with('success', 'Retry completed.');
    }

    /*
    |--------------------------------------------------
    | 7. RETRY FAILED (SINGLE COLLEGE)
    |--------------------------------------------------
    */
    public function retryByCollege(Request $request)
    {
        $request->validate([
            'college_id' => 'required|exists:colleges,id'
        ]);

        $activeSessionId = session('admin_session_id');

        $recipients = EmailRecipient::where('college_id', $request->college_id)
            ->where('session_id', $activeSessionId)
            ->where('status', 'failed')
            ->get();

        foreach ($recipients as $recipient) {

            $recipient->update([
                'status' => 'pending',
                'error_message' => null
            ]);

            try {
                (new SendCollegeEmailJob($recipient))->handle();
                sleep(1);
            } catch (\Exception $e) {
                // log
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Retry completed'
        ]);
    }

    public function logs($collegeId)
    {
        $activeSessionId = session('admin_session_id');

        $college = College::findOrFail($collegeId);

        $recipients = EmailRecipient::with('campaign', 'hod')
            ->where('college_id', $collegeId)
            ->where('session_id', $activeSessionId)
            ->latest()
            ->paginate(20);

        return view('college_emails.logs', compact('recipients', 'college'));
    }

    public function view($id)
    {
        $recipient = EmailRecipient::with('campaign.purpose', 'campaign.sender', 'college', 'hod')
            ->findOrFail($id);

        return view('college_emails.view', compact('recipient'));
    }

    public function oldbulkMarkManuallySent(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:colleges,id',
        ]);

        $activeSessionId = session('admin_session_id');

        DB::transaction(function () use ($request, $activeSessionId) {

            $recipients = EmailRecipient::whereIn(
                    'college_id',
                    $request->ids
                )
                ->where('session_id', $activeSessionId)
                ->where('status', '!=', 'sent')
                ->get();

            foreach ($recipients as $recipient) {

                $meta = $recipient->meta ?? [];

                $meta['manually_sent'] = true;
                $meta['manually_sent_at'] = now()->toDateTimeString();

                $recipient->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'error_message' => null,
                    'meta' => $meta,
                ]);

                EmailCampaign::where('id', $recipient->campaign_id)
                    ->increment('sent_count');
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Selected emails marked as manually sent.',
        ]);
    }

    public function bulkMarkManuallySent(Request $request)
{
    $request->validate([
        'ids' => 'required|array|min:1',
        'ids.*' => 'integer|exists:colleges,id',
    ]);

    $activeSessionId = session('admin_session_id');

    if (!$activeSessionId) {
        return response()->json([
            'status' => false,
            'message' => 'No active session selected.'
        ], 422);
    }

    DB::beginTransaction();

    try {

        /*
         * Fixed details for manually sent email
         */
        $purposeId = 1;
        $senderId  = 1;
        $subject   = 'Workshop';

        $template = 'college_emails.college_visit';

        /*
         * Create ONE NEW CAMPAIGN.
         *
         * This represents one manual sending activity.
         */
        $campaign = EmailCampaign::create([
            'purpose_id'       => $purposeId,
            'sender_id'        => $senderId,
            'session_id'       => $activeSessionId,
            'subject'          => $subject,
            'body'             => null,
            'total_recipients' => 0,
            'sent_count'       => 0,
            'failed_count'     => 0,
        ]);

        $totalRecipients = 0;

        $colleges = College::with('hod')
            ->whereIn('id', $request->ids)
            ->get();

       foreach ($colleges as $college) {

    $hod = $college->hod;

    /*
     * Find previous email history for this college.
     */
    $latestRecipients = EmailRecipient::where('college_id', $college->id)
        ->where('session_id', $activeSessionId)
        ->latest('id')
        ->get();

    /*
     * If previous history exists:
     * use the same recipient types again.
     *
     * Example:
     * HOD
     * HOD + TPO
     */
    if ($latestRecipients->isNotEmpty()) {

        $types = $latestRecipients
            ->pluck('type')
            ->unique()
            ->values();

    } else {

        /*
         * No previous history.
         *
         * Since this is a manual send, create
         * one HOD history record by default.
         *
         * This also works when HOD does not exist.
         */
        $types = collect(['hod']);
    }

    foreach ($types as $type) {

        /*
         * Default values for manual entry.
         */
        $email = 'manual@system.com';
        $recipientName = 'Manual Entry';

        $emailObj = null;
        $hodId = null;

        /*
         * If HOD record exists, use it.
         */
        if ($hod) {

            $hodId = $hod->id;

            if ($type === 'hod') {

                $emailObj = $hod->firstHodEmail;
                $name = $hod->hod_name;

            } elseif ($type === 'tpo') {

                $emailObj = $hod->firstTpoEmail;
                $name = $hod->tpo_name;

            } else {

                continue;
            }

            /*
             * If real email exists, use it.
             * Otherwise keep manual entry.
             */
            if ($emailObj && $emailObj->email) {

                $email = $emailObj->email;
                $recipientName = $name;
            }
        }

        /*
         * CREATE EMAIL HISTORY
         *
         * No actual email is sent.
         */
        EmailRecipient::create([

            'campaign_id'    => $campaign->id,
            'session_id'     => $activeSessionId,
            'college_id'     => $college->id,

            // NULL when HOD does not exist
            'hod_id'         => $hodId,

            // NULL when actual email record does not exist
            'hod_email_id'   => $emailObj?->id,

            'email'          => $email,
            'recipient_name' => $recipientName,
            'type'           => $type,

            'status'         => 'sent',
            'sent_at'        => now(),

            'error_message'  => null,

            'meta' => [
                'template'         => $template,
                'manually_sent'    => true,
                'manually_sent_at' => now()->toDateTimeString(),
            ],
        ]);

        $totalRecipients++;
    }
}

        /*
         * Update campaign statistics.
         */
        $campaign->update([
            'total_recipients' => $totalRecipients,
            'sent_count'       => $totalRecipients,
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => $totalRecipients .
                ' recipient(s) marked as manually sent.',
            'campaign_id' => $campaign->id,
            'total_recipients' => $totalRecipients,
            'sent_count' => $totalRecipients,
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}