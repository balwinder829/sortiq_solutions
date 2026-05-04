<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentCvTemplate;

class StudentCvTemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:cv_templates.view')->only('index');
        $this->middleware('permission:cv_templates.create')->only(['create','store']);
        $this->middleware('permission:cv_templates.edit')->only(['edit','update']);
        $this->middleware('permission:cv_templates.delete')->only('destroy');
    }
    public function index()
    {

    $templates = StudentCvTemplate::latest()->get();

    return view('admin.student-cv-templates.index',compact('templates'));

    }


    public function create()
    {
    return view('admin.student-cv-templates.create');
    }


    public function store(Request $request)
    {

    $fileName = null;

    if($request->hasFile('sample_cv')){

    $file = $request->file('sample_cv');

    $fileName = time().'.'.$file->getClientOriginalExtension();

    $file->move(public_path('uploads/cv-samples'),$fileName);

    }

    StudentCvTemplate::create([
    'name'=>$request->name,
    'template_key'=>$request->template_key,
    'sample_cv'=>$fileName,
    'status'=>$request->status
    ]);

    return redirect()->route('admin.student.cv-templates.index')
    ->with('success','Template added');

    }


    public function edit($id)
    {
        $template = StudentCvTemplate::findOrFail($id);
        return view('admin.student-cv-templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = StudentCvTemplate::findOrFail($id);

        $fileName = $template->sample_cv;

        if ($request->hasFile('sample_cv')) {
            $file = $request->file('sample_cv');
            $fileName = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/cv-samples'), $fileName);
        }

        $template->update([
            'name' => $request->name,
            'template_key' => $request->template_key,
            'sample_cv' => $fileName,
            'status' => $request->status
        ]);

        return redirect()->route('admin.student.cv-templates.index')
            ->with('success', 'Template updated');
    }


    public function destroy($id)
    {

    StudentCvTemplate::findOrFail($id)->delete();

    return back()->with('success','Template deleted');

    }

}