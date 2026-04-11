<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeOnlineTest;
use App\Models\OfficeOnlineQuestion;
use App\Models\OfficeOnlineOption;

class OfficeOnlineQuestionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Questions List
    |--------------------------------------------------------------------------
    */
    public function index($test_id)
    {
        $test = OfficeOnlineTest::findOrFail($test_id);

        $questions = OfficeOnlineQuestion::with('options')
            ->where('office_online_test_id', $test_id)
            ->latest()
            ->get();

        return view('admin.office-online-questions.index', compact('test', 'questions'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create Question Form
    |--------------------------------------------------------------------------
    */
    public function create($test_id)
    {
        $test = OfficeOnlineTest::findOrFail($test_id);

        return view('admin.office-online-questions.create', compact('test'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store Question + Options
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'office_online_test_id' => 'required|exists:office_online_tests,id',
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer'
        ]);

        // Save Question
        $question = OfficeOnlineQuestion::create([
            'office_online_test_id' => $request->office_online_test_id,
            'question' => $request->question,
            'type' => 'mcq',
            // 'marks' => 1
        ]);

        // Save Options
        foreach ($request->options as $key => $optionText) {
            OfficeOnlineOption::create([
                'office_online_question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($request->correct_option == $key) ? 1 : 0
            ]);
        }

        return redirect()
            ->route('admin.office-online-questions.index', $request->office_online_test_id)
            ->with('success', 'Question added successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Question
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $question = OfficeOnlineQuestion::with('options')->findOrFail($id);

        return view('admin.office-online-questions.edit', compact('question'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update Question + Options
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
{
    $question = OfficeOnlineQuestion::findOrFail($id);

    $request->validate([
        'question' => 'required|string',
        'options' => 'required|array|min:2',
        'options.*.text' => 'required|string',
        'correct_option' => 'required'
    ]);

    // Update question
    $question->update([
        'question' => $request->question
    ]);

    // Delete old options
    $question->options()->delete();

    // Re-add options
    foreach ($request->options as $key => $option) {

        OfficeOnlineOption::create([
            'office_online_question_id' => $question->id,
            'option_text' => $option['text'],
            'is_correct' => ($request->correct_option == $key) ? 1 : 0
        ]);
    }

    return redirect()
        ->route('admin.office-online-questions.index', $question->office_online_test_id)
        ->with('success', 'Question updated successfully');
}
    public function update12(Request $request, $id)
    {
        $question = OfficeOnlineQuestion::findOrFail($id);

        // $request->validate([
        //     'question' => 'required|string',
        //     'options' => 'required|array|min:2',
        //     'options.*' => 'required|string',
        //     'correct_option' => 'required|integer'
        // ]);

        $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'correct_option' => 'required'
        ]);

        // Update question
        $question->update([
            'question' => $request->question
        ]);

        // Delete old options
        $question->options()->delete();

        // Re-add options
        // foreach ($request->options as $key => $optionText) {
        //     OfficeOnlineOption::create([
        //         'office_online_question_id' => $question->id,
        //         'option_text' => $optionText,
        //         'is_correct' => ($request->correct_option == $key) ? 1 : 0
        //     ]);
        // }

        foreach ($request->options as $key => $option) {

            OfficeOnlineOption::create([
                'office_online_question_id' => $question->id,
                'option_text' => $option['text'],
                'is_correct' => ($request->correct_option == $key) ? 1 : 0
            ]);
        }

        return redirect()
            ->route('admin.office-online-questions.index', $question->office_online_test_id)
            ->with('success', 'Question updated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Question
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $question = OfficeOnlineQuestion::findOrFail($id);

        $test_id = $question->office_online_test_id;

        $question->delete();

        return redirect()
            ->route('admin.office-online-questions.index', $test_id)
            ->with('success', 'Question deleted successfully');
    }
}