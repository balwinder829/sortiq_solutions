<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Sim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Rules\NotBlockedNumber;
use App\Http\DataTables\DataTablesServerSide;

class SimController extends Controller
{
    protected string $permissionPrefix = 'sims';

    protected array $permissionMap = [
        'index'   => 'view',
        'show'    => 'view',

        'create'  => 'create',
        'store'   => 'create',

        'edit'    => 'edit',
        'update'  => 'edit',

        'destroy' => 'delete',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }

    /**
     * Display SIM listing.
     */
    public function index(Request $request)
    {
        $query = Sim::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('q')) {

            $q = $request->input('q');

            $query->where(function ($qry) use ($q) {

                $qry->where(
                    'mobile_number',
                    'like',
                    "%{$q}%"
                )

                ->orWhere(
                    'operator',
                    'like',
                    "%{$q}%"
                )

                ->orWhere(
                    'name',
                    'like',
                    "%{$q}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->input('status')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AJAX DataTable
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {

            return DataTablesServerSide::response(
                $request,
                $query,

                [
                    'orderable' => [
                        'id',
                        'mobile_number',
                        'operator',
                        'name',
                        'status',
                        'issued_at',
                    ],

                    'searchable' => [
                        'mobile_number',
                        'operator',
                        'name',
                        'status',
                    ],
                ],

                function ($sim) {

                    $actions = '';

                    /*
                    |--------------------------------------------------------------------------
                    | Edit
                    |--------------------------------------------------------------------------
                    */

                    $actions .= '
                        <a href="' . route('sims.edit', $sim) . '"
                           class="btn btn-sm"
                           data-bs-toggle="tooltip"
                           title="Edit">

                            <i class="fas fa-edit"></i>

                        </a>
                    ';

                    /*
                    |--------------------------------------------------------------------------
                    | Delete
                    |--------------------------------------------------------------------------
                    */

                    $actions .= '
                        <form action="' . route('sims.destroy', $sim) . '"
                              method="POST"
                              class="d-inline">

                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '

                            <button
                                type="submit"
                                class="btn btn-sm"
                                data-swal-confirm="Delete SIM?"
                                data-bs-toggle="tooltip"
                                title="Delete">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>
                    ';

                    return [

                        $sim->id,

                        e($sim->mobile_number),

                        e($sim->operator),

                        e($sim->name ?? ''),

                        $sim->status === 'active'
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-secondary">Deactive</span>',

                        $sim->issued_at
                            ? $sim->issued_at->format('d M Y')
                            : '',

                        $actions,
                    ];
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Normal Page Load
        |--------------------------------------------------------------------------
        */

        $sims = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return view(
            'sims.index',
            compact('sims')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('sims.create');
    }

    /**
     * Store SIM.
     */
    public function store(Request $request)
    {
        $data = $request->validate([

            'mobile_number' => [
                'required',
                'string',
                'max:20',
                new NotBlockedNumber,
            ],

            'operator' => [
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:active,deactive',
            ],

            'issued_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $data['created_by'] = auth()->id();

        DB::beginTransaction();

        try {

            Sim::create($data);

            DB::commit();

            return redirect()
                ->route('sims.index')
                ->with(
                    'success',
                    'SIM created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                        'Failed to create SIM: '
                        . $e->getMessage()
                ]);
        }
    }

    /**
     * Display SIM details.
     */
    public function show(Sim $sim)
    {
        return view(
            'sims.show',
            compact('sim')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Sim $sim)
    {
        return view(
            'sims.edit',
            compact('sim')
        );
    }

    /**
     * Update SIM.
     */
    public function update(Request $request, Sim $sim)
    {
        $data = $request->validate([

            'mobile_number' => [
                'required',
                'string',
                'max:20',
                new NotBlockedNumber,
            ],

            'operator' => [
                'required',
                'string',
                'max:100',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:active,deactive',
            ],

            'issued_at' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        DB::beginTransaction();

        try {

            $sim->update($data);

            DB::commit();

            return redirect()
                ->route('sims.index')
                ->with(
                    'success',
                    'SIM updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                        'Failed to update SIM: '
                        . $e->getMessage()
                ]);
        }
    }

    /**
     * Delete SIM.
     */
    public function destroy(Sim $sim)
    {
        $sim->delete();

        return redirect()
            ->route('sims.index')
            ->with(
                'success',
                'SIM deleted successfully.'
            );
    }

    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids, true);

        if (empty($ids) || !is_array($ids)) {
            return redirect()
                ->route('sims.index')
                ->with('error', 'Please select at least one SIM.');
        }

        Sim::whereIn('id', $ids)->delete();

        return redirect()
            ->route('sims.index')
            ->with('success', 'Selected SIMs deleted successfully.');
    }
}