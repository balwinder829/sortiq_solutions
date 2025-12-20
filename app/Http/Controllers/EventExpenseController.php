<?php

namespace App\Http\Controllers;

use App\Models\EventExpense;
use Illuminate\Http\Request;

class EventExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = EventExpense::query();

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

        return view('event-expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('event-expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        EventExpense::create($request->only([
            'expense_date',
            'title',
            'amount',
            'description'
        ]));

        return redirect()
            ->route('event-expenses.index')
            ->with('success', 'Event cost added successfully');
    }

    public function show($id)
    {
        $expense = EventExpense::findOrFail($id);
        return view('event-expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = EventExpense::findOrFail($id);
        return view('event-expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $expense = EventExpense::findOrFail($id);

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
            ->route('event-expenses.index')
            ->with('success', 'Event cost updated successfully');
    }

    public function destroy($id)
    {
        EventExpense::findOrFail($id)->delete();

        return redirect()
            ->route('event-expenses.index')
            ->with('success', 'Event cost deleted successfully');
    }
}
