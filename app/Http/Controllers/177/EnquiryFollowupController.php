<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\EnquiryFollowup;
use App\Models\EnquiryActivity;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnquiryFollowupController extends Controller
{
    public function store(Request $request, Enquiry $enquiry)
    {
        $request->validate([
            'status' => 'required',
            'call_status' => 'required',
            'note' => 'nullable|string',
            'next_followup_date' => 'nullable|date'
        ]);

        // 1️⃣ CREATE FOLLOW-UP ENTRY
        EnquiryFollowup::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => Auth::id(),
            'status' => $request->status,
            'call_status' => $request->call_status,
            'note' => $request->note,
            'next_followup_date' => $request->next_followup_date,
        ]);

        // 2️⃣ LOG FOLLOW-UP ACTIVITY
        EnquiryActivity::create([
            'enquiry_id' => $enquiry->id,
            'user_id' => Auth::id(),
            'type' => 'followup',
            'details' => "Call Status: {$request->call_status}. Note: {$request->note}",
        ]);

        // 3️⃣ UPDATE STATUS + LOG STATUS CHANGE
        $oldStatus = $enquiry->getOriginal('status');

        // Save new status
        $enquiry->update(['status' => $request->status]);

        // Only log if status actually changed
        if ($oldStatus !== $request->status) {
            EnquiryActivity::create([
                'enquiry_id' => $enquiry->id,
                'user_id' => Auth::id(),
                'type' => 'status_change',
                'old_value' => $oldStatus,
                'new_value' => $request->status,
                'details' => "Lead status changed from {$oldStatus} to {$request->status}",
            ]);
        }

         // 4️⃣ IF CONVERTED → ADD TO STUDENTS_DETAIL TABLE
        if (in_array($request->status, ['converted', 'joined'])) {

            $studentExists = Student::where('contact', $enquiry->mobile)
                ->orWhere('email_id', $enquiry->email)
                ->exists();

            if (! $studentExists) {

                Student::create([
                    'student_name'       => $enquiry->name,
                    'f_name'       => '',
                    'email_id'      => $enquiry->email,
                    'contact'     => $enquiry->mobile,
                    'college_name'    => $enquiry->college,
                    // 'study'      => $enquiry->study,
                    // 'semester'   => $enquiry->semester,
                    // 'source'     => 'Enquiry Converted',
                    'enquiry_id' => $enquiry->id,
                    'created_by' => Auth::id(),
                ]);

                // log creation
                EnquiryActivity::create([
                    'enquiry_id' => $enquiry->id,
                    'user_id'    => Auth::id(),
                    'type'       => 'converted_to_student',
                    'details'    => "Lead converted to student and added to students_detail table.",
                ]);
            }
        }

        return back()->with('success', 'Follow-up recorded successfully.');
    }


}
