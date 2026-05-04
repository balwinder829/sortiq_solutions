<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Hod;
use App\Models\HodEmail;
use App\Models\EmailPurpose;
use App\Models\EmailSender;
use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Jobs\SendCollegeEmailJob;
use App\Http\DataTables\DataTablesServerSide;

use Illuminate\Support\Facades\DB;

class OldCollegeEmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. LIST (Row-wise with filters)
    |--------------------------------------------------------------------------
    */
public function index(Request $request)
{
    if ($request->ajax()) {

        $query = EmailRecipient::with('college');

        if ($request->college_id) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        $query->orderBy('sent_at', 'desc');

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id','email','type','status','sent_at'],
            'searchable' => ['email','status','type'],
        ], function ($row, $index, $start) {

            return [
                'id' => $row->id,
                'college_name' => optional($row->college)->full_name ?? '-',
                'email' => $row->email,
                'type' => '<span class="badge bg-info">'.e(ucfirst($row->type)).'</span>',
                'status' => '<span class="badge bg-secondary">'.e(ucfirst($row->status)).'</span>',
                'sent_at' => $row->sent_at ? $row->sent_at->format('d M Y h:i A') : '-',
            ];
        });
    }

    $colleges = College::all();

    return view('college_emails.index', compact('colleges'));
}
    public function indexas(Request $request)
{
    if ($request->ajax()) {

        $query = EmailRecipient::with('college');

        if ($request->college_id) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        return datatables()->of($query)
            ->addColumn('college_name', function ($row) {
                return $row->college->full_name ?? '-';
            })
            ->editColumn('sent_at', function ($row) {
                return $row->sent_at ? $row->sent_at->format('d M Y H:i') : '-';
            })
            ->make(true);
    }

    $colleges = College::all();

    return view('college_emails.index', compact('colleges'));
}
    

    /*
    |--------------------------------------------------------------------------
    | 2. CREATE FORM
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $colleges = College::with('hod.emails')->get();
        $purposes = EmailPurpose::where('is_active', 1)->get();
        $senders = EmailSender::where('is_active', 1)->get();

        return view('college_emails.create', compact('colleges', 'purposes', 'senders'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. STORE (MAIN LOGIC)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
{
    $request->validate([
        'college_ids' => 'required|array',
        'type' => 'required|in:hod,tpo,both',
        'purpose_id' => 'required|exists:college_email_purposes,id',
        'sender_id' => 'required|exists:college_email_senders,id',
        'subject' => 'required|string|max:500',
        // 'body' => 'required|string',
    ]);

    /*
    |--------------------------------------------------------------------------
    | 🔴 STEP 0: VALIDATE BEFORE CREATING CAMPAIGN
    |--------------------------------------------------------------------------
    */

    $colleges = College::with('hod.emails')
        ->whereIn('id', $request->college_ids)
        ->get();

    $invalidColleges = [];

    foreach ($colleges as $college) {

        if (!$college->hod) {
            $invalidColleges[] = $college->full_name;
            continue;
        }

        $hod = $college->hod;

        if ($request->type === 'hod') {
            $emails = $hod->hodEmails;
        } elseif ($request->type === 'tpo') {
            $emails = $hod->tpoEmails;
        } else {
            $emails = $hod->emails;
        }

        if ($emails->isEmpty()) {
            $invalidColleges[] = $college->full_name;
        }
    }

    // ❌ STOP if invalid found
    if (!empty($invalidColleges)) {
        return back()
            ->withInput()
            ->with('error', 'Please add email for the following colleges before sending:<br><br>• ' . implode('<br>• ', $invalidColleges));
    }

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | Step 1: Create Campaign
        |--------------------------------------------------------------------------
        */
        $campaign = EmailCampaign::create([
            'purpose_id' => $request->purpose_id,
            'sender_id' => $request->sender_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'total_recipients' => 0,
            'sent_count' => 0,
            'failed_count' => 0,
        ]);

        $totalRecipients = 0;

        /*
        |--------------------------------------------------------------------------
        | Step 2: Create Recipients
        |--------------------------------------------------------------------------
        */
        foreach ($colleges as $college) {

            $hod = $college->hod;

            if ($request->type === 'hod') {
                $emails = $hod->hodEmails;
            } elseif ($request->type === 'tpo') {
                $emails = $hod->tpoEmails;
            } else {
                $emails = $hod->emails;
            }

            foreach ($emails as $email) {

                if (!$email->email) continue;

                EmailRecipient::create([
                    'campaign_id' => $campaign->id,
                    'college_id' => $college->id,
                    'hod_id' => $hod->id,
                    'hod_email_id' => $email->id,
                    'email' => $email->email,
                    'recipient_name' => $hod->hod_name ?? $hod->tpo_name,
                    'type' => $email->type,
                    'status' => 'pending',
                ]);

                $totalRecipients++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Step 3: Update Campaign Count
        |--------------------------------------------------------------------------
        */
        $campaign->update([
            'total_recipients' => $totalRecipients
        ]);

        DB::commit();

        /*
        |--------------------------------------------------------------------------
        | Step 4: Dispatch Jobs
        |--------------------------------------------------------------------------
        */
        $recipients = EmailRecipient::where('campaign_id', $campaign->id)->get();

        foreach ($recipients as $recipient) {
            // SendCollegeEmailJob::dispatch($recipient)->delay(now()->addSeconds(2));
            try {

                    (new SendCollegeEmailJob($recipient))->handle();

                    sleep(1); // optional (recommended to avoid SMTP limits)

                } catch (\Exception $e) {

                    // \Log::error('Email send failed: ' . $e->getMessage());
                }
        }

        return redirect()->route('admin.college-emails.index')
            ->with('success', 'Email campaign created successfully!');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
    public function storeold(Request $request)
    {
        $request->validate([
            'college_ids' => 'required|array',
            'type' => 'required|in:hod,tpo,both',
            'purpose_id' => 'required|exists:college_email_purposes,id',
            'sender_id' => 'required|exists:college_email_senders,id',
            'subject' => 'required|string|max:500',
            'body' => 'required|string',
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Step 1: Create Campaign
            |--------------------------------------------------------------------------
            */
            $campaign = EmailCampaign::create([
                'purpose_id' => $request->purpose_id,
                'sender_id' => $request->sender_id,
                'subject' => $request->subject,
                'body' => $request->body,
                'total_recipients' => 0,
                'sent_count' => 0,
                'failed_count' => 0,
            ]);

            $totalRecipients = 0;

            /*
            |--------------------------------------------------------------------------
            | Step 2: Fetch Colleges + Emails
            |--------------------------------------------------------------------------
            */
            $colleges = College::with('hod.emails')
                ->whereIn('id', $request->college_ids)
                ->get();

            foreach ($colleges as $college) {

                if (!$college->hod) {
                    continue;
                }

                $hod = $college->hod;

                $emails = collect();

                // Select type
                if ($request->type === 'hod') {
                    $emails = $hod->hodEmails;
                } elseif ($request->type === 'tpo') {
                    $emails = $hod->tpoEmails;
                } else {
                    $emails = $hod->emails;
                }

                foreach ($emails as $email) {

                    if (!$email->email) continue;

                    EmailRecipient::create([
                        'campaign_id' => $campaign->id,
                        'college_id' => $college->id,
                        'hod_id' => $hod->id,
                        'hod_email_id' => $email->id,
                        'email' => $email->email,
                        'recipient_name' => $hod->hod_name ?? $hod->tpo_name,
                        'type' => $email->type,
                        'status' => 'pending',
                    ]);

                    $totalRecipients++;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Step 3: Update Campaign Count
            |--------------------------------------------------------------------------
            */
            $campaign->update([
                'total_recipients' => $totalRecipients
            ]);

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Step 4: Dispatch Jobs (NEXT STEP)
            |--------------------------------------------------------------------------
            */
            $recipients = EmailRecipient::where('campaign_id', $campaign->id)->get();

            foreach ($recipients as $recipient) {
                // SendCollegeEmailJob::dispatch($recipient);
                SendCollegeEmailJob::dispatch($recipient)->delay(now()->addSeconds(2));
            }

            return redirect()->route('admin.college-emails.index')
                ->with('success', 'Email campaign created successfully!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 4. CAMPAIGNS LIST (Group View)
    |--------------------------------------------------------------------------
    */
    public function campaigns()
    {
        $campaigns = EmailCampaign::with('purpose', 'sender')
            ->latest()
            ->paginate(20);

        return view('college_emails.campaigns', compact('campaigns'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5. RETRY FAILED EMAILS
    |--------------------------------------------------------------------------
    */

    public function retry(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:college_email_campaigns,id'
        ]);

        $recipients = EmailRecipient::where('campaign_id', $request->campaign_id)
            ->where('status', 'failed')
            ->get();

        foreach ($recipients as $recipient) {

            $recipient->update([
                'status' => 'pending',
                'error_message' => null
            ]);

            // \App\Jobs\SendCollegeEmailJob::dispatch($recipient);
            SendCollegeEmailJob::dispatch($recipient)->delay(now()->addSeconds(2));
        }

        return back()->with('success', 'Retry started.');
    }


    public function retryold(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:college_email_campaigns,id'
        ]);

        $failedRecipients = EmailRecipient::where('campaign_id', $request->campaign_id)
            ->where('status', 'failed')
            ->get();

        foreach ($failedRecipients as $recipient) {

            // Reset status
            $recipient->update([
                'status' => 'pending'
            ]);

            // Dispatch job later (next step)
        }

        return back()->with('success', 'Retry queued for failed emails.');
    }
}