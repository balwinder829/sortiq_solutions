<?php

namespace App\Http\Controllers;

use App\Models\OfficeCleaningExpense;
use Illuminate\Http\Request;

class OfficeCleaningExpenseController extends Controller
{

    protected string $permissionPrefix = 'office_cleaning_expenses';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'download'         => 'view',
        'sendEmail'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }


    public function index(Request $request)
    {
        $query = OfficeCleaningExpense::query();

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

        return view('office-cleaning-expenses.index', compact('expenses'));
    }

    public function create()
    {
        return view('office-cleaning-expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'quantity'        => 'required',
            'total_amount'       => 'required|numeric|min:0',
            'other_charges'       => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        OfficeCleaningExpense::create($request->only([
            'expense_date', 'title','quantity', 'total_amount', 'other_charges', 'description'
        ]));

        return redirect()
            ->route('office-cleaning-expenses.index')
            ->with('success', 'Expenses added successfully');
    }

    public function show($id)
    {
        $expense = OfficeCleaningExpense::findOrFail($id);
        return view('office-cleaning-expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = OfficeCleaningExpense::findOrFail($id);
        return view('office-cleaning-expenses.edit', compact('expense'));
    }

    public function update(Request $request, $id)
    {
        $expense = OfficeCleaningExpense::findOrFail($id);

        $request->validate([
            'expense_date' => 'required|date',
            'title'        => 'required|string|max:255',
            'quantity'        => 'required',
            'total_amount'       => 'required|numeric|min:0',
            'other_charges'       => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $expense->update($request->only([
            'expense_date', 'title','quantity', 'total_amount', 'other_charges','description'
        ]));

        return redirect()
            ->route('office-cleaning-expenses.index')
            ->with('success', 'Expenses updated successfully');
    }

    public function destroy($id)
    {
        OfficeCleaningExpense::findOrFail($id)->delete();

        return redirect()
            ->route('office-cleaning-expenses.index')
            ->with('success', 'Expenses deleted successfully');
    }
}
