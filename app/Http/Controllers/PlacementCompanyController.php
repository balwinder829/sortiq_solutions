<?php

namespace App\Http\Controllers;

use App\Models\PlacementCompany;
use Illuminate\Http\Request;

class PlacementCompanyController extends Controller
{
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
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'status' => 'required',
        ]);

        PlacementCompany::create($request->all());

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
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'status' => 'required',
        ]);

        $company->update($request->all());

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
}
