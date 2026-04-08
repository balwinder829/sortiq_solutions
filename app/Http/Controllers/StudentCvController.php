<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentCvProfile;
use App\Models\StudentCvProfileSkill;
use App\Models\StudentCvTemplate;
use Mpdf\Mpdf;

class StudentCvController extends Controller
{   

    public function __construct()
    {
        $this->middleware('permission:student_cvs.view')->only('index');
    }

    public function index()
    {
        $cvs = StudentCvProfile::latest()->get();
        return view('admin.student-cv.index', compact('cvs'));
    }

    public function create()
    {
        $templates = StudentCvTemplate::where('status',1)->get();

        return view('student-cv.form',compact('templates'));
    }

public function store(Request $request)
{

    // check existing CV by phone and email
    $cv = StudentCvProfile::where('phone', $request->phone)
            ->where('email', $request->email)
            ->first();


    if($cv){

        // update existing CV
        $cv->update([
            'session_id' => session()->getId(),
            'student_id' => auth()->check() ? auth()->id() : null,
            'full_name'  => $request->full_name,
            'title'      => $request->title,
            'location'   => $request->location,
            'linkedin'   => $request->linkedin,
            'github'     => $request->github,
            'portfolio'  => $request->portfolio,
            'template_key' => $request->template_key,
            'summary'    => $request->summary
        ]);

        // delete old related data
        \App\Models\StudentCvProfileSkill::where('cv_profile_id',$cv->id)->delete();
        \App\Models\StudentCvProfileEducation::where('cv_profile_id',$cv->id)->delete();
        \App\Models\StudentCvProfileProject::where('cv_profile_id',$cv->id)->delete();
        \App\Models\StudentCvProfileExperience::where('cv_profile_id',$cv->id)->delete();
        \App\Models\StudentCvProfileCertification::where('cv_profile_id',$cv->id)->delete();

    } else {

        // create new CV
        $cv = StudentCvProfile::create([
            'session_id' => session()->getId(),
            'student_id' => auth()->check() ? auth()->id() : null,
            'full_name'  => $request->full_name,
            'title'      => $request->title,
            'phone'      => $request->phone,
            'email'      => $request->email,
            'location'   => $request->location,
            'linkedin'   => $request->linkedin,
            'github'     => $request->github,
            'portfolio'  => $request->portfolio,
            'template_key' => $request->template_key,
            'summary'    => $request->summary
        ]);

    }



    /*
    |--------------------------------
    | Skills
    |--------------------------------
    */

    if($request->skills){

        foreach($request->skills as $skill){

            if(!empty($skill)){

                \App\Models\StudentCvProfileSkill::create([
                    'cv_profile_id' => $cv->id,
                    'skill' => $skill
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Education
    |--------------------------------
    */

    if($request->degree){

        foreach($request->degree as $index => $degree){

            if(!empty($degree)){

                \App\Models\StudentCvProfileEducation::create([
                    'cv_profile_id' => $cv->id,
                    'degree' => $degree,
                    'institution' => $request->institution[$index] ?? null,
                    'start_year' => $request->start_year[$index] ?? null,
                    'end_year' => $request->end_year[$index] ?? null,
                    'grade' => null
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Projects
    |--------------------------------
    */

    if($request->project_title){

        foreach($request->project_title as $index => $title){

            if(!empty($title)){

                \App\Models\StudentCvProfileProject::create([
                    'cv_profile_id' => $cv->id,
                    'title' => $title,
                    'description' => $request->project_description[$index] ?? null,
                    'technology' => $request->project_tech[$index] ?? null,
                    'github_link' => $request->github_link[$index] ?? null,
                    'live_link' => null
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Experience
    |--------------------------------
    */

    if($request->company_name){

        foreach($request->company_name as $index => $company){

            if(!empty($company)){

                \App\Models\StudentCvProfileExperience::create([
                    'cv_profile_id' => $cv->id,
                    'company_name' => $company,
                    'role' => $request->role[$index] ?? null,
                    'location' => null,
                    'start_date' => $request->exp_start[$index] ?? null,
                    'end_date' => $request->exp_end[$index] ?? null,
                    'currently_working' => 0,
                    'summary' => $request->exp_summary[$index] ?? null
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Certifications
    |--------------------------------
    */

    if($request->cert_title){

        foreach($request->cert_title as $index => $title){

            if(!empty($title)){

                \App\Models\StudentCvProfileCertification::create([
                    'cv_profile_id' => $cv->id,
                    'title' => $title,
                    'issuer' => $request->issuer[$index] ?? null,
                    'year' => $request->cert_year[$index] ?? null
                ]);

            }

        }

    }



    return redirect()
        ->route('student.cv.preview',$cv->id)
        ->with('success','CV saved successfully');

}
    public function storeold(Request $request)
{

    $cv = StudentCvProfile::create([
        'session_id' => session()->getId(),

        'student_id' => auth()->check() ? auth()->id() : null,
        'full_name'  => $request->full_name,
        'title'      => $request->title,
        'phone'      => $request->phone,
        'email'      => $request->email,
        'location'   => $request->location,
        'linkedin'   => $request->linkedin,
        'github'     => $request->github,
        'portfolio'  => $request->portfolio,
        'template_key'    => $request->template_key,
        'summary'    => $request->summary
    ]);



    /*
    |--------------------------------
    | Skills
    |--------------------------------
    */

    if($request->skills){

        foreach($request->skills as $skill){

            if(!empty($skill)){

                \App\Models\StudentCvProfileSkill::create([
                    'cv_profile_id' => $cv->id,
                    'skill' => $skill
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Education
    |--------------------------------
    */

    if($request->degree){

        foreach($request->degree as $index => $degree){

            if(!empty($degree)){

                \App\Models\StudentCvProfileEducation::create([
                    'cv_profile_id' => $cv->id,
                    'degree' => $degree,
                    'institution' => $request->institution[$index] ?? null,
                    'start_year' => $request->start_year[$index] ?? null,
                    'end_year' => $request->end_year[$index] ?? null,
                    'grade' => null
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Projects
    |--------------------------------
    */

    if($request->project_title){

        foreach($request->project_title as $index => $title){

            if(!empty($title)){

                \App\Models\StudentCvProfileProject::create([
                    'cv_profile_id' => $cv->id,
                    'title' => $title,
                    'description' => $request->project_description[$index] ?? null,
                    'technology' => $request->project_tech[$index] ?? null,
                    'github_link' => $request->github_link[$index] ?? null,
                    'live_link' => null
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Experience
    |--------------------------------
    */

    if($request->company_name){

        foreach($request->company_name as $index => $company){

            if(!empty($company)){

                \App\Models\StudentCvProfileExperience::create([
                    'cv_profile_id' => $cv->id,
                    'company_name' => $company,
                    'role' => $request->role[$index] ?? null,
                    'location' => null,
                    'start_date' => $request->exp_start[$index] ?? null,
                    'end_date' => $request->exp_end[$index] ?? null,
                    'currently_working' => 0,
                    'summary' => $request->exp_summary[$index] ?? null
                ]);

            }

        }

    }



    /*
    |--------------------------------
    | Certifications
    |--------------------------------
    */

    if($request->cert_title){

        foreach($request->cert_title as $index => $title){

            if(!empty($title)){

                \App\Models\StudentCvProfileCertification::create([
                    'cv_profile_id' => $cv->id,
                    'title' => $title,
                    'issuer' => $request->issuer[$index] ?? null,
                    'year' => $request->cert_year[$index] ?? null
                ]);

            }

        }

    }



    return redirect()
        ->route('student.cv.preview',$cv->id)
        ->with('success','CV created successfully');

}


    // public function preview($id)
    // {

    //     $cv = StudentCvProfile::with([
    //         'skills',
    //         'education',
    //         'projects',
    //         'experience',
    //         'certifications'
    //     ])->findOrFail($id);

    //     $templates = StudentCvTemplate::where('status',1)->get();

    //     return view('student-cv.preview',compact('cv','templates'));

    // }

    public function preview($id)
    {

    $cv = StudentCvProfile::with([
    'skills',
    'education',
    'projects',
    'experience',
    'certifications'
    ])->findOrFail($id);

    $template = $cv->template_key ?: ($cv->template_key ?: 'classic');

    $isPdf = false;
    return view(
    'cv-templates.'.$template,
    compact('cv', 'isPdf')
    );

    }

    public function download($id)
{
    $cv = StudentCvProfile::with([
        'skills',
        'education',
        'projects',
        'experience',
        'certifications'
    ])->findOrFail($id);

    $isPdf = true;

    $template = $cv->template_key ?: ($cv->template_key ?: 'classic');

    $html = view(
        'cv-templates.'.$template,
        compact('cv','isPdf')
    )->render();


    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',

         'margin_top' => 0,
    'margin_bottom' => 0,
    'margin_left' => 0,
    'margin_right' => 0,

        'default_font' => 'dejavusans'
    ]);


    // Improves CSS rendering
    $mpdf->SetDisplayMode('fullpage');

    // Write HTML
    $mpdf->WriteHTML($html);

    $fileName = preg_replace('/\s+/', '_', $cv->full_name).'_CV.pdf';

    return $mpdf->Output($fileName,'D');
    
}
    
}