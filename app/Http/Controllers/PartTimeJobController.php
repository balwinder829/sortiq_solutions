<?php

namespace App\Http\Controllers;

use App\Models\PartTimeJob;
use Illuminate\Http\Request;
use App\Imports\PartTimeJobImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use App\Rules\NotBlockedNumber;
use App\Exports\PartTimeJobExport;
// use Maatwebsite\Excel\Facades\Excel;

class PartTimeJobController extends Controller
{   
    protected string $permissionPrefix = 'part_time_jobs';

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

        'import'      => 'import',
        'importForm'      => 'import',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

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
	    $jobs = PartTimeJob::query();

	    if ($request->job_type) {
	        $jobs->where('job_type', 'like', '%' . $request->job_type . '%');
	    }

	    if ($request->shift) {
	        $jobs->where('shift', 'like', '%' . $request->shift . '%');
	    }

	    if ($request->location) {
	        $jobs->where('location', 'like', '%' . $request->location . '%');
	    }

	    if ($request->status) {
	        $jobs->where('status', $request->status);
	    }

	    $jobs = $jobs->orderBy('id', 'desc')->get();

	    return view('part_time_jobs.index', compact('jobs'));
	}


    public function create()
    {
        return view('part_time_jobs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'mobile' => 'nullable|string|max:20',
            // 'mobile' => ['required','regex:/^\d{10}(,\d{10})*$/'],
            // 'mobile' => ['required','regex:/^[0-9]{10}(,[0-9]{10})*$/'],
            // 'mobile' => ['required','regex:/^[0-9]{10,18}(,[0-9]{10,18})*$/'],

            'email' => [
                'nullable',
                'regex:/^[^,\s]+@[^,\s]+\.[^,\s]+(,[^,\s]+@[^,\s]+\.[^,\s]+)*$/'
            ],

            'mobile' => [
                'required',
                'regex:/^[0-9]{10,18}(,[0-9]{10,18})*$/',
                new NotBlockedNumber,
            ],
            'status' => 'required|in:active,inactive',
            // 'email' => 'nullable|email',
        ],[
            'mobile.regex' => 'Each phone number must be between 10 to 18 digits and comma separated.',
        ]);

         $data = $request->all();

        // normalize phones
        if (!empty($data['mobile'])) {
            $data['mobile'] = preg_replace('/[\|\-_;\s]+/', ',', $data['mobile']);
            $data['mobile'] = preg_replace('/,+/', ',', $data['mobile']);
            $data['mobile'] = trim($data['mobile'], ',');
        }

        // normalize emails
        if (!empty($data['email'])) {
            $data['email'] = preg_replace('/[\|\-_;\s]+/', ',', $data['email']);
            $data['email'] = preg_replace('/,+/', ',', $data['email']);
            $data['email'] = trim($data['email'], ',');
        }

        PartTimeJob::create($data);

        // PartTimeJob::create($request->all());

        return redirect()->route('part-time-jobs.index')
            ->with('success', 'Part-time job added successfully');
    }

    /* ✅ SHOW METHOD (IMPORTANT) */
    public function show($id)
    {
        $job = PartTimeJob::findOrFail($id);
        return view('part_time_jobs.show', compact('job'));
    }

    public function edit($id)
    {
        $job = PartTimeJob::findOrFail($id);
        return view('part_time_jobs.edit', compact('job'));
    }

    public function update(Request $request, $id)
    {
        $job = PartTimeJob::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // 'mobile' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            // 'email' => 'nullable|email',
            // 'mobile' => ['required','regex:/^\d{10}(,\d{10})*$/'],
            // 'mobile' => ['required','regex:/^[0-9]{10}(,[0-9]{10})*$/'],
            // 'mobile' => ['required','regex:/^[0-9]{10,18}(,[0-9]{10,18})*$/'],
            'mobile' => [
                'required',
                'regex:/^[0-9]{10,18}(,[0-9]{10,18})*$/',
                new NotBlockedNumber,
            ],
            'email'  => ['nullable','regex:/^[^,\s]+@[^,\s]+\.[^,\s]+(,[^,\s]+@[^,\s]+\.[^,\s]+)*$/']
        ],[
            'mobile.regex' => 'Each phone number must be between 10 to 18 digits and comma separated.',
        ]);

         $data = $request->all();

        $data['mobile'] = !empty($data['mobile'])
            ? trim(preg_replace('/,+/', ',', preg_replace('/[\|\-_;\s]+/', ',', $data['mobile'])),',')
            : null;

        $data['email'] = !empty($data['email'])
            ? trim(preg_replace('/,+/', ',', preg_replace('/[\|\-_;\s]+/', ',', $data['email'])),',')
            : null;

        $job->update($data);
        // $job->update($request->all());

        return redirect()->route('part-time-jobs.index')
            ->with('success', 'Part-time job updated successfully');
    }

    public function destroy($id)
    {
        PartTimeJob::findOrFail($id)->delete();

        return redirect()->route('part-time-jobs.index')
            ->with('success', 'Part-time job deleted successfully');
    }

      public function importForm()
    {
        return view('part_time_jobs.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        \DB::beginTransaction();

        try {

            $importer = new \App\Imports\PartTimeJobImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

            // Validation failures
            $failures = $importer->failures();

            if ($failures->isNotEmpty()) {

                \DB::rollBack();

                $messages = [];

                foreach ($failures as $failure) {
                    $messages[] =
                        "Row {$failure->row()} – {$failure->attribute()} – " .
                        implode(', ', $failure->errors());
                }

                return back()->withErrors($messages);
            }

            \DB::commit();

            // Final counts
            $total    = $importer->totalRows;
            $inserted = $importer->insertedRows;
            $skipped  = $importer->skippedRows;

            $message = "From {$total} rows: {$inserted} inserted successfully, {$skipped} skipped.";

            // Warnings
            $warnings = $importer->duplicateContacts;

            if (!empty($warnings)) {
                return back()
                    ->with('success', $message)
                    ->withErrors($warnings);
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            // dd($e);
            \DB::rollBack();

            return back()->withErrors([
                'Import failed: Something went wrong while importing the file.'
            ]);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(
            new PartTimeJobExport($request),
            'part_time_jobs.xlsx'
        );
    }
}
