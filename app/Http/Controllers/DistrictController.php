<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Validation\Rule;


class DistrictController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:districts.view')->only('index');
        $this->middleware('permission:districts.create')->only(['create','store']);
        $this->middleware('permission:districts.edit')->only(['edit','update']);
        $this->middleware('permission:districts.delete')->only('destroy');
        // $this->middleware('permission:colleges.import')->only('showImport');
    }
    // Return JSON list of districts for a given state id
    public function getByState($stateId)
    {
        $districts = District::where('state_id', $stateId)->orderBy('name')->get(['id','name']);
        return response()->json($districts);
    }

    public function index(){
       $states = State::orderBy('name')->get();
        return view('districts.index', compact('states'));
    }
    
   public function data(Request $request)
{
    $query = District::with('state');

    // Apply state filter
    if ($request->filled('state_id')) {
        $query->where('state_id', $request->state_id);
    }

    return DataTablesServerSide::response($request, $query, [
        'orderable'  => ['id', 'name'],
        'searchable' => ['name'],
    ], function ($district) {

        $actions  = '<a href="' . route('districts.edit', $district->id) . '" 
                        class="btn btn-sm" 
                        title="Edit">
                        <i class="fa fa-edit"></i>
                     </a>';

        return [
            $district->id,
            optional($district->state)->name,
            $district->name,
            $actions,
        ];
    });
}

     

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $states = State::orderBy('name')->get();
        return view('districts.create', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->merge([
        'state_id' => trim($request->state_id),
        'name'     => trim($request->name),
    ]);

    $validated = $request->validate([
        'state_id' => 'required|exists:states,id',
        'name' => [
            'required',
            Rule::unique('districts')->where(function ($query) use ($request) {
                return $query->where('state_id', $request->state_id);
            }),
        ],
    ], [
        'name.unique' => 'District already exists for this state.'
    ]);

    District::create($validated);

    return redirect()
        ->route('states.index', ['tab' => 'districts'])
        ->with('success', 'District created successfully');
}

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        return view('districts.show', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {   
        $states = State::orderBy('name')->get();
        $district = District::findOrFail($id);
         
        return view('districts.edit', compact('states','district'));
    }


    /**
     * Update the specified resource in storage.
     */

public function update(Request $request, $id)
{
    $district = District::findOrFail($id);

    $request->merge([
        'state_id' => trim($request->state_id),
        'name'     => trim($request->name),
    ]);

    $validated = $request->validate([
        'state_id' => 'required|exists:states,id',

        'name' => [
            'required',
            Rule::unique('districts')
                ->where(function ($query) use ($request) {
                    return $query->where('state_id', $request->state_id);
                })
                ->ignore($district->id),
        ],
    ], [
        'name.unique' => 'District already exists for this state.'
    ]);

    $district->update($validated);

    return redirect()
        ->route('states.index', ['tab' => 'districts'])
        ->with('success', 'District updated successfully');
}

     /**
     * Remove the specified resource from storage.
     */
    public function destroy(District $district)
    {
        $district->delete();
        return redirect()
            ->route('states.index', ['tab' => 'districts'])
            ->with('success','District deleted successfully');
    }
}
