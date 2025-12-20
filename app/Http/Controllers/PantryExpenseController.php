<?php

namespace App\Http\Controllers;

use App\Models\PantryExpense;
use Illuminate\Http\Request;

class PantryExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = PantryExpense::query();

        // Quick filters
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

        return view('pantry-expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('pantry-expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        PantryExpense::create($request->only([
            'expense_date', 'title', 'amount', 'description'
        ]));

        return redirect()
            ->route('pantry-expenses.index')
            ->with('success', 'Pantry expense added successfully');
    }

    public function show($id)
    {
        $expense = PantryExpense::findOrFail($id);
        return view('pantry-expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = PantryExpense::findOrFail($id);
        return view('pantry-expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $expense = PantryExpense::findOrFail($id);

        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $expense->update($request->only([
            'expense_date', 'title', 'amount', 'description'
        ]));

        return redirect()
            ->route('pantry-expenses.index')
            ->with('success', 'Pantry expense updated successfully');
    }

    public function destroy($id)
    {
        PantryExpense::findOrFail($id)->delete();

        return redirect()
            ->route('pantry-expenses.index')
            ->with('success', 'Pantry expense deleted successfully');
    }
}
