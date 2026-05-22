<?php

namespace App\Http\Controllers\Students;
use App\Http\Controllers\Controller;

use App\Models\Student;
use App\Models\Trainer;
use App\Models\StudentEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

class StudentEvaluationController extends Controller
{
    protected string $permissionPrefix = 'student_evaluations';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'downloadEmpty'         => 'view',
        'downloadFull'         => 'view',
         

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
        // $evaluations = StudentEvaluation::with(['student','trainer'])
        //     ->latest()
        //     ->get();

        $evaluations = StudentEvaluation::with([
            'student' => function ($query) {
                $query->withTrashed();
            },
            'trainer' => function ($query) {
                $query->withTrashed();
            }
        ])->latest('updated_at')->get();

        return view('student_evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $activeSessionId = session('admin_session_id');
        $students = Student::where('session', $activeSessionId)->orderBy('student_name')->get();

        $trainers = Trainer::query()
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();




        return view('student_evaluations.create', compact('students','trainers'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students_detail,id',
            'trainer_id' => 'required|exists:trainers,id',
            'attendance_percentage' => 'required|integer|min:0|max:100',
            'email' => 'nullable|email',

            'behavior' => 'required|in:good,avg,bad',
            'technical' => 'required|in:good,avg,bad',
            'live_project' => 'required|in:good,avg,bad',
            'soft_skills' => 'required|in:good,avg,bad',
            'github' => 'required|in:good,avg,bad',

            'projects' => 'required|in:completed,partial,pending',
            'assignments' => 'required|in:completed,partial,pending',
        ]);

        StudentEvaluation::create($data);

        return redirect()
            ->route('student-evaluations.index')
            ->with('success', 'Student evaluation added successfully.');
    }

    public function show(StudentEvaluation $student_evaluation)
    {
        $student_evaluation->load(['student','trainer']);

        return view('student_evaluations.print', compact('student_evaluation'));
    }

    public function edit(StudentEvaluation $student_evaluation)
    {
        // $students = Student::orderBy('student_name', 'asc')->get();
        $activeSessionId = session('admin_session_id');
        $students = Student::where('session', $activeSessionId)->orderBy('student_name')->get();

        $trainers = Trainer::query()
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();


 
        return view('student_evaluations.edit', compact(
            'student_evaluation',
            'students',
            'trainers'
        ));
    }


    public function update(Request $request, StudentEvaluation $student_evaluation)
    {
        $data = $request->validate([
            'attendance_percentage' => 'required|integer|min:0|max:100',
            'trainer_id' => 'required|exists:trainers,id',
            'email' => 'nullable|email',
            'behavior' => 'required|in:good,avg,bad',
            'technical' => 'required|in:good,avg,bad',
            'live_project' => 'required|in:good,avg,bad',
            'soft_skills' => 'required|in:good,avg,bad',
            'github' => 'required|in:good,avg,bad',

            'projects' => 'required|in:completed,partial,pending',
            'assignments' => 'required|in:completed,partial,pending',
        ]);

        $student_evaluation->update($data);

        return redirect()
            ->route('student-evaluations.index')
            ->with('success', 'Student evaluation updated successfully.');
    }

    public function destroy(StudentEvaluation $student_evaluation)
    {
        $student_evaluation->delete();

        return redirect()
            ->route('student-evaluations.index')
            ->with('success', 'Student evaluation deleted successfully.');
    }

    private function generateEvaluationPdf(StudentEvaluation $evaluation, string $mode = 'full'): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        $evaluation->load(['student', 'trainer']);
        // dd($mode, $evaluation);
        $html = View::make('student_evaluations.pdf', [
            'evaluation' => $evaluation,
            'mode'       => $mode, // 🔑 full | empty
        ])->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }

    public function downloadFull(StudentEvaluation $student_evaluation)
    {
        $pdf = $this->generateEvaluationPdf($student_evaluation, 'full');

        $studentName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim($student_evaluation->student->student_name ?? 'STUDENT')
            )
        );

        $fatherName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim($student_evaluation->student->f_name ?? 'STUDENT')
            )
        );

       

        $date = $student_evaluation->created_at->format('Y_M_d');

        $fileName = trim(
            preg_replace('/_+/', '_', "{$studentName}_{$fatherName}_EVALUATION_{$date}"),
            '_'
        ) . '.pdf';

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }

    public function downloadEmpty(StudentEvaluation $student_evaluation)
    {

        $studentName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim($student_evaluation->student->student_name ?? 'STUDENT')
            )
        );

        $fatherName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim($student_evaluation->student->f_name ?? 'STUDENT')
            )
        );

       

        $date = $student_evaluation->created_at->format('Y_M_d');

        $fileName = trim(
            preg_replace('/_+/', '_', "{$studentName}_{$fatherName}_EVALUATION_{$date}"),
            '_'
        ) . '.pdf';


        $pdf = $this->generateEvaluationPdf($student_evaluation, 'empty');

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }


public function sendEmail(StudentEvaluation $student_evaluation)
{

    // dd($student_evaluation);


    if (
        !$student_evaluation->email ||
        empty($student_evaluation->email)
    ) {
        return back()->with('error', 'Please add email to send report.');
    }


    $studentName = strtoupper(
        preg_replace(
            '/[^A-Za-z0-9]+/',
            '_',
            trim($student_evaluation->student->student_name ?? 'STUDENT')
        )
    );

    $fatherName = strtoupper(
        preg_replace(
            '/[^A-Za-z0-9]+/',
            '_',
            trim($student_evaluation->student->f_name ?? 'STUDENT')
        )
    );

   

    $date = $student_evaluation->created_at->format('Y_M_d');

    $fileName = trim(
        preg_replace('/_+/', '_', "{$studentName}_{$fatherName}_EVALUATION_{$date}"),
        '_'
    ) . '.pdf';

    $pdfContent = $this->generateEvaluationPdf($student_evaluation, 'full');

     

    $body = 
        "Dear ,\n\n" .
        "Please find attached report.\n\n" .
        "Regards,\nHR Department";

    Mail::raw($body, function ($message) use (
        $student_evaluation,
        $pdfContent,
        $fileName
    ) {
        $message->to($student_evaluation->email)
            ->subject("REPORT OF - {$student_evaluation->student->student_name}")
            ->attachData(
                $pdfContent,
                $fileName,
                ['mime' => 'application/pdf']
            );
    });

    return back()->with('success', 'Report emailed successfully.');
}  
    /*
public function sendEmail(StudentEvaluation $student_evaluation)
{
    $pdfContent = $this->generateEvaluationPdf($student_evaluation);

    

    if (!$email) {
        return back()->withErrors([
            'email' => 'Student email not available.'
        ]);
    }

    Mail::send([], [], function ($message) use ($email, $pdfContent) {
        $message->to($email)
            ->subject('Student Evaluation')
            ->attachData(
                $pdfContent,
                'STUDENT_EVALUATION.pdf',
                ['mime' => 'application/pdf']
            );
    });

    return back()->with('success', 'Evaluation emailed successfully.');
}
*/


}
