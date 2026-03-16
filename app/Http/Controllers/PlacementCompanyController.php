<?php

namespace App\Http\Controllers;

use App\Models\PlacementCompany;
use Illuminate\Http\Request;

use App\Imports\PlacementCompanyImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\View;
class PlacementCompanyController extends Controller
{   
    protected string $permissionPrefix = 'placement_companies';

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

    public function index()
    {
        $companies = PlacementCompany::orderBy('name')->get();
        return view('placement_companies.index', compact('companies'));
    }

    public function create()
    {
        return view('placement_companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // email optional
            'email'   => ['nullable','string'],
            // 'phone' => ['required','regex:/^[0-9]{10}(,[0-9]{10})*$/'],
            'phone' => ['required','regex:/^[0-9]{10,18}(,[0-9]{10,18})*$/'],
            'website' => 'nullable|url',
            'status' => 'required',
        ],[
            'phone.regex' => 'Each phone number must be between 10 to 18 digits and comma separated.',
        ]);

        $data = $request->all();

        $data['phone'] = isset($data['phone'])
            ? implode(',', array_map('trim', explode(',', $data['phone'])))
            : null;

        $data['email'] = isset($data['email'])
            ? implode(',', array_map('trim', explode(',', $data['email'])))
            : null;

        // PlacementCompany::create($request->all());
        PlacementCompany::create($data);

        return redirect()->route('placement-companies.index')
            ->with('success', 'Company added successfully');
    }

    public function edit($id)
    {
        $company = PlacementCompany::findOrFail($id);
        return view('placement_companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $company = PlacementCompany::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
             'email'   => ['nullable','string'],
            // 'phone' => ['nullable','regex:/^[0-9]{10}(,[0-9]{10})*$/'],
            'phone' => ['required','regex:/^[0-9]{10,18}(,[0-9]{10,18})*$/'],
            
            'website' => 'nullable|url',
            'status' => 'required',
        ],[
            'phone.regex' => 'Each phone number must be between 10 t 18 digits and comma separated.',
        ]);

        $data = $request->all();

        // ✅ Clean phone values
        $data['phone'] = isset($data['phone'])
            ? implode(',', array_map('trim', explode(',', $data['phone'])))
            : null;

        // ✅ Clean email values
        $data['email'] = isset($data['email'])
            ? implode(',', array_map('trim', explode(',', $data['email'])))
            : null;

        $company->update($data);
        // $company->update($request->all());

        return redirect()->route('placement-companies.index')
            ->with('success', 'Company updated successfully');
    }

    public function show($id)
    {
        $company = PlacementCompany::findOrFail($id);

        return view('placement_companies.show', compact('company'));
    }

    public function destroy($id)
    {
        PlacementCompany::findOrFail($id)->delete();

        return redirect()->route('placement-companies.index')
            ->with('success', 'Company deleted successfully');
    }

     public function importForm()
    {
        return view('placement_companies.import');
    }
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        \DB::beginTransaction();

        try {

            $importer = new \App\Imports\PlacementCompanyImport();
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
}
