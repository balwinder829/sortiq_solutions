<?php

namespace App\Http\Controllers;

use App\Models\PantryExpense;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;

class PantryExpenseController extends Controller
{

    protected string $permissionPrefix = 'pantry_expenses';

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
    $query = PantryExpense::query();

    // Quick filters
    if ($request->quick) {

        match ($request->quick) {

            'today' =>
                $query->whereDate(
                    'expense_date',
                    today()
                ),

            'yesterday' =>
                $query->whereDate(
                    'expense_date',
                    today()->subDay()
                ),

            '7days' =>
                $query->where(
                    'expense_date',
                    '>=',
                    today()->subDays(7)
                ),

            '1month' =>
                $query->where(
                    'expense_date',
                    '>=',
                    today()->subMonth()
                ),

            default => null,
        };
    }

    // From date
    if ($request->from_date) {

        $query->whereDate(
            'expense_date',
            '>=',
            $request->from_date
        );
    }

    // To date
    if ($request->to_date) {

        $query->whereDate(
            'expense_date',
            '<=',
            $request->to_date
        );
    }

    // Title
    if ($request->title) {

        $query->where(
            'title',
            'like',
            '%' . $request->title . '%'
        );
    }

    /* ================= AJAX DATATABLE ================= */

    if ($request->ajax()) {

        return DataTablesServerSide::response(

            $request,

            $query,

            [

                'orderable' => [
                    'id',
                    'expense_date',
                    'title',
                    'amount'
                ],

                'searchable' => [
                    'title',
                    'amount'
                ],
            ],

            function ($expense) {

                $actions = '';

                // View
                $actions .= '
                    <a href="' . route('pantry-expenses.show', $expense->id) . '"
                       class="btn btn-sm"
                       title="View">

                        <i class="fa fa-eye"></i>

                    </a>';

                // Edit
                $actions .= '
                    <a href="' . route('pantry-expenses.edit', $expense->id) . '"
                       class="btn btn-sm"
                       title="Edit">

                        <i class="fa fa-edit"></i>

                    </a>';

                // Delete
                $actions .= '
                    <form action="' . route('pantry-expenses.destroy', $expense->id) . '"
                          method="POST"
                          class="d-inline">

                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '

                        <button class="btn btn-sm"
                                data-swal-confirm="Are you sure?"
                                title="Delete">

                            <i class="fa fa-trash"></i>

                        </button>

                    </form>';

                return [

                    $expense->id,

                    \Carbon\Carbon::parse(
                        $expense->expense_date
                    )->format('d M Y'),

                    e($expense->title),

                    number_format(
                        $expense->amount,
                        2
                    ),

                    $actions
                ];
            }
        );
    }

    $expenses = $query
        ->orderBy('expense_date', 'desc')
        ->get();

    return view(
        'pantry-expenses.index',
        compact('expenses')
    );
}

    public function index14may(Request $request)
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
