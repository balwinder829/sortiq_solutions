<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\State;
use App\Models\District;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
class CollegeController extends Controller
{
    public function index()
    {
        // $colleges = College::all();
        $colleges = College::with(['state','district'])->get();
        $states = State::orderBy('name')->get();

$districtsGrouped = District::select('districts.id','districts.name','districts.state_id','states.name as state_name')
    ->join('states', 'states.id', '=', 'districts.state_id')
    ->orderBy('districts.name')
    ->get()
    ->groupBy('state_id');

        return view('colleges.index', compact('colleges', 'states', 'districtsGrouped'));
    }

    public function create()
    {   
        $states = State::orderBy('name')->get();
        return view('colleges.create', compact('states'));
        // return view('colleges.create');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'college_name' => 'required|string|max:255',
    //     ]);

    //     College::create($request->all());

    //     return redirect()->route('colleges.index')
    //                      ->with('success', 'College created successfully.');
    // }

public function store(Request $request)
{
    $data = $request->validate([
        'college_name' => 'required|string|max:255',
        'state_id' => 'required|exists:states,id',
        'district_id' => 'required|exists:districts,id',
    ]);

    $cleanName = College::clean($request->college_name);

    // Check duplicate using clean_name
    // if (College::where('clean_name', $cleanName)->withTrashed()->exists()) {
    //     return back()->withErrors(['college_name' => 'College already exists.'])->withInput();
    // }

    $exists = College::withTrashed()
        ->where('clean_name', $cleanName)
        ->where('state_id', $data['state_id'])
        ->where('district_id', $data['district_id'])
        ->exists();

    if ($exists) {
        return back()
            ->withErrors([
                'college_name' => 'This college already exists in the selected state and district.'
            ])
            ->withInput();
    }

    College::create([
        'college_name' => $request->college_name,
        'clean_name'   => $cleanName,
        'slug'         => null, // model will generate slug
        'state_id'  => $request->state_id,
        'district_id'  => $request->district_id,
    ]);

    return redirect()->route('colleges.index')->with('success', 'College added successfully.');
}


    public function show(College $college)
    {
        return view('colleges.show', compact('college'));
    }

    public function edit(College $college)
    {   
         $states = State::orderBy('name')->get();
         // districts for the selected state (so edit form can pre-load)
        $districts = $college->state ? $college->state->districts()->orderBy('name')->get() : collect();
         return view('colleges.edit', compact('college','states','districts'));
    }

    // public function update(Request $request, College $college)
    // {
    //     $request->validate([
    //         'college_name' => 'required|string|max:255',
    //     ]);

    //     $college->update($request->all());

    //     return redirect()->route('colleges.index')->with('success', 'College updated successfully.');
    // }

    public function update(Request $request, $id)
{
     $data = $request->validate([
        'college_name' => 'required|string|max:255',
        'state_id' => 'required|exists:states,id',
        'district_id' => 'required|exists:districts,id',
    ]);

    $college = College::findOrFail($id);

    $cleanName = College::clean($request->college_name);

    // Check duplicates except current ID
    // $exists = College::where('clean_name', $cleanName)
    //                  ->where('id', '!=', $id)
    //                  ->withTrashed()
    //                  ->exists();

    // if ($exists) {
    //     return back()->withErrors(['college_name' => 'College already exists.'])->withInput();
    // }

     $exists = College::withTrashed()
        ->where('clean_name', $cleanName)
        ->where('state_id', $data['state_id'])
        ->where('district_id', $data['district_id'])
        ->where('id', '!=', $college->id)
        ->exists();

    if ($exists) {
        return back()
            ->withErrors([
                'college_name' => 'This college already exists in the selected state and district.'
            ])
            ->withInput();
    }


    // Reset slug to regenerate if college name changed
    $college->update([
        'college_name' => $request->college_name,
        'clean_name'   => $cleanName,
        'slug'         => null,
        'state_id'  => $request->state_id,
        'district_id'  => $request->district_id,
    ]);

    return redirect()->route('colleges.index')->with('success', 'College updated successfully.');
}




    public function destroy(College $college)
    {
        $college->delete();

        return redirect()->route('colleges.index')
                         ->with('success', 'College deleted successfully.');
    }
}
