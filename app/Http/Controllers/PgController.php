<?php

namespace App\Http\Controllers;

use App\Models\Pg;
use Illuminate\Http\Request;

class PgController extends Controller
{
    public function index(Request $request)
	{
	    $pgs = Pg::query();

	    if ($request->pg_type) {
	        $pgs->where('pg_type', $request->pg_type);
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
            'pg_type' => 'required|in:boys,girls',
            'food_type' => 'required|in:food,without_food',
            'status' => 'required|in:active,inactive',
        ]);

        Pg::create($request->all());

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
            'pg_type' => 'required|in:boys,girls',
            'food_type' => 'required|in:food,without_food',
            'status' => 'required|in:active,inactive',
        ]);

        $pg->update($request->all());

        return redirect()->route('pgs.index')
            ->with('success', 'PG updated successfully');
    }

    public function destroy($id)
    {
        Pg::findOrFail($id)->delete();

        return redirect()->route('pgs.index')
            ->with('success', 'PG deleted successfully');
    }
}
