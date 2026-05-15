<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopExpense;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;
use App\Models\StudentSession;

class WorkshopExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:workshop_expense.view')->only('index');
        $this->middleware('permission:workshop_expense.create')->only(['create','store']);
        $this->middleware('permission:workshop_expense.edit')->only(['edit','update']);
        $this->middleware('permission:workshop_expense.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $activeSessionNo = session('admin_session_id');

        $workshops = Workshop::with('college')
            ->where('session', $activeSessionNo)
            ->orderBy('title')
            ->get();

        return view('workshop_expenses.index', compact('workshops'));
    }

    public function data(Request $request)
    {
        $activeSessionNo = session('admin_session_id');

        $query = WorkshopExpense::with(['workshop.college'])
            ->whereHas('workshop', function ($q) use ($activeSessionNo) {
                $q->where('session', $activeSessionNo);
            });

        if ($request->workshop_id) {
            $query->where('workshop_id', $request->workshop_id);
        }

        if ($request->title) {
            $query->where('title', 'LIKE', '%' . $request->title . '%');
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $query->latest();

        return DataTablesServerSide::response($request, $query, [
            'orderable' => ['id', 'title', 'expense'],
            'searchable' => ['title', 'description'],
        ], function ($expense, $index, $start) {

            $actions  = '<a href="' . route('workshop-expenses.edit', $expense->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';

            $actions .= '<form action="' . route('workshop-expenses.destroy', $expense->id) . '" method="POST" style="display:inline-block;">'
                . csrf_field()
                . method_field('DELETE') .
                '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?">
                    <i class="fa fa-trash"></i>
                </button>
                </form>';

            return [
                $start + $index + 1,
                e(optional($expense->workshop)->title),
                e(optional(optional($expense->workshop)->college)->FullName),
                e($expense->title),
                number_format($expense->expense, 2),
                number_format($expense->other_expense, 2),
                e(\Illuminate\Support\Str::limit($expense->description, 50)),
                $expense->created_at?->format('d M Y'),
                $actions,
            ];
        });
    }

    public function create()
    {
        $activeSessionId = session('admin_session_id');

        $activeSession = StudentSession::find($activeSessionId);

        $workshops = Workshop::with('college')
            ->where('session', $activeSessionId)
            ->orderBy('title')
            ->get();

        return view('workshop_expenses.create', compact('workshops', 'activeSession'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'workshop_id'   => 'required|exists:workshops,id',
            'title'         => 'required|string|max:255',
            'expense'       => 'required|numeric|min:0',
            'other_expense' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
        ]);
        
        WorkshopExpense::create($validated);

        return redirect()
            ->route('workshop-expenses.index')
            ->with('success', 'Workshop expense created successfully');
    }

    public function edit($id)
    {
        $activeSessionId = session('admin_session_id');

        $activeSession = StudentSession::find($activeSessionId);

        $expense = WorkshopExpense::findOrFail($id);

        $workshops = Workshop::with('college')
            ->where('session', $activeSessionId)
            ->orderBy('title')
            ->get();

        return view('workshop_expenses.edit', compact(
            'expense',
            'workshops',
            'activeSession'
        ));
    }

    public function update(Request $request, $id)
    {
        $expense = WorkshopExpense::findOrFail($id);

        $validated = $request->validate([
            'workshop_id'   => 'required|exists:workshops,id',
            'title'         => 'required|string|max:255',
            'expense'       => 'required|numeric|min:0',
            'other_expense' => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
        ]);

        $expense->update($validated);

        return redirect()
            ->route('workshop-expenses.index')
            ->with('success', 'Workshop expense updated successfully');
    }

    public function destroy(WorkshopExpense $workshopExpense)
    {
        $workshopExpense->delete();

        return redirect()
            ->route('workshop-expenses.index')
            ->with('success', 'Workshop expense deleted successfully');
    }
}