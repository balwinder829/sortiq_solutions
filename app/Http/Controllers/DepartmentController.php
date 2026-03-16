<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department; // Make sure you have a Department model
use App\Http\DataTables\DataTablesServerSide;

class DepartmentController extends Controller
{
    // Show all departments
    public function index()
    {
        // $departments = Department::all(); // Fetch all departments
        return view('departments.index', compact('departments'));
    }

    public function data(Request $request)
    {
        $query = Department::query();

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id', 'name', 'created_at'],
            'searchable' => ['name'],
        ], function ($department, $index, $start) {
            $actions = '<a href="' . route('departments.edit', $department->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';
            $actions .= '<form action="' . route('departments.destroy', $department->id) . '" method="POST" style="display:inline-block;">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?"><i class="fa fa-trash"></i></button></form>';
            return [
                $department->id,
                e($department->name),
                optional($department->created_at)->format('Y-m-d'),
                $actions,
            ];
        });
    }

     public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Department::create([
            'name' => $request->name,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->update([
            'name' => $request->name,
        ]);

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
