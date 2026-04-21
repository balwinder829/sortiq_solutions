<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeTest;
use App\Models\StudentSession;
use App\Models\Batch;
use App\Models\Trainer;
use App\Models\Course;
use App\Models\TestCategory;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;
// use App\Models\OfficeTest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OfficeTestAnswersExport;


class OfficeTestController extends Controller
{

public function __construct()
{
    $this->middleware('permission:students_office_test.view')->only('index');
    $this->middleware('permission:students_office_test.create')->only(['create','store']);
    $this->middleware('permission:students_office_test.edit')->only(['edit','update']);
    $this->middleware('permission:students_office_test.delete')->only('destroy');
}

public function downloadPdf($officeTestId)
{

    $test = OfficeTest::with([
        'session',
        'batch',
        'trainer',
        'questions'
    ])->findOrFail($officeTestId);


    // Format questions
    $questions = $test->questions
        ->sortBy('question_order')
        ->values()
        ->map(function ($q) {

            return (object)[
                'id' => $q->id,
                'question' => $q->question,
                'marks' => $q->marks
            ];

        });


    $mpdf = new Mpdf([

        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',

        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 20,
        'margin_bottom' => 20,

        'tempDir' => storage_path('app/mpdf')

    ]);


    $html = View::make(
        'pdf.office-question-paper',
        compact('test','questions')
    )->render();


    $mpdf->WriteHTML($html);


    $safeTitle = preg_replace('/[\/\\\\]/','-',$test->title);
    $safeTitle = preg_replace('/[^A-Za-z0-9\-_ ]/','',$safeTitle);


    return response()->streamDownload(
        fn() => print($mpdf->Output('', 'S')),
        $safeTitle.'-office-question-paper.pdf'
    );

}
    public function index(Request $request)
    {

    $tests = OfficeTest::with(['session','batch','trainer'])
    ->withCount('questions');

    if($request->session_id){
    $tests->where('session_id',$request->session_id);
    }

    if($request->batch_id){
    $tests->where('batch_id',$request->batch_id);
    }

    if($request->trainer_id){
    $tests->where('trainer_id',$request->trainer_id);
    }

    if($request->exam_mode){
    $tests->where('exam_mode',$request->exam_mode);
    }

    $tests = $tests->latest()->get();


    $batches = Batch::orderBy('batch_name')->get();
    $sessions = StudentSession::orderBy('session_name')->get();
    $trainers = Trainer::orderBy('name')->get();

    return view('admin.office-tests.index',[

    'tests'=> $tests,
    'sessionsList'=> $sessions,
    'batches'=> $batches,
    'trainers'=> $trainers

    ]);

    }

    public function create()
    {
        return view('admin.office-tests.create', [

            'sessionsList' => StudentSession::all(),
            'courses' => Course::all(),
            'categories' => TestCategory::all(),
            'batches' => Batch::all(),
            'trainers' => Trainer::all(),

        ]);
    }

    // public function create()
    // {
    //     return view('admin.tests.create', [
    //         'colleges'  => College::all(),
    //         'courses'   => Course::all(),
    //         'semesters' => Semester::all(),
    //         'branches'  => array(),
    //         'categories' => TestCategory::all(),
    //     ]);
    // }

    public function store(Request $request)
    {

        $data = $request->validate([

            'session_id' => 'required',
            'student_course_id' => 'nullable',
            'test_category_id' => 'nullable',

            'batch_id' => 'nullable',
            'trainer_id' => 'nullable',

            'title' => 'required',
            'total_marks' => 'required',
            'description' => 'nullable',

            'test_date' => 'nullable|date',
            'exam_start_at' => 'nullable',
            'exam_end_at' => 'nullable',

            'timer_type' => 'required|in:fixed,individual',
            'exam_mode' => 'required|in:online,offline',

            'status' => 'required|in:draft,published,unpublished'

        ]);

        $data['slug'] = Str::slug($request->title) . '-' . rand(1000,9999);

        $data['access_key'] = Str::random(15);

        $data['created_by'] = auth()->id();
        $data['status'] = 'unpublished';
        
        OfficeTest::create($data);

        return redirect()
            ->route('admin.office-tests.index')
            ->with('success','Test created successfully');

    }

    public function edit(OfficeTest $office_test)
    {

        return view('admin.office-tests.edit',[

            'test' => $office_test,
            'sessions' => StudentSession::all(),
            'courses' => Course::all(),
            'categories' => TestCategory::all(),
            'batches' => Batch::all(),
            'trainers' => Trainer::all()

        ]);

    }

    public function update(Request $request, OfficeTest $office_test)
    {

        $request->validate([

            'title' => 'required',
            'status' => 'required|in:draft,published,unpublished'

        ]);

         // ✅ Prevent publish if no questions
        if ($request->status === 'published' && !$office_test->questions()->exists()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'status' => 'You cannot publish this test because no questions have been added.'
                ]);
        }

        $office_test->update($request->all());

        return redirect()
            ->route('admin.office-tests.index')
            ->with('success','Test updated');

    }

    public function destroy(OfficeTest $office_test)
    {

        $office_test->delete();

        return back()->with('success','Test deleted');

    }

    public function downloadAnswers($slug)
    {
        $test = OfficeTest::where('slug',$slug)
            ->with('questions')
            ->firstOrFail();

        return Excel::download(
            new OfficeTestAnswersExport($test),
            $test->slug.'_answers.xlsx'
        );
    }

}