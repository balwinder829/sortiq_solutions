<?php

namespace App\Http\Controllers;

use App\Models\OfficeExpense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class OfficeExpenseController extends Controller
{
    /* ================= LIST ================= */

 public function index(Request $request)
{
    $query = OfficeExpense::query();

    // Convert today once (DATE string)
    $today = Carbon::now()->format('Y-m-d');

    /* ================= QUICK FILTER ================= */
    if ($request->filled('quick')) {

        if ($request->quick === 'today') {
            $query->where('expense_date', '=', $today);
        }

        elseif ($request->quick === 'yesterday') {
            $query->where(
                'expense_date',
                '=',
                Carbon::now()->subDay()->format('Y-m-d')
            );
        }

        elseif ($request->quick === '7days') {
            $query->whereBetween('expense_date', [
                Carbon::now()->subDays(6)->format('Y-m-d'),
                $today
            ]);
        }

        elseif ($request->quick === '1month') {
            $query->whereBetween('expense_date', [
                Carbon::now()->subMonth()->format('Y-m-d'),
                $today
            ]);
        }

    }
    /* ================= MANUAL DATE FILTER ================= */
    else {

        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }
    }

    /* ================= OTHER FILTERS ================= */
    if ($request->filled('title')) {
        $query->where('title', 'like', '%' . $request->title . '%');
    }

    /* ================= ORDER ================= */
    // $expenses = $query
    //     ->orderBy('expense_date', 'desc')
    //     ->orderBy('id', 'desc')
    //     ->get();

    $expenses = $query->get();
    // dd($expenses);

    return view('office_expenses.index', compact('expenses'));
}

    /* ================= CREATE ================= */
    public function create()
    {
        return view('office_expenses.create');
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'expense_date', 'title', 'amount', 'description'
        ]);

        /* Store Image in PUBLIC directory */
        if ($request->hasFile('image')) {
            $imageName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(public_path('expenses'), $imageName);
            $data['image'] = 'expenses/'.$imageName;
        }

        OfficeExpense::create($data);

        return redirect()
            ->route('office-expenses.index')
            ->with('success', 'Expense added successfully');
    }

    /* ================= EDIT ================= */
    public function edit($id)
    {
        $expense = OfficeExpense::findOrFail($id);
        return view('office_expenses.edit', compact('expense'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $id)
    {
        $expense = OfficeExpense::findOrFail($id);

        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'expense_date', 'title', 'amount', 'description'
        ]);

        /* Replace image if uploaded */
        if ($request->hasFile('image')) {

            // Delete old image
            if ($expense->image && File::exists(public_path($expense->image))) {
                File::delete(public_path($expense->image));
            }

            $imageName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(public_path('expenses'), $imageName);
            $data['image'] = 'expenses/'.$imageName;
        }

        $expense->update($data);

        return redirect()
            ->route('office-expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    public function show($id)
    {
        $expense = OfficeExpense::findOrFail($id);

        return view('office_expenses.show', compact('expense'));
    }

    /* ================= SOFT DELETE ================= */
    public function destroy($id)
    {
        $expense = OfficeExpense::findOrFail($id);
        $expense->delete(); // Soft delete

        return redirect()
            ->route('office-expenses.index')
            ->with('success', 'Expense deleted successfully');
    }
}
