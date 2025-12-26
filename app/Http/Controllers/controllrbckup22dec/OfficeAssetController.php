<?php

namespace App\Http\Controllers;

use App\Models\OfficeAsset;
use Illuminate\Http\Request;

class OfficeAssetController extends Controller
{
    public function index(Request $request)
    {
        $query = OfficeAsset::query();

        if ($request->quick) {
            match ($request->quick) {
                'today'     => $query->whereDate('expense_date', today()),
                'yesterday' => $query->whereDate('expense_date', today()->subDay()),
                '7days'     => $query->where('expense_date', '>=', today()->subDays(7)),
                '1month'    => $query->where('expense_date', '>=', today()->subMonth()),
                default     => null,
            };
        }

        if ($request->from_date) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        if ($request->title) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        $assets = $query->orderBy('expense_date', 'desc')->get();

        return view('office-assets.index', compact('assets'));
    }

    public function create()
    {
        return view('office-assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        OfficeAsset::create($request->only([
            'expense_date',
            'title',
            'amount',
            'description'
        ]));

        return redirect()
            ->route('office-assets.index')
            ->with('success', 'Office asset added successfully');
    }

    public function show($id)
    {
        $asset = OfficeAsset::findOrFail($id);
        return view('office-assets.show', compact('asset'));
    }

    public function edit($id)
    {
        $asset = OfficeAsset::findOrFail($id);
        return view('office-assets.edit', compact('asset'));
    }

    public function update(Request $request, $id)
    {
        $asset = OfficeAsset::findOrFail($id);

        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $asset->update($request->only([
            'expense_date',
            'title',
            'amount',
            'description'
        ]));

        return redirect()
            ->route('office-assets.index')
            ->with('success', 'Office asset updated successfully');
    }

    public function destroy($id)
    {
        OfficeAsset::findOrFail($id)->delete();

        return redirect()
            ->route('office-assets.index')
            ->with('success', 'Office asset deleted successfully');
    }
}
