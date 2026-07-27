<?php

namespace App\Http\Controllers\Letters;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\View;
use App\Models\LetterTemplate;

class LetterTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = LetterTemplate::query();

        if ($request->filled('letter_type')) {
            $query->where('letter_type', $request->letter_type);
        }
        
        $templates = $query->latest()->get();
        // dd($templates);
        return view('letter_templates.index', [
            'templates' => $templates,
            'selectedType' => $request->letter_type,
        ]);
    }

    public function create()
    {
        return view('letter_templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'letter_type' => 'required|string|max:100|unique:letter_templates,letter_type',
            'department'  => 'required|string|max:100',
            'content'     => 'required',
            'status'      => 'required|boolean',
        ]);

        LetterTemplate::create($data);

        return redirect()
            ->route('letter-templates.index')
            ->with('success', 'Letter template created successfully.');
    }

    public function edit(LetterTemplate $letter_template)
    {
        return view('letter_templates.edit', [
            'template' => $letter_template
        ]);
    }

    public function update(Request $request, LetterTemplate $letter_template)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'letter_type' => 'required|string|max:100|unique:letter_templates,letter_type,' . $letter_template->id,
            'department'  => 'required|string|max:100',
            'content'     => 'required',
            'status'      => 'required|boolean',
        ]);

        $letter_template->update($data);

        return redirect()
            ->route('letter-templates.index')
            ->with('success', 'Letter template updated successfully.');
    }

    public function destroy(LetterTemplate $letter_template)
    {
        $letter_template->delete();

        return redirect()
            ->route('letter-templates.index')
            ->with('success', 'Letter template deleted successfully.');
    }
}