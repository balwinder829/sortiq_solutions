<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Trainer;
use App\Models\StudentEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;

class StudentEvaluationController extends Controller
{
    public function index()
    {
        $evaluations = StudentEvaluation::with(['student','trainer.user'])
            ->latest()
            ->get();

        return view('student_evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $students = Student::orderBy('student_name')->get();
        $trainers = Trainer::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 2)
                  ->where('status', 'active');
            })
            ->join('users', 'users.id', '=', 'trainers.user_id')
            ->orderBy('users.name', 'asc')
            ->select('trainers.*')
            ->get();



        return view('student_evaluations.create', compact('students','trainers'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students_detail,id',
            'trainer_id' => 'required|exists:trainers,id',
            'attendance_percentage' => 'required|integer|min:0|max:100',

            'behavior' => 'required|in:good,avg,bad',
            'technical' => 'required|in:good,avg,bad',
            'live_project' => 'required|in:good,avg,bad',
            'soft_skills' => 'required|in:good,avg,bad',
            'github' => 'required|in:good,avg,bad',

            'projects' => 'required|in:completed,partial,pending',
            'assignments' => 'required|in:completed,pending',
        ]);

        StudentEvaluation::create($data);

        return redirect()
            ->route('student-evaluations.index')
            ->with('success', 'Student evaluation added successfully.');
    }

    public function show(StudentEvaluation $student_evaluation)
    {
        $student_evaluation->load(['student','trainer.user']);

        return view('student_evaluations.print', compact('student_evaluation'));
    }

    public function edit(StudentEvaluation $student_evaluation)
    {
        $students = Student::orderBy('student_name', 'asc')->get();

        $trainers = Trainer::with('user')
            ->whereHas('user', function ($q) {
                $q->where('role', 2)
                  ->where('status', 'active');
            })
            ->join('users', 'users.id', '=', 'trainers.user_id')
            ->orderBy('users.name', 'asc')
            ->select('trainers.*')
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

        $evaluation->load(['student', 'trainer.user']);
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

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="STUDENT_EVALUATION_FULL.pdf"'
            );
    }

    public function downloadEmpty(StudentEvaluation $student_evaluation)
    {
        $pdf = $this->generateEvaluationPdf($student_evaluation, 'empty');

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="STUDENT_EVALUATION_EMPTY.pdf"'
            );
    }


    public function downloadPdf(StudentEvaluation $student_evaluation)
    {
        $pdfContent = $this->generateEvaluationPdf($student_evaluation);

        // Build clean filename
        $studentName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim($student_evaluation->student->student_name ?? 'STUDENT')
            )
        );

        $trainerName = strtoupper(
            preg_replace(
                '/[^A-Za-z0-9]+/',
                '_',
                trim(optional($student_evaluation->trainer->user)->name ?? 'TRAINER')
            )
        );

        $date = $student_evaluation->created_at->format('Y_M_d');

        $fileName = trim(
            preg_replace('/_+/', '_', "{$studentName}_{$trainerName}_EVALUATION_{$date}"),
            '_'
        ) . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'attachment; filename="'.$fileName.'"'
            );
    }

    /*
public function sendEmail(StudentEvaluation $student_evaluation)
{
    $pdfContent = $this->generateEvaluationPdf($student_evaluation);

    $email = optional($student_evaluation->student->user)->email;

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
