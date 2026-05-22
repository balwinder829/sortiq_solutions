<?php

namespace App\Http\Controllers\Finance;
use App\Http\Controllers\Controller;

use App\Models\TravelExpense;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;

class TravelExpenseController extends Controller
{
    protected string $permissionPrefix = 'travel_expenses';

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
    $query = TravelExpense::query();

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

    if ($request->from_date) {

        $query->whereDate(
            'expense_date',
            '>=',
            $request->from_date
        );
    }

    if ($request->to_date) {

        $query->whereDate(
            'expense_date',
            '<=',
            $request->to_date
        );
    }

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
                    <a href="' . route('travel-expenses.show', $expense->id) . '"
                       class="btn btn-sm"
                       title="View">

                        <i class="fa fa-eye"></i>

                    </a>';

                // Edit
                $actions .= '
                    <a href="' . route('travel-expenses.edit', $expense->id) . '"
                       class="btn btn-sm"
                       title="Edit">

                        <i class="fa fa-edit"></i>

                    </a>';

                // Delete
                $actions .= '
                    <form action="' . route('travel-expenses.destroy', $expense->id) . '"
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
        'travel-expenses.index',
        compact('expenses')
    );
}

    public function index14may(Request $request)
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
