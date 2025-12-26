<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\EmailCount;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateIssuedMail;
use App\Mail\StudentConfirmationMail;
use App\Models\College;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Department;
use App\Models\Reference;
use App\Models\Duration;
use App\Models\StudentStatus;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;
use Mpdf\Mpdf;

class StudentController extends Controller
{
    // Constructor to apply auth middleware

    // List students
    public function index(Request $request)
    {   
        $notificationMode = $request->notification ?? null;

        $query = Student::query();
        if ($notificationMode === 'registered_today') {
            $query->whereDate('created_at', today());
        }else{
            // Filters
            if ($request->filled('student_name')) {
                $query->where('student_name', 'like', '%' . $request->student_name . '%');
            }
            if ($request->filled('f_name')) {
                $query->where('f_name', 'like', '%' . $request->f_name . '%');
            }
            if ($request->filled('sno')) {
                $query->where('sno', $request->sno);
            }
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('session')) {
                $query->where('session', $request->session);
            }
            if ($request->filled('college_name')) {
                $query->where('college_name', $request->college_name);
            }
            if ($request->filled('email_id')) {
                $query->where('email_id', 'like', '%' . $request->email_id . '%');
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('technology')) {
                $query->where('technology', $request->technology);
            }
            // if ($request->filled('department')) {
            //     $query->where('department', $request->department);
            // }
            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }
            // Filter by pending fee
            if ($request->filled('pending_fees') && $request->pending_fees == 1) {
                $query->where('pending_fees', '>', 0.00);
            }

            if ($request->filled('part_time_offer')) {
                $query->where('part_time_offer', $request->part_time_offer);
            }

            if ($request->filled('placement_offer')) {
                $query->where('placement_offer', $request->placement_offer);
            }

            if ($request->filled('pg_offer')) {
                $query->where('pg_offer', $request->pg_offer);
            }
            
