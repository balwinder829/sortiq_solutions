<?php

namespace App\Http\Controllers;

use App\Models\StudentSession;
use App\Models\Student;
use App\Models\Batch;
use App\Models\College;
use App\Models\Trainer;
use App\Models\Session;
use App\Models\Course;
use App\Models\Event;
use App\Models\User;
use App\Models\EventNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;



class DashboardController extends Controller
{

    public function index()
    {
        if (auth()->user()->role == 1 || auth()->user()->role == 4) {
            
            $activeSessionId = session('admin_session_id');

            // $totalStudents = Student::count();
            $totalStudents = Student::where('session', $activeSessionId)->count();
            
            $totalBatches  = Batch::where('session_name', $activeSessionId)->count();
            
            $totalColleges = College::count();
            $totalTrainers = Trainer::count();
            $totalSessions = StudentSession::count();
            $totalCourses  = Course::count();
            $pendingFeeStudents = Student::where('pending_fees', '>', 0)
                                        ->whereDate('next_due_date', '<=', now())
                                        ->where('session', $activeSessionId)
                                        ->count();
            $sessions = StudentSession::all();

            //new added
            $totalConfirmed = Student::where('certificate_status', 1)->where('session', $activeSessionId)->count();
            $totalCertificate = Student::where('certificate_status', 2)->where('session', $activeSessionId)->count();


            // new
            $totalEmployye  = User::where('role', [2,3])->count();
            $totalSaleEmployye  = User::where('role', 3)->count();
            $placedStudents = Student::where('is_placed', 1)->where('session', $activeSessionId)->count();
            $feeconfirmSum = Student::where('certificate_status', 1)
                ->where('session', $activeSessionId)
                ->selectRaw('SUM(total_fees - pending_fees) as collected')
                ->value('collected');

            $feecertificateSum = Student::where('certificate_status', 2)
                ->where('session', $activeSessionId)
                ->selectRaw('SUM(total_fees - pending_fees) as collected')
                ->value('collected');


            $topCollegeData = DB::table('students_detail')
                ->join('colleges', 'colleges.id', '=', 'students_detail.college_name')
                ->select(
                    'students_detail.college_name',
                    'colleges.college_name as college_name_text',
                    DB::raw('COUNT(students_detail.id) as total_students'),
                    DB::raw('SUM(students_detail.total_fees - students_detail.pending_fees) as total_collected')
                )
                ->where('students_detail.session', $activeSessionId)
                ->groupBy('students_detail.college_name', 'colleges.college_name')
                ->orderByDesc('total_students')
                ->first() 
                ?: (object) [
                    'college_name'      => null,
                    'college_name_text' => '',
                    'total_students'    => 0,
                    'total_collected'   => 0
                ];





            // $topCollege = Student::join('colleges', 'colleges.id', '=', 'students_detail.college_name')
            //     ->select(
            //         'students_detail.college_name',
            //         'colleges.college_name as college_name_text',
            //         DB::raw('COUNT(students_detail.id) as total')
            //     )
            //     ->where('students_detail.session', $activeSessionId)
            //     ->groupBy('students_detail.college_name', 'colleges.college_name')
            //     ->orderByDesc('total')
            //     ->first();

            // $topCollegeTotal = $topCollege->total ?? 0;
            // $topCollegeName  = $topCollege->college_name_text ?? '-';

            $topCollege = DB::table('students_detail')
            ->join('colleges', 'colleges.id', '=', 'students_detail.college_name')
            ->select(
                'students_detail.college_name',
                'colleges.college_name as college_name_text',
                DB::raw('COUNT(students_detail.id) as total')
            )
            ->where('students_detail.session', $activeSessionId)
            ->groupBy('students_detail.college_name', 'colleges.college_name')
            ->orderByDesc('total')
            ->first() 
            ?: (object) ['college_name' => null, 'college_name_text' => '', 'total' => 0];

            $highestFeeStudent = Student::where('session', $activeSessionId)
    ->orderByDesc('total_fees')
    ->value('total_fees');


            $dismissed = session('dismiss_pending_fee');

            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();

            $today = Carbon::today();
            $tomorrow = Carbon::tomorrow();

            // EVENT LISTS
            $todayEvents = Event::whereDate('event_date', $today)->get();
            $tomorrowEvents = Event::whereDate('event_date', $tomorrow)->get();
            $upcomingEvents = Event::whereDate('event_date', '>', $tomorrow)
                                    ->orderBy('event_date')
                                    ->get();

            // POPUP DISMISS RECORD (only for today)
            $todayNotification = EventNotification::today();
             /** FINALLY — RETURN VIEW **/
            return view('dashboard_admin', compact(
                'totalStudents',
                'totalBatches',
                'totalColleges',
                'totalTrainers',
                'totalSessions',
                'totalCourses',
                'pendingFeeStudents',
                'sessions',
                'pendingStudents',
                'totalConfirmed',
                'totalCertificate',
                'topCollege',
                'highestFeeStudent',

                // event notification variables (admin only)
                'todayEvents',
                'tomorrowEvents',
                'upcomingEvents',
                'placedStudents',
                'totalEmployye',
                'totalSaleEmployye',
                'feeconfirmSum',
                'feecertificateSum',
                'topCollegeData',
                'todayNotification'
            ));
        }else{
            /** EXISTING DASHBOARD COUNTERS **/
            $totalStudents = Student::count();
            $totalBatches  = Batch::count();
            $totalColleges = College::count();
            $totalTrainers = Trainer::count();
            $totalSessions = StudentSession::count();
            $totalCourses  = Course::count();
            $pendingFeeStudents = Student::where('pending_fees', '>', 0)
                                        ->whereDate('next_due_date', '<=', now())
                                        ->count();
            $sessions = StudentSession::all();
            $pendingStudents = collect();
            $todayEvents = collect();
            $tomorrowEvents = collect();
            $upcomingEvents = collect();
            $todayNotification = (object)['dismissed' => true];

             /** FINALLY — RETURN VIEW **/
            return view('dashboard', compact(
                'totalStudents',
                'totalBatches',
                'totalColleges',
                'totalTrainers',
                'totalSessions',
                'totalCourses',
                'pendingFeeStudents',
                'sessions',
                'pendingStudents',

                // event notification variables (admin only)
                'todayEvents',
                'tomorrowEvents',
                'upcomingEvents',
                'todayNotification'
            ));
        }
       
    }


