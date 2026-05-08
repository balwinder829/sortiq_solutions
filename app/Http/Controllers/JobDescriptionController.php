<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobDescription;
use App\Http\DataTables\DataTablesServerSide;

class JobDescriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:job_description.view')->only('index');
        $this->middleware('permission:job_description.create')->only(['create','store']);
        $this->middleware('permission:job_description.edit')->only(['edit','update']);
        $this->middleware('permission:job_description.delete')->only('destroy');
    }

    public function index()
    {
        return view('jd.index');
    }

    public function data(Request $request)
    {
        $query = JobDescription::query();

        if($request->status){
            $query->where('status',$request->status);
        }

        if($request->job_type){
            $query->where('job_type',$request->job_type);
        }

        if($request->date){
            $query->whereDate('last_date',$request->date);
        }

        $query->orderBy('id','desc');

        return DataTablesServerSide::response($request, $query, [
            'orderable' => ['id','title','job_type','last_date','status'],
            'searchable' => ['title','job_type','status'],
        ], function ($jd, $index, $start) {

            $statusBadge = '<span class="badge bg-secondary">'.ucfirst($jd->status).'</span>';
            $formattedDate = $jd->last_date 
                ? \Carbon\Carbon::parse($jd->last_date)->format('d M Y') 
                : '-';
            $publicUrl = route('jd.public', $jd->id);

            $actions  = '<a href="' . route('jd.edit', $jd->id) . '" class="btn btn-sm"><i class="fa fa-edit"></i></a> ';
            $actions .= '<a href="' . route('jd.show', $jd->id) . '" class="btn btn-sm"><i class="fa fa-eye"></i></a> ';
            $actions .= '<a href="' . $publicUrl . '" target="_blank"
                class="btn btn-sm"
                title="View Public">
                <i class="fa fa-external-link-alt"></i>
            </a>';

            // 🔥 COPY LINK BUTTON
            $actions .= '<button type="button"
                            class="btn btn-sm copy-link-btn"
                            data-url="'.$publicUrl.'"
                            title="Copy Share Link">
                            <i class="fa fa-link"></i>
                        </button> ';
            $actions .= '<form action="' . route('jd.destroy', $jd->id) . '" method="POST" style="display:inline-block;">'
                        . csrf_field()
                        . method_field('DELETE') .
                        '<button type="submit" class="btn btn-sm" data-swal-confirm="Are you sure?">
                            <i class="fa fa-trash"></i>
                        </button>
                        </form>';

            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                e($jd->title),
                ucfirst($jd->job_type),
                $formattedDate,
                $statusBadge,
                $actions
            ];
        });
    }

    public function create()
    {
        return view('jd.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'job_type' => 'required',
            'status' => 'required',
            'last_date' => 'nullable|date',
            'description' => 'nullable'
        ]);

        $validated['created_by'] = auth()->id();

        JobDescription::create($validated);

        return redirect()->route('jd.index')->with('success','JD Created');
    }

    public function edit($id)
    {
        $jd = JobDescription::findOrFail($id);
        return view('jd.edit', compact('jd'));
    }

    public function update(Request $request, $id)
    {
        $jd = JobDescription::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required',
            'job_type' => 'required',
            'status' => 'required',
            'last_date' => 'nullable|date',
            'description' => 'nullable'
        ]);

        $jd->update($validated);

        return redirect()->route('jd.index')->with('success','JD Updated');
    }

    public function show($id)
    {
        $jd = JobDescription::findOrFail($id);
        return view('jd.show', compact('jd'));
    }

    public function destroy($id)
    {
        JobDescription::findOrFail($id)->delete();
        return redirect()->route('jd.index')->with('success','Deleted');
    }

    // PUBLIC SHARE
    public function publicView($id)
    {
        $jd = JobDescription::where('status','active')->findOrFail($id);
        return view('jd.public', compact('jd'));
    }
}