            if (auth()->user()->role == 1) {
                $activeSessionId = session('admin_session_id');
                $query->where('session', $activeSessionId);
            }
            
        }
        
            
            $query->where('certificate_status', 0);
            //dd($request->all());
            $students = $query->paginate(10);

        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        $references = Reference::all();
        $departments = Department::all();
        $users = User::all();
        $student_status = StudentStatus::all();

        //pending fee
        $dismissed = session('dismiss_pending_fee');
        $activeSessionNo = session('admin_session_id');
            
            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionNo)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();
        

        return view('students.index', compact('students','sessions','colleges','courses','batches','references','departments','users','student_status','pendingStudents'));
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    // Show create form
    public function create()
    {
        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        $department = Department::all();
        $references = Reference::all();
        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();

        return view('students.create', compact('sessions','colleges','courses','batches','department','references','users','course_duration','student_status'));
    }

    // Store student
    public function store(Request $request)
    {

        $validate= $request->validate([
            'student_name'   => 'required|string|max:255',
            'f_name'         => 'required|string|max:255',
            'sno'            => 'required|string|max:255',
            'email_id'       => 'required|email|unique:students_detail,email_id',
            'contact'        => 'nullable|string|max:15',
            'gender'         => 'required|string',
            'college_name'   => 'required|string',
            'session'        => 'required|string',
            'status'         => 'required',
            'technology'     => 'required|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'pending_fees'   => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
            'duration'       => 'required',
            'batch_assign'   => 'required|string',
            'reference'      => 'string',
            'reg_due_amount' => 'required|integer',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'required|boolean',
            'placement_offer'  => 'required|boolean',
            'pg_offer'         => 'required|boolean',

        ]);

        Student::create($validate);

        return redirect()->route('students.index')
                         ->with('success','Student added successfully');
    }

    // Show edit form
    public function edit(Student $student)
    {
        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        // $department = Department::all();
        $references = Reference::all();
        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();

        return view('students.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status'));
        // return view('students.edit', compact('student','sessions','colleges','courses','batches','department','references','users'));
    }

    public function update(Request $request, Student $student)
    {
        // dd($request->all());
        $validates = $request->validate([
            'student_name'   => 'required|string|max:255',
            'f_name'         => 'required|string|max:255',
            'sno'            => 'required|string|max:255',
            'email_id'       => 'required|email|unique:students_detail,email_id,'.$student->id,
            'contact'        => 'nullable|string|max:15',
            'gender'         => 'required|string',
            'college_name'   => 'required|string',   // not college_id
            'session'        => 'required|string',   // not session_id
            'technology'     => 'required|string',   // not technology_id
            'batch_assign'   => 'required|string',   // not batch_id
            'reference'      => 'string',   // not reference_user
            'status'         => 'required|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'pending_fees'   => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
            'reg_due_amount' => 'required|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'required|boolean',
            'placement_offer'  => 'required|boolean',
            'pg_offer'         => 'required|boolean',
        ]);
        // dd('Passed validation', $validates);

        $student->update($validates);

        return redirect()->route('students.index')
                        ->with('success','Student updated successfully');
    }


    // Delete student
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
                         ->with('success','Student deleted successfully');
    }

    public function bulkDelete(Request $request)
{
    // If no ids sent (GET request or empty payload), DO NOTHING
    // If no valid IDs are sent, ignore GET request
    if (!$request->filled('ids') || is_array($request->ids)) {
        return back()->with('error', 'No students selected.');
    }

    // Now decode properly
    $ids = json_decode($request->ids, true);

    Student::whereIn('id', $ids)->delete();

    return back()->with('success', 'Selected students deleted.');
}



    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls,csv'
    //     ]);

    //     Excel::import(new StudentsImport, $request->file('file'));

    //     return back()->with('success', 'Students imported successfully!');
    // }

    public function importForm()
    {
        return view('students.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            // 'file' => 'required|file|mimes:xlsx,xls,csv',
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        /** STEP 1: Read Excel headers BEFORE actual import */
        $data =  \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
         // dd('here');
        if (empty($data) || empty($data[0]) || empty($data[0][0])) {
            return back()->with('error', 'Uploaded file is empty or unreadable.');
        }

        // lowercase header keys
        $headers = array_map('strtolower', $data[0][0]);

        // REQUIRED COLUMNS (in correct order)
        $requiredHeaders = [
            'student_name',
            'f_name',
            'sno',
            'email_id',
            'contact',
            'gender',
            'college_name',
            'session',
            'status',
            'technology',
            'total_fees',
            'reg_fees',
            'pending_fees',
            'next_due_date',
            'join_date',
            'duration',
            'batch_assign',
            'reg_due_amount',
            'start_date',
            'end_date',
        ];

        /** 1️⃣ Missing headers? */
        $missing = array_diff($requiredHeaders, $headers);
        if (!empty($missing)) {
            return back()->withErrors([
                "Missing required column(s): " . implode(', ', $missing)
            ]);
        }

        /** 2️⃣ Check required columns order (extra columns allowed) */
        $uploadedRequired = array_values(array_intersect($headers, $requiredHeaders));

        if ($uploadedRequired !== $requiredHeaders) {
            return back()->withErrors([
                "Invalid column order! Required order: " . implode(' → ', $requiredHeaders)
            ]);
        }

        /** STEP 2: Run import */
        try {
            $importer = new \App\Imports\StudentsImport();
             \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

             // Collect duplicate contact errors
            $errors = [];

            if (!empty($importer->duplicateContacts)) {
                foreach ($importer->duplicateContacts as $msg) {
                    $errors[] = $msg;
                }
            }

            if (!empty($importer->duplicateEMail)) {
                foreach ($importer->duplicateEMail as $em) {
                    $errors[] = $em;
                }
            }

            // If importer has other errors (optional)
            if (!empty($importer->errors ?? [])) {
                foreach ($importer->errors as $err) {
                    $errors[] = $err;
                }
            }

            // If any errors exist → show them (but still show success)
            if (!empty($errors)) {
                return back()
                    ->with('success', "Students Imported Successfully!")
                    ->withErrors($errors);
            }

            return back()->with('success', "Students Imported Successfully!");
            // Log file if needed
            $logFile = null;
            if (!empty($importer->errors)) {
                $logFile = 'students-import-log-' . time() . '.txt';
                \Storage::put($logFile, implode("\n", $importer->errors));
            }

            return back()->with('success', "Students Imported Successfully!")
                         ->with('logFile', $logFile);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            // Collect all row validation errors
            $messages = [];
            foreach ($e->failures() as $failure) {
                $messages[] =
                    "Row {$failure->row()}: " .
                    implode(', ', $failure->errors());
            }

            return back()->withErrors($messages);
        }
        catch (\Throwable $e) {
            return back()->withErrors([
                'Import failed: ' . $e->getMessage()
            ]);
        }
    }

    public function confirmStudent(Request $request, $id)
    {
        // $student = Student::findOrFail($id);
        // $student = Student::with('sessionData')->find($id);
        $isInternship = $request->boolean('is_internship');
        // dd($isInternship);
        $student = Student::with(['sessionData', 'durationData','collegeData'])->find($id);

        // 🔥 1. Check if pending fees exists
        $totalPaid = $student->total_fees - $student->pending_fees;

        // If the student has paid nothing
        if ($totalPaid <= 0) {
            return redirect()->back()
                ->with('error', "Cannot Confirm. No fees submitted yet.");
        }

        // 2. Generate PDF dynamically
        $filePath = $this->generateConfirmationPdf($student,$isInternship);
        $receiptPath = $this->generatePaymentReceiptPdf($student);

        // 3. Send email with attachment
        Mail::to($student->email_id)
            ->send(new StudentConfirmationMail($student, $filePath,$receiptPath));

        // 4. Increment student's certificate email count
        $student->increment('email_count_confirmation');
        $student->increment('count_receipt_download');

        // 5. Increment global email count
        $emailCount = EmailCount::firstOrCreate(
            ['email' => $student->email_id],
            ['count' => 0]
        );
        $emailCount->increment('count');

        $student->certificate_status = 0; // confirmed
        $student->save();

        return redirect()->back()->with('success', 'Student confirm and email sent.');
    }

    public function confirmMultiple(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');
        $isInternship = $request->boolean('is_internship');

        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
            // NEW CHECK: Has the student paid anything?
            $totalPaid = $student->total_fees - $student->pending_fees;
             if ($totalPaid <= 0) {
                return back()->with(
                    'error',
                    "Cannot Confirm {$student->student_name}. No payment submitted yet."
                );
            }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

               // 2. Generate PDF dynamically
            $filePath = $this->generateConfirmationPdf($student, $isInternship);
            $receiptPath = $this->generatePaymentReceiptPdf($student);

            // 3. Send email with attachment
            Mail::to($student->email_id)
                ->send(new StudentConfirmationMail($student, $filePath, $receiptPath));

            // Increment counters
            $student->increment('email_count_confirmation');
            $student->increment('count_receipt_download');
            $student->certificate_status = 0; // confirmed
            $student->save();

            EmailCount::firstOrCreate(['email' => $student->email_id], ['count' => 0])->increment('count');
        }

        return back()->with('success', 'Confimation send to selected students.');
    }

    public function downloadconfirmMultiple(Request $request)
    {
        $ids = json_decode($request->ids, true);
         $isInternship = $request->boolean('is_internship');
         // dd($isInternship);
        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'No students selected.');
        }

        // Ensure IDs are integers
        $ids = array_map('intval', $ids);

        $students = Student::whereIn('id', $ids)->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Selected students not found.');
        }

        // Generate (or reuse) PDFs and collect file paths
        $pdfPaths = [];
        foreach ($students as $student) {
            $pdfPath = $this->generateConfirmationPdf($student, $isInternship);

            if (file_exists($pdfPath)) {
                $pdfPaths[] = $pdfPath;
            }
        }

        // If only one PDF, return it directly
        if (count($pdfPaths) === 1) {
            $singlePath = $pdfPaths[0];
            $downloadName = basename($singlePath);

            // Return the single PDF with proper headers
            return response()->download($singlePath, $downloadName, [
                'Content-Type' => 'application/pdf'
            ]);
        }

        // Otherwise create ZIP
        $zipFileName = 'confirmation_letters_' . time() . '.zip';
        $zipFullPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($pdfPaths as $path) {
                // Use a friendlier name inside zip (only filename)
                $zip->addFile($path, basename($path));
            }
            $zip->close();

            // Return and delete zip after download
            return response()->download($zipFullPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP file.');
    }

    public function downloadMultipleReceipts(Request $request)
    {
        $ids = json_decode($request->ids, true);

        if (!$ids || count($ids) == 0) {
            return back()->with('error', 'No students selected.');
        }

        // Fetch students
        $students = Student::whereIn('id', $ids)->get();

        // -------------------------------------------------------
        // 🔥 If only ONE student is selected → download PDF directly
        // -------------------------------------------------------
        if ($students->count() === 1) {

            $student = $students->first();

            // Generate PDF
            $pdfPath = $this->generatePaymentReceiptPdf($student);

            if (!file_exists($pdfPath)) {
                return back()->with('error', 'Receipt could not be generated.');
            }

            $student->increment('count_receipt_download');
            return response()->download(
                $pdfPath,
                basename($pdfPath),
                ['Content-Type' => 'application/pdf']
            );
        }

        // -------------------------------------------------------
        // 🔥 If MULTIPLE students → ZIP all receipts
        // -------------------------------------------------------
        $zipFileName = 'payment_receipts_' . time() . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {

            foreach ($students as $student) {

                $pdfPath = $this->generatePaymentReceiptPdf($student);

                if (file_exists($pdfPath)) {
                    $zip->addFile($pdfPath, basename($pdfPath));
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadCertificateMultiple(Request $request)
    {
        $ids = json_decode($request->ids, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'No students selected.');
        }

        // Ensure IDs are integers
        $ids = array_map('intval', $ids);

        $students = Student::whereIn('id', $ids)->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Selected students not found.');
        }

        // Generate (or reuse) PDFs and collect file paths
        $pdfPaths = [];
        foreach ($students as $student) {
            // $pdfPath = $this->generateConfirmationPdf($student);
            $pdfPath = $this->generatePdf($student);

            if (file_exists($pdfPath)) {
                $pdfPaths[] = $pdfPath;
            }
        }

        // If only one PDF, return it directly
        if (count($pdfPaths) === 1) {
            $singlePath = $pdfPaths[0];
            $downloadName = basename($singlePath);

            // Return the single PDF with proper headers
            return response()->download($singlePath, $downloadName, [
                'Content-Type' => 'application/pdf'
            ]);
        }

        // Otherwise create ZIP
        $zipFileName = 'certificate_letters_' . time() . '.zip';
        $zipFullPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($pdfPaths as $path) {
                // Use a friendlier name inside zip (only filename)
                $zip->addFile($path, basename($path));
            }
            $zip->close();

            // Return and delete zip after download
            return response()->download($zipFullPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP file.');
    }

    public function moveMultipleToCertificate(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');

        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
            // NEW CHECK: Has the student paid anything?
            $totalPaid = $student->total_fees - $student->pending_fees;
             if ($totalPaid <= 0) {
                return back()->with(
                    'error',
                    "Cannot Shift {$student->student_name}. No payment submitted yet."
                );
            }

            if($student->count_receipt_download == 0){
                return back()->with(
                    'error',
                    "Cannot Shift {$student->student_name}. No payment slip downloaded yet."
                );
            }

            if($student->email_count_confirmation == 0){
                return back()->with(
                    'error',
                    "Cannot Shift {$student->student_name}. No Confirmation Letter downloaded yet."
                );
            }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

            $student->certificate_status = 1; // confirmed
            $student->save();
        }

        return back()->with('success', 'Student send to Certificate Section.');
    }


    public function issueCertificate($id)
    {
        // $student = Student::findOrFail($id);
        $student = Student::with(['sessionData', 'durationData','collegeData','courseData'])->find($id);

        // 🔥 1. Check if pending fees exists
        if ($student->pending_fees > 0) {
            return redirect()->back()->with('error', "Cannot Confirm. Pending fees: ₹{$student->pending_fees}");
        }

        // 2. Generate PDF dynamically
        $filePath = $this->generatePdf($student);

        // 3. Send email with attachment
        Mail::to($student->email_id)
            ->send(new CertificateIssuedMail($student, $filePath));

        // 4. Increment student's certificate email count
        $student->increment('email_count_certificate');

        // 5. Increment global email count
        $emailCount = EmailCount::firstOrCreate(
            ['email' => $student->email_id],
            ['count' => 0]
        );
        $emailCount->increment('count');

        $student->certificate_status = 2; // certificate send
        $student->certificate_send_date = now();
        $student->close_date = now();
        $student->save();

        return redirect()->back()->with('success', 'Certificate issued and email sent.');
    }

    public function issueMultiple(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');

        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData','courseData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
            if ($student->pending_fees > 0) {
                return back()->with('error', "Cannot Confirm {$student->student_name}. Pending fees: ₹{$student->pending_fees}");
            }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

            //return view('pdf.student_certificate', compact('student'));
            $filePath = $this->generatePdf($student);
            //die();
            // Send email
            Mail::to($student->email_id)->send(new CertificateIssuedMail($student, $filePath));

            // Increment counters
            $student->increment('email_count_certificate');
            $student->certificate_status = 2; // certificate send
            $student->certificate_send_date = now();
            // Only set close_date if it is currently NULL
            if (is_null($student->close_date)) {
                $student->close_date = now();
            }
            $student->save();

            EmailCount::firstOrCreate(['email' => $student->email_id], ['count' => 0])->increment('count');
        }

        return back()->with('success', 'Certificates issued to selected students.');
    }

    private function generateConfirmationPdf($student, $isInternship = false)
    {
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("studentConfirmation/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }

        // Generate or overwrite PDF if needed

        if ($regenerate) {
            
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);
        
            $mpdf->SetHTMLHeader($this->getPDFHeader());
            $mpdf->SetHTMLFooter($this->getPDFFooter());

            $html = view('pdf.confirmation_detail', compact('student','isInternship'))->render();
        
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, 'F');
            //return response()->download($filePath);
        }

         return $filePath;
        // if ($regenerate) {
        //     $pdf = Pdf::loadView('pdf.confirmation_detail', ['student' => $student])
        //               ->setPaper('a4', 'portrait')  // or 'portrait'
        //               ->setOption('dpi', 150)        // higher resolution
        //               ->setOption('defaultFont', 'sans-serif');

        //     $pdf->save($filePath);
        // }
        return $filePath;
    }


    function getPDFHeader()
    {
        return '<div style="position: fixed; top: -35px;" class="head-shape">
            <img src="'. public_path('images/confirmation_images/head-shape.png').'"/>
        </div>';
    }



    function getPDFFooter()
    {
        return '<div style="position: fixed; bottom: -35px;" class="ct-footer-shape">
                    <img src="'.public_path('images/confirmation_images/footer-shape-1.png').'"/>
                </div>';
    }


    private function generateConfirmationPdf_22dec($student)
    {
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("studentConfirmation/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }

        // Generate or overwrite PDF if needed
        if ($regenerate) {
            $pdf = Pdf::loadView('pdf.confirmation_detail', ['student' => $student])
                      ->setPaper('a4', 'portrait')  // or 'portrait'
                      ->setOption('dpi', 150)        // higher resolution
                      ->setOption('defaultFont', 'sans-serif');

            $pdf->save($filePath);
        }

        


        return $filePath;
    }

private function generatePdf($student)
    {
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("student_certificate/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }
        // echo $filePath;
        // Generate or overwrite PDF if needed
        if ($regenerate) {
            
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);
        
            $mpdf->SetHTMLHeader($this->getPDFHeader());

            $mpdf->SetHTMLFooter($this->getPDFFooter());

            $html = view('pdf.student_certificate', compact('student'))->render();
        
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, 'F');
            //return response()->download($filePath);
        }

        return $filePath;
    }
    
    private function generatePdf_old($student)
    {
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("student_certificate/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }

        // $html = view('pdf.certificate_detail', [
        //     'student' => $student
        // ])->render();
        // echo $html;die;
        // dd($html);
        // Generate or overwrite PDF if needed
        if ($regenerate) {
            // $pdf = Pdf::loadView('pdf.student_certificate', ['student' => $student])
            //           ->setPaper('a4', 'portrait')  // or 'portrait'
            //           ->setOption('dpi', 150)        // higher resolution
            //           ->setOption('defaultFont', 'sans-serif');
            // return $pdf->stream('student-certificate.pdf');
            // return $pdf->download('student-certificate.pdf');
            $pdf->save($filePath);
        }

        return $filePath;
    }


   private function generatePaymentReceiptPdf($student)
    {
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $folderPath = public_path("paymentReceipts/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Receipt No.
        $receiptNumber = strtoupper(uniqid("RCT"));

        // Payment amount (you can change this)
        $amount = $student->reg_fees;

        // Convert amount to words
        $amountInWords = ucwords(
            (new \NumberFormatter('en', \NumberFormatter::SPELLOUT))->format($amount)
        );

        // PDF Name
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->student_name);
        $fileName = $student->id . '_' . $safeName . '_receipt.pdf';
        $filePath = $folderPath . '/' . $fileName;

        // Render PDF with ALL dynamic values
        // $pdf = \PDF::loadView('pdf.payment_receipt', [
        //     'student'        => $student,
        //     'receiptNumber'  => $receiptNumber,
        //     'amount'         => $amount,
        //     'amountInWords'  => $amountInWords,
        //     'payment_mode'   => 'Cash',   // default
        //     'transaction_no' => 'N/A',    // default
        // ])
        // ->setPaper('a4')
        // ->setOption('dpi', 150)
        // ->setOption('defaultFont', 'sans-serif');

        // $pdf->save($filePath);


        $mpdf = new Mpdf([
            'mode'           => 'utf-8',
            'format'         => [5.5, 4.25]
            'orientation'    => 'L',
            'margin_left'    => 10,
            'margin_right'   => 10,
            'margin_top'     => 10,
            'margin_bottom'  => 10,
            'default_font'   => 'sans-serif',
            'dpi'            => 150,
        ]);

        // Render Blade view to HTML
        $html = view('pdf.payment_receipt', [
            'student'        => $student,
            'receiptNumber'  => $receiptNumber,
            'amount'         => $amount,
            'amountInWords'  => $amountInWords,
            'payment_mode'   => 'Cash',
            'transaction_no' => 'N/A',
        ])->render();

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Save PDF to file path
        $mpdf->Output($filePath, 'F');


        return $filePath;
    }

    private function generatePaymentReceiptPdf22dec($student)
    {
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $folderPath = public_path("paymentReceipts/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Receipt No.
        $receiptNumber = strtoupper(uniqid("RCT"));

        // Payment amount (you can change this)
        $amount = $student->reg_fees;

        // Convert amount to words
        $amountInWords = ucwords(
            (new \NumberFormatter('en', \NumberFormatter::SPELLOUT))->format($amount)
        );

        // PDF Name
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->student_name);
        $fileName = $student->id . '_' . $safeName . '_receipt.pdf';
        $filePath = $folderPath . '/' . $fileName;

        // Render PDF with ALL dynamic values
        $pdf = \PDF::loadView('pdf.payment_receipt', [
            'student'        => $student,
            'receiptNumber'  => $receiptNumber,
            'amount'         => $amount,
            'amountInWords'  => $amountInWords,
            'payment_mode'   => 'Cash',   // default
            'transaction_no' => 'N/A',    // default
        ])
        ->setPaper('a4')
        ->setOption('dpi', 150)
        ->setOption('defaultFont', 'sans-serif');

        $pdf->save($filePath);

        return $filePath;
    }


    public function managerIndex(Request $request)
    {
        $query = Student::query();

        // Filters
        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('f_name')) {
            $query->where('f_name', 'like', '%' . $request->f_name . '%');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session); // use 'session' not session_id
        }

        if ($request->filled('college_name')) {
            $query->where('college_name', $request->college_name);
        }

        if ($request->filled('email_id')) {
            $query->where('email_id', 'like', '%' . $request->email_id . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('technology')) {
            $query->where('technology', $request->technology);
        }

        // if ($request->filled('department')) {
        //     $query->where('department', $request->department);
        // }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $query->where('pending_fees', '>', 0.00);
        }



        $students = $query->paginate(10);

        return view('manager.student', [
            'students'    => $students, // use pagination
            'colleges'    => College::all(),
            'sessions'    => StudentSession::all(),
            'courses'     => Course::all(),
            'batches'     => Batch::all(),
            'users'       => User::all(),
            'departments' => Department::all(),
            'reference'   => Reference::all(),
        ]);
    }

    public function salesIndex(Request $request)
    {
        $query = Student::query();

        // Filters
        if ($request->filled('student_name')) {
            $query->where('student_name', 'like', '%' . $request->student_name . '%');
        }

        if ($request->filled('f_name')) {
            $query->where('f_name', 'like', '%' . $request->f_name . '%');
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session); // use 'session' not session_id
        }

        if ($request->filled('college_name')) {
            $query->where('college_name', $request->college_name);
        }

        if ($request->filled('email_id')) {
            $query->where('email_id', 'like', '%' . $request->email_id . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('technology')) {
            $query->where('technology', $request->technology);
        }

        // if ($request->filled('department')) {
        //     $query->where('department', $request->department);
        // }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $query->where('pending_fees', '>', 0.00);
        }



        $students = $query->paginate(10);

        return view('sales.student', [
            'students'    => $students, // use pagination
            'colleges'    => College::all(),
            'sessions'    => StudentSession::all(),
            'courses'     => Course::all(),
            'batches'     => Batch::all(),
            'users'       => User::all(),
            'departments' => Department::all(),
            'reference'   => Reference::all(),
        ]);
    }

    public function pendingFees()
    {
        $activeSessionId = session('admin_session_id');

        $students = Student::where('pending_fees', '>', 0)
                           
                           ->where('certificate_status', 1)
                           ->where('session', $activeSessionId)
                           ->paginate(50);
                           
        return view('pending_fees', compact('students'));
    }

    public function closingList()
    {
        $activeSessionId = session('admin_session_id');

        $students = Student::where('pending_fees', 0)
                           ->where('certificate_status', 2)
                           ->where('email_count_certificate','>' ,0)
                           ->where('session', $activeSessionId)
                           ->paginate(50);

        return view('closing_list', compact('students'));
    }

    public function pendingFeesold()
    {

        $students = Student::where('pending_fees', '>', 0)
                           ->whereDate('next_due_date', '<=', now())
                           ->paginate(10);

        return view('pending_fees', compact('students'));
    }


    //Pending STudent whose session not added
     public function pendingStudents(Request $request)
    {   
        $notificationMode = $request->notification ?? null;

        $query = Student::query();
        
        $query->where(function ($q) {
            $q->whereNull('session')
              ->orWhere('session', '');
        });
        
        $students = $query->paginate(100);

        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        $references = Reference::all();
        $departments = Department::all();
        $users = User::all();
        $student_status = StudentStatus::all();

        //pending fee
        $dismissed = session('dismiss_pending_fee');
        $activeSessionNo = session('admin_session_id');
            
            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionNo)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();
        

        return view('students.pending_student_index', compact('students','sessions','colleges','courses','batches','references','departments','users','student_status','pendingStudents'));
    }

}