    public function index_old()
    {
        $totalStudents = Student::count();
        $totalBatches  = Batch::count();
        $totalColleges = College::count();
        $totalTrainers = Trainer::count();
        $totalSessions = StudentSession::count();
        $totalCourses  = Course::count();
        $pendingFeeStudents = Student::where('pending_fees', '>', 0)->whereDate('next_due_date', '<=', now())->count();
        $sessions = StudentSession::all(); // For dropdown

        // Only for Admin
        if (auth()->user()->role == 1) {
            
            // Check if dismissed in session
            $dismissed = session('dismiss_pending_fee');

            // Get pending dues if not dismissed
            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                ->whereDate('next_due_date', '<=', now())
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect(); // Empty if dismissed
        } else {
             
            $pendingStudents = collect(); 
        }
        
        return view('dashboard', compact(
            'totalStudents',
            'totalBatches',
            'totalColleges',
            'totalTrainers',
            'totalSessions',
            'totalCourses',
            'pendingFeeStudents',
            'sessions',
            'pendingStudents'
        ));
    }
    

    public function getSessionStudents($sessionName)
    {
        // Count students where session matches the given session name
        $studentsCount = Student::where('session', $sessionName)->count();

        return response()->json([
            'sessionName' => $sessionName, // use the input session name
            'studentsCount' => $studentsCount
        ]);
    }

    public function changeSession(Request $request)
    {

        if (Auth::user()->role != 1) {
            abort(403, "Unauthorized");
        }
        $request->validate([
            'session_id' => 'required|exists:student_sessions,id',
        ]);

        session(['admin_session_id' => $request->session_id]);

        return back()->with('success', 'Session changed successfully.');
    }
}
