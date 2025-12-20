<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// ✅ Import the models
use App\Models\Test;
use App\Models\Question;
use App\Models\Option;

class QuestionController extends Controller
{
    // Show form to add question
    public function create($test_id) {
        $test = Test::findOrFail($test_id);
        return view('admin.questions.create', compact('test'));
    }

    // Store question with options
    public function store(Request $request, $test_id) {
        $request->validate([
            'question'=>'required',
            'options'=>'required|array|min:2',
            'correct_option'=>'required'
        ]);

        $test = Test::findOrFail($test_id);
        // dd($test);
        // ✅ Make sure Question model has $fillable property
        $question = Question::create([
            'test_id'=>$test_id,
            'question'=>$request->question
        ]);

        foreach($request->options as $index => $option_text){
            // ✅ Make sure Option model has $fillable property
            Option::create([
                'question_id'=>$question->id,
                'option_text'=>$option_text,
                'is_correct'=>($index == $request->correct_option)
            ]);
        }

        // Redirect back to tests index
         return redirect()
            ->route(
                $test->test_mode === 'offline'
                    ? 'admin.offline-tests.index'
                    : 'admin.tests.index'
            )
            ->with('success', 'Question Added');
        // return redirect()->route('admin.tests.index')->with('success','Question Added');
    }
    public function edit($id)
    {
        $question = Question::findOrFail($id);
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $question = Question::findOrFail($id);
        $question->update($request->all());

        return redirect()->route('admin.tests.show', $question->test_id)
                        ->with('success', 'Question updated successfully!');
    }

}
