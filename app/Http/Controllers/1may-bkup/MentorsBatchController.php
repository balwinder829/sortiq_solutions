<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use App\Models\Trainer;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BatchMessageMail;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;

class MentorsBatchController extends Controller
{

    public function show(Batch $batch)
    {
        // Logged-in trainer
        $trainer = Auth::guard('trainer')->user();

        if (!$trainer) {
            abort(403, 'Unauthorized');
        }

        // Check if batch belongs to trainer
        if ($batch->batch_assign != $trainer->id) {
            abort(403, 'You are not allowed to access this batch');
        }

        // Load related data
        $batch->load([
            'trainerData',
            'courseData',
            'sessionData',
            'students',
            'durationData'
        ]);

        return view('trainers.show', compact('batch'));
    }


    public function MyBatches()
    {
        // ✅ Get logged-in trainer
        $trainer = Auth::guard('trainer')->user();

        if (!$trainer) {
            abort(403, 'Unauthorized');
        }

        // ✅ Fetch batches assigned to this trainer
        $batches = Batch::with(['students.collegeData', 'courseData'])
            ->where('batch_assign', $trainer->id)
            ->get();

        return view('trainers.trainer_index', compact('batches'));
    }

    public function sendBatchEmail(Request $request)
    {
        $request->validate([
            'batch_id' => 'required',
            'subject' => 'required',
            'message' => 'required'
        ]);

        $batch = Batch::with('students','trainerData')->findOrFail($request->batch_id);

        $batchName = $batch->batch_name;
        $trainerName = $batch->trainerData->name ?? 'Trainer';

        foreach ($batch->students as $student) {

            if (!empty($student->email_id)) {

                try {

                    Mail::to($student->email_id)->send(
                        new BatchMessageMail(
                            $request->subject,
                            $request->message,
                            $batchName,
                            $trainerName
                        )
                    );

                } catch (\Exception $e) {
                    continue;
                }

            }

        }

        return back()->with('success','Email is being sent to batch students.');
    }

    public function markAttendance2(Batch $batch)
    {
        $trainer = Auth::guard('trainer')->user();

        if (!$trainer) {
            abort(403,'Unauthorized');
        }

        // Ensure trainer owns this batch
        if ($batch->batch_assign != $trainer->id) {
            abort(403,'You are not allowed to access this batch');
        }

        $today = Carbon::today();

        // Check if attendance already exists
        $session = AttendanceSession::where('batch_id',$batch->id)
                    ->where('session_date',$today)
                    ->first();

        if($session){

            $records = AttendanceRecord::where('session_id',$session->id)
                        ->pluck('status','student_id');

            $students = $batch->students;

            return view(
                'trainers.attendance.mark',
                compact('batch','students','records','session')
            );
        }

        $students = $batch->students;

        return view(
            'trainers.attendance.mark',
            compact('batch','students')
        );
    }
    public function markAttendance(Request $request, Batch $batch)
{
    $trainer = Auth::guard('trainer')->user();

    if ($batch->batch_assign != $trainer->id) {
        abort(403);
    }

    $date = $request->date 
        ? Carbon::parse($request->date)
        : Carbon::today();

    $session = AttendanceSession::where('batch_id',$batch->id)
        ->whereDate('session_date',$date)
        ->first();

    $students = $batch->students;

    $records = [];

    if($session){

        $records = AttendanceRecord::where('session_id',$session->id)
            ->pluck('status','student_id');

    }

    return view(
        'trainers.attendance.mark',
        compact('batch','students','records','date')
    );
}
    public function saveAttendance2(Request $request)
    {
        $request->validate([
            'batch_id' => 'required'
        ]);

        $session = AttendanceSession::firstOrCreate(
        [
            'batch_id' => $request->batch_id,
            'session_date' => Carbon::today()
        ],
        [
            'trainer_id' => Auth::guard('trainer')->id()
        ]
        );

        if($request->has('attendance')){
            foreach ($request->attendance as $studentId => $status) {

                AttendanceRecord::updateOrCreate(
                    [
                        'session_id'=>$session->id,
                        'student_id'=>$studentId
                    ],
                    [
                        'status'=>$status
                    ]
                );

            }
        }

        return redirect()->route('batches.mybatches')
            ->with('success','Attendance saved successfully');
    }

    public function saveAttendance(Request $request)
    {
        $request->validate([
            'batch_id' => 'required',
            'attendance_date' => 'required|date'
        ]);

        $session = AttendanceSession::firstOrCreate(
            [
                'batch_id' => $request->batch_id,
                'session_date' => Carbon::parse($request->attendance_date)
            ],
            [
                'trainer_id' => Auth::guard('trainer')->id()
            ]
        );

        if($request->has('attendance')){
            foreach ($request->attendance as $studentId => $status) {

                AttendanceRecord::updateOrCreate(
                    [
                        'session_id'=>$session->id,
                        'student_id'=>$studentId
                    ],
                    [
                        'status'=>$status
                    ]
                );

            }
        }

        return redirect()->route('batches.mybatches')
            ->with('success','Attendance saved successfully');
    }

    public function batchAttendance(Batch $batch)
    {
        $trainer = Auth::guard('trainer')->user();

        if($batch->batch_assign != $trainer->id){
            abort(403);
        }

        $sessionsAtt = AttendanceSession::with('records')
        ->where('batch_id',$batch->id)
        ->orderByDesc('session_date')
        ->get();

        return view(
            'trainers.attendance.batch',
            compact('batch','sessionsAtt')
        );
    }

    public function studentAttendance(Student $student)
    {
        $records = AttendanceRecord::with('session')
            ->where('student_id', $student->id)
            ->join('attendance_sessions','attendance_records.session_id','=','attendance_sessions.id')
            ->orderByDesc('attendance_sessions.session_date')
            ->select('attendance_records.*')
            ->get();

        $present = $records
            ->whereIn('status',['present','late'])
            ->count();

        $absent = $records
            ->where('status','absent')
            ->count();

        $total = $records->count();

        $percentage = $total > 0
            ? round(($present/$total)*100)
            : 0;

        return view(
            'trainers.attendance.student',
            compact('student','records','present','absent','total','percentage')
        );
    }
}
