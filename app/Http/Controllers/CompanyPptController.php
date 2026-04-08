<?php

namespace App\Http\Controllers;

use App\Models\CompanyPpt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyPptController extends Controller
{   
    protected string $permissionPrefix = 'company_ppt';

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
        $query = CompanyPpt::query();
        $now = now();

        if ($request->filter === 'active') {
            $query->where('is_active', true)
                ->where(fn($q) => $q->whereNull('start_at')->orWhere('start_at','<=',$now))
                ->where(fn($q) => $q->whereNull('end_at')->orWhere('end_at','>=',$now));
        } elseif ($request->filter === 'expired') {
            $query->whereNotNull('end_at')->where('end_at','<',$now);
        }

        $companyPpts = $query->latest()->paginate(20);

        return view('company_ppt.index', compact('companyPpts'));
    }

    public function create()
    {
        return view('company_ppt.create');
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
        $path = storage_path('app/secure-company-ppts');

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $filename);

        CompanyPpt::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'file_name' => $filename,
            'mime' => $file->getClientMimeType(),
            'is_active' => $data['is_active'] ?? true,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
        ]);

        return redirect()->route('company_ppt.index')
            ->with('success', 'Company PPT uploaded successfully.');
    }

    public function edit(CompanyPpt $companyPpt)
    {
        return view('company_ppt.edit', compact('companyPpt'));
    }

    public function update(Request $request, CompanyPpt $companyPpt)
    {
        $data = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'file' => 'nullable|file|mimes:ppt,pptx|max:102400',
            'is_active' => 'nullable|boolean',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        if ($request->hasFile('file')) {
            @unlink($companyPpt->full_file_path);

            $file = $request->file('file');
            $filename = time().'-'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(storage_path('app/secure-company-ppts'), $filename);

            $companyPpt->file_name = $filename;
            $companyPpt->mime = $file->getClientMimeType();
        }

        $companyPpt->update($data);

        return redirect()->route('company_ppt.index')
            ->with('success', 'Company PPT updated successfully.');
    }

    public function preview($token)
    {
        $ppt = CompanyPpt::where('share_token', $token)
            ->publiclyVisible()
            ->firstOrFail();

        $ppt->increment('download_count');

        return response()->download(
            $ppt->full_file_path,
            $ppt->file_name,
            ['Content-Type' => $ppt->mime]
        );
    }

    public function adminDownload(CompanyPpt $companyPpt)
    {
        return response()->download(
            $companyPpt->full_file_path,
            $companyPpt->file_name,
            ['Content-Type' => $companyPpt->mime]
        );
    }

    public function destroy(CompanyPpt $companyPpt)
    {
        @unlink($companyPpt->full_file_path);
        $companyPpt->delete();

        return back()->with('success', 'Deleted successfully.');
    }
}
