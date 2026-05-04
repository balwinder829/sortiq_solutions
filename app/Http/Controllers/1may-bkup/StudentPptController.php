<?php

namespace App\Http\Controllers;

use App\Models\StudentPpt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentPptController extends Controller
{   
    protected string $permissionPrefix = 'student_ppt';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'download'         => 'view',
        'sendEmail'         => 'view',
        'adminDownload'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth')->except('preview');

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
        $query = StudentPpt::query();
        $now = now();

        if ($request->filter === 'active') {
            $query->where('is_active', true)
                ->where(fn($q) => $q->whereNull('start_at')->orWhere('start_at','<=',$now))
                ->where(fn($q) => $q->whereNull('end_at')->orWhere('end_at','>=',$now));
        } elseif ($request->filter === 'expired') {
            $query->whereNotNull('end_at')->where('end_at','<',$now);
        }

        $companyPpts = $query->latest()->paginate(20);

        return view('students_ppt.index', compact('companyPpts'));
    }

    public function create()
    {
        return view('students_ppt.create');
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'required|file|max:102400',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        if (! in_array($ext, ['ppt', 'pptx'])) {
            return back()
                ->withErrors(['file' => 'Only PPT or PPTX files are allowed'])
                ->withInput();
        }
        $filename = time().'-'.Str::random(8).'.'.$ext;
        $path = storage_path('app/secure-student-ppts');

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $filename);

        StudentPpt::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'file_name' => $filename,
            'mime' => $file->getClientMimeType(),
            'is_active' => $data['is_active'] ?? true,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
        ]);

        return redirect()->route('student_ppt.index')
            ->with('success', 'PPT uploaded successfully.');
    }

    public function edit(StudentPpt $studentPpt)
    {
        $companyPpt = $studentPpt;
        return view('students_ppt.edit', compact('companyPpt'));
    }

    public function update(Request $request, StudentPpt $studentPpt)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'nullable|file|max:102400',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        // $file = $request->file('file');
        
        if ($request->hasFile('file')) {

            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());

            if (! in_array($ext, ['ppt', 'pptx'])) {
                return back()
                    ->withErrors(['file' => 'Only PPT or PPTX files are allowed'])
                    ->withInput();
            }

            @unlink($studentPpt->full_file_path);


            $filename = time().'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/secure-student-ppts'), $filename);

            $studentPpt->file_name = $filename;
            $studentPpt->mime = $file->getClientMimeType();
        }

        $studentPpt->update($data);

        return redirect()->route('student_ppt.index')
            ->with('success', 'PPT updated successfully.');
    }

    public function preview($token)
    {
        $ppt = StudentPpt::where('share_token', $token)
            ->publiclyVisible()
            ->firstOrFail();
        // dd($ppt);
        $ppt->increment('download_count');

        return response()->download(
            $ppt->full_file_path,
            $ppt->file_name,
            ['Content-Type' => $ppt->mime]
        );
    }

    public function adminDownload(StudentPpt $studentPpt)
    {
        // dd($studentPpt);
        return response()->download(
            $studentPpt->full_file_path,
            $studentPpt->file_name,
            ['Content-Type' => $studentPpt->mime]
        );
    }

    public function destroy(StudentPpt $studentPpt)
    {
        @unlink($studentPpt->full_file_path);
        $studentPpt->delete();

        return back()->with('success', 'Deleted successfully.');
    }
}
