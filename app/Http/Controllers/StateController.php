<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('permission:states.view')->only('index');
        $this->middleware('permission:states.create')->only(['create','store']);
        $this->middleware('permission:states.edit')->only(['edit','update']);
        $this->middleware('permission:states.delete')->only('destroy');
        // $this->middleware('permission:colleges.import')->only('showImport');
    }

    // public function index(){
    //     return view('states.index');
    // }

    public function index(Request $request)
    {
        $states = State::orderBy('name')->get();

        // 👉 If AJAX request → return only partial
        if ($request->ajax() || $request->get('ajax')) {

            if ($request->get('tab') == 'districts') {
                return view('districts.districts_table', compact('states'));
            }

            return view('states.states_table');
        }

        // 👉 Normal load
        return view('states.master', compact('states'));
    }
    public function index5may(){
        $states = State::orderBy('name')->get(); // needed for district filter
        return view('states.master', compact('states'));
    }
    
    public function data(Request $request)
{
    $query = State::query();

    return DataTablesServerSide::response($request, $query, [
        'orderable'  => ['id', 'name', 'code'],
        'searchable' => ['name', 'code'],
    ], function ($state) {

        $actions  = '<a href="' . route('states.edit', $state->id) . '" 
                        class="btn btn-sm" 
                        title="Edit">
                        <i class="fa fa-edit"></i>
                     </a>';

        return [
            $state->id,
            e($state->name),
            e($state->code),
            $actions,
        ];
    });
}

     

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        return view('states.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'name' => trim($request->name),
            'code' => trim($request->code),
        ]);

        $validated = $request->validate([
            'name' => 'required|unique:states,name',
            'code' => 'required|unique:states,code',
        ]);

         
        State::create($validated);

        return redirect()
            ->route('states.index', ['tab' => 'states'])
            ->with('success','State created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(State $state)
    {
        return view('states.show', compact('state'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $state = State::findOrFail($id);
         
        return view('states.edit', compact('state'));
    }


    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
    {
        $state = State::findOrFail($id);

        $request->merge([
            'name' => trim($request->name),
            'code' => trim($request->code),
        ]);

        $validated = $request->validate([
            'name' => 'required|unique:states,name,' . $state->id,
            'code' => 'required|unique:states,code,' . $state->id,
        ]);

        $state->update($validated);

        return redirect()
            ->route('states.index', ['tab' => 'states'])
            ->with('success','State updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(State $state)
    // {
    //     $state->delete();
    //     return redirect()
    //         ->route('states.index', ['tab' => 'states'])
    //         ->with('success','State deleted successfully');
    // }
}
