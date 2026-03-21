<?php

namespace App\Http\Controllers;

use App\Models\InterviewQuestion;
use App\Models\Technology;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InterviewQuestionController extends Controller
{
    protected string $permissionPrefix = 'interview_questions';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'download'         => 'view',
        'sendEmail'         => 'view',
         

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
    
    
    public function index(Request $request)
    {
        $technologies = Technology::where('is_active', 1)->get();

        $questions = InterviewQuestion::with('technology')
            ->when($request->round_type, fn ($q) =>
                $q->where('round_type', $request->round_type)
            )
            ->when($request->experience_level, fn ($q) =>
                $q->where('experience_level', $request->experience_level)
            )
            ->when($request->technology_id, fn ($q) =>
                $q->where('technology_id', $request->technology_id)
            )
            ->orderByDesc('updated_at')
            ->get();

        return view('interview_questions.index', compact('questions', 'technologies'));
    }

    public function create()
    {
        $technologies = Technology::where('is_active', 1)->get();
        return view('interview_questions.create', compact('technologies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'round_type' => 'required',
            'experience_level' => 'required',
        ]);
        // dd($request);
        InterviewQuestion::create($request->all());

        // return redirect()->route('interview-questions.index')
        //     ->with('success', 'Question added successfully');

        return redirect()->route('interview-questions.index', $request->query())
            ->with('success', 'Question added successfully');
        // return redirect()->back()
        //     ->with('success', 'Question added successfully');
    }

    public function edit(InterviewQuestion $interview_question)
    {
        $technologies = Technology::where('is_active', 1)->get();
        return view('interview_questions.edit', compact('interview_question', 'technologies'));
    }

    public function update(Request $request, InterviewQuestion $interview_question)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'round_type' => 'required',
            'experience_level' => 'required',
        ]);

        $interview_question->update($request->all());

        return redirect()->route('interview-questions.index', $request->query())
        ->with('success', 'Question updated successfully');
        // return redirect()->route('interview-questions.index')
        //     ->with('success', 'Question updated successfully');
    }

    public function destroy(InterviewQuestion $interview_question)
    {
        $interview_question->delete();
        return back()->with('success', 'Question deleted');
    }

    public function practice(Request $request)
	{
	    $technologies = Technology::where('is_active', 1)
	        ->orderBy('name')
	        ->get();

	    $questions = InterviewQuestion::with('technology')
	        ->where('is_active', 1)

	        ->when($request->round_type, function ($q) use ($request) {
	            $q->where('round_type', $request->round_type);
	        })

	        ->when($request->experience_level, function ($q) use ($request) {
	            $q->where('experience_level', $request->experience_level);
	        })

	        ->when($request->technology_id, function ($q) use ($request) {
	            $q->where('technology_id', $request->technology_id);
	        })

	        ->orderBy('id', 'desc')
	        ->get();

	    return view('interview_questions.show_question_ans', compact(
	        'questions',
	        'technologies'
	    ));
	}

}

