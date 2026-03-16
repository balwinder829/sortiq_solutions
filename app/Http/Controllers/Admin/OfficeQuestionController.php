<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeTest;
use App\Models\OfficeQuestion;

class OfficeQuestionController extends Controller
{   


    public function __construct()
    {
        
        $this->middleware('permission:students_office_test.handle_questions_ans')->only(['index','create','store','edit','update','destroy']);
        
    }


    public function index(OfficeTest $office_test)
    {
        $questions = $office_test->questions()
            ->orderBy('question_order')
            ->get();

        return view(
            'admin.office-tests.questions.index',
            compact('office_test','questions')
        );

    }

    public function create(OfficeTest $office_test)
    {

        return view(
            'admin.office-tests.questions.create',
            compact('office_test')
        );

    }

    public function store(Request $request, OfficeTest $office_test)
    {

        $request->validate([

            'question' => 'required',
            'marks' => 'nullable|numeric'

        ]);

        $order = $office_test->questions()->count() + 1;

        OfficeQuestion::create([

            'office_test_id' => $office_test->id,
            'question' => $request->question,
            'marks' => $request->marks,
            'question_order' => $order

        ]);

        // return redirect()
        //     ->route(
        //         'admin.office-tests.office-questions.index',
        //         $office_test->id
        //     )
        //     ->with('success','Question added successfully');

        return redirect()->back()
            ->with('success', 'Question added successfully');

    }

    public function edit(OfficeTest $office_test, OfficeQuestion $office_question)
    {

        return view(
            'admin.office-tests.questions.edit',
            compact('office_test','office_question')
        );

    }

    public function update(Request $request, OfficeTest $office_test, OfficeQuestion $office_question)
    {

        $request->validate([
            'question' => 'required'
        ]);

        $office_question->update($request->only('question','marks'));

        return back()->with('success','Question updated successfully');

    }

    public function destroy(OfficeTest $office_test, OfficeQuestion $office_question)
    {

        $office_question->delete();

        return back()->with('success','Question deleted');

    }

}