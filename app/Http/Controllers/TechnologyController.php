<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Technology;
use App\Http\DataTables\DataTablesServerSide;

class TechnologyController extends Controller
{
    protected string $permissionPrefix = 'interview_technology';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'data'         => 'view',
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
    
    // public function index()
    // {
    //     $technologies = Technology::orderBy('name')->get();
    //     return view('technologies.index', compact('technologies'));
    // }

    public function index()
    {
        return view('technologies.index');
    }

    public function data(Request $request)
    {
        $query = Technology::query();

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['name', 'category', 'is_active'],
            'searchable' => ['name', 'category'],
        ], function ($tech, $index, $start) {
            $category = '<span class="badge bg-info">' . e(ucfirst($tech->category)) . '</span>';
            $status = $tech->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>';
           $actions = '<a href="' . route('interview-questions.practice', ['technology_id' => $tech->id]) . '" class="btn btn-sm" title="Practice"><i class="fas fa-eye"></i></a>';

            $actions .= '<a href="' . route('technologies.edit', $tech) . '" class="btn btn-sm" title="Edit"><i class="fas fa-edit"></i></a> ';
            $actions .= '<form action="' . route('technologies.destroy', $tech) . '" method="POST" style="display:inline;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Delete this technology?"><i class="fas fa-trash"></i></button></form>';
            $rowNum = $start + $index + 1;
            return [
                $rowNum,
                e($tech->name),
                $category,
                $status,
                $actions,
            ];
        });
    }

    public function create()
    {
        return view('technologies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:technologies,name',
            'category' => 'required'
        ]);

        Technology::create($request->all());

        return redirect()->route('technologies.index')
            ->with('success', 'Technology added');
    }

    public function edit(Technology $technology)
    {
        return view('technologies.edit', compact('technology'));
    }

    public function update(Request $request, Technology $technology)
    {
        $request->validate([
            'name' => 'required|unique:technologies,name,' . $technology->id,
            'category' => 'required'
        ]);

        $technology->update($request->all());

        return redirect()->route('technologies.index')
            ->with('success', 'Technology updated');
    }

    public function destroy(Technology $technology)
    {
        $technology->delete();
        return back()->with('success', 'Technology deleted');
    }
}

