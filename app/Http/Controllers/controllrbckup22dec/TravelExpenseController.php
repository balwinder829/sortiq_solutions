<?php

namespace App\Http\Controllers;

use App\Models\TravelExpense;
use Illuminate\Http\Request;

class TravelExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = TravelExpense::query();

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

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        return view('travel-expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('travel-expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        TravelExpense::create($request->only([
            'expense_date',
            'title',
            'amount',
            'description'
        ]));

        return redirect()
            ->route('travel-expenses.index')
            ->with('success', 'Travel expense added successfully');
    }

    public function show($id)
    {
        $expense = TravelExpense::findOrFail($id);
        return view('travel-expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = TravelExpense::findOrFail($id);
        return view('travel-expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $expense = TravelExpense::findOrFail($id);

        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $expense->update($request->only([
            'expense_date',
            'title',
            'amount',
            'description'
        ]));

        return redirect()
            ->route('travel-expenses.index')
            ->with('success', 'Travel expense updated successfully');
    }

    // SOFT DELETE
    public function destroy($id)
    {
        TravelExpense::findOrFail($id)->delete();

        return redirect()
            ->route('travel-expenses.index')
            ->with('success', 'Travel expense deleted successfully');
    }
}
