<?php

namespace App\Http\Controllers;

use App\Models\Pg;
use Illuminate\Http\Request;
use App\Imports\PgImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
use App\Rules\NotBlockedNumber;
use App\Exports\PgExport;

class PgController extends Controller
{   
    protected string $permissionPrefix = 'pgs';

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
	    $pgs = Pg::query();

	    // if ($request->pg_type) {
	    //     $pgs->where('pg_type', $request->pg_type);
	    // }

        if ($request->pg_type) {
            $pgs->whereIn('pg_type', [$request->pg_type, 'both']);
        }

	    if ($request->food_type) {
	        $pgs->where('food_type', $request->food_type);
	    }

	    if ($request->status) {
	        $pgs->where('status', $request->status);
	    }

	    if ($request->address) {
	        $pgs->where('address', 'like', '%' . $request->address . '%');
	    }

	    $pgs = $pgs->orderBy('id', 'desc')->get();

	    return view('pgs.index', compact('pgs'));
	}


    public function create()
    {
        return view('pgs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'pg_type' => 'required|in:boys,girls,both',
             // 'contact' => 'required|digits:10',
            'food_type' => 'required|in:food,without_food',
            'status' => 'required|in:active,inactive',
            // 'contact' => ['required','regex:/^\d{10}(,\d{10})*$/'],
            // 'contact' => ['required','regex:/^[0-9]{10}(,[0-9]{10})*$/'],
            'contact' => [
                'required',
                'regex:/^[0-9]{10}(,[0-9]{10})*$/',
                new NotBlockedNumber,
            ],

            // 'email' => [
            //     'nullable',
            //     'regex:/^[^,\s]+@[^,\s]+\.[^,\s]+(,[^,\s]+@[^,\s]+\.[^,\s]+)*$/'
            // ],

        ],[
            'contact.regex' => 'Each phone number must be exactly 10 digits and comma separated.',
        ]);

         $data = $request->all();
        $data['contact'] = !empty($data['contact'])
        ? trim(preg_replace('/,+/', ',', preg_replace('/[\|\-_;\s]+/', ',', $data['contact'])),',')
        : null;

        $data['email'] = !empty($data['email'])
        ? trim(preg_replace('/,+/', ',', preg_replace('/[\|\-_;\s]+/', ',', $data['email'])),',')
        : null;

        Pg::create($data);
        // Pg::create($request->all());

        return redirect()->route('pgs.index')
            ->with('success', 'PG added successfully');
    }

    /* ✅ SHOW METHOD */
    public function show($id)
    {
        $pg = Pg::findOrFail($id);
        return view('pgs.show', compact('pg'));
    }

    public function edit($id)
    {
        $pg = Pg::findOrFail($id);
        return view('pgs.edit', compact('pg'));
    }

    public function update(Request $request, $id)
    {
        $pg = Pg::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'pg_type' => 'required|in:boys,girls,both',
             // 'contact' => 'required|digits:10',
            // 'contact' => ['required','regex:/^\d{10}(,\d{10})*$/'],
            // 'contact' => ['required','regex:/^[0-9]{10}(,[0-9]{10})*$/'],
            'contact' => [
                'required',
                'regex:/^[0-9]{10}(,[0-9]{10})*$/',
                new NotBlockedNumber,
            ],
            'food_type' => 'required|in:food,without_food',
            'status' => 'required|in:active,inactive',
        ],[
            'contact.regex' => 'Each phone number must be exactly 10 digits and comma separated.',
        ]);

        $data = $request->all();

        $data['contact'] = !empty($data['contact'])
        ? trim(preg_replace('/,+/', ',', preg_replace('/[\|\-_;\s]+/', ',', $data['contact'])),',')
        : null;

        $data['email'] = !empty($data['email'])
        ? trim(preg_replace('/,+/', ',', preg_replace('/[\|\-_;\s]+/', ',', $data['email'])),',')
        : null;

        $pg->update($data);

        return redirect()->route('pgs.index')
            ->with('success', 'PG updated successfully');
    }

    public function destroy($id)
    {
        Pg::findOrFail($id)->delete();

        return redirect()->route('pgs.index')
            ->with('success', 'PG deleted successfully');
    }

      public function importForm()
    {
        return view('pgs.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        \DB::beginTransaction();

        try {

            $importer = new \App\Imports\PgImport();
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
            new PgExport($request),
            'pgs.xlsx'
        );
    }
}
