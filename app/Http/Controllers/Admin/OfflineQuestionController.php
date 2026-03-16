<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\OfflineQuestion;

class OfflineQuestionController extends Controller
{

    public function index(Test $test)
    {
        $questions = $test->offlineQuestions;

        return view('admin.tests.offline.questions.index', compact('test','questions'));
    }

    public function create(Test $test)
    {
        return view('admin.tests.offline.questions.create', compact('test'));
    }

    public function store(Request $request, Test $test)
    {
        $request->validate([
            'question' => 'required',
            'marks' => 'nullable|numeric'
        ]);

        $order = OfflineQuestion::where('test_id',$test->id)->count()+1;

        OfflineQuestion::create([
            'test_id' => $test->id,
            'question' => $request->question,
            'marks' => $request->marks,
            'question_order' => $order
        ]);

        return redirect()
            ->route('admin.offline-questions.index',$test->id)
            ->with('success','Question added successfully');
    }

    public function edit(OfflineQuestion $offlineQuestion)
    {
        return view('admin.tests.offline.questions.edit', compact('offlineQuestion'));
    }

    public function update(Request $request, OfflineQuestion $offlineQuestion)
    {
        $request->validate([
            'question' => 'required',
            'marks' => 'nullable|numeric'
        ]);

        $offlineQuestion->update($request->only('question','marks'));

        return back()->with('success','Question updated');
    }

    public function destroy(OfflineQuestion $offlineQuestion)
    {
        $offlineQuestion->delete();

        return back()->with('success','Question deleted');
    }
}