<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollegeMistake;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Support\Str;

class CollegeMistakeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:college_mistake.view')->only('index');
        $this->middleware('permission:college_mistake.create')->only(['create','store']);
        $this->middleware('permission:college_mistake.edit')->only(['edit','update']);
        $this->middleware('permission:college_mistake.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        return view('college_mistakes.index');
    }

    public function data(Request $request)
    {
        $query = CollegeMistake::query();

        if ($request->college_name) {
            $query->where('college_name', 'LIKE', '%' . $request->college_name . '%');
        }

        if ($request->contact_person) {
            $query->where('contact_person', 'LIKE', '%' . $request->contact_person . '%');
        }

        if ($request->location) {
            $query->where('location', 'LIKE', '%' . $request->location . '%');
        }

        if ($request->website_status == 'with_website') {
            $query->whereNotNull('website')
                  ->where('website', '!=', '');
        }

        if ($request->website_status == 'without_website') {
            $query->where(function ($q) {
                $q->whereNull('website')
                  ->orWhere('website', '');
            });
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $query->latest();

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => ['id', 'college_name'],
            'searchable' => ['college_name', 'contact_person', 'location'],
        ], function ($mistake, $index, $start) {

            $actions  = '<a href="' . route('college-mistakes.edit', $mistake->id) . '" class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a> ';

            $actions .= '<form action="' . route('college-mistakes.destroy', $mistake->id) . '" method="POST" style="display:inline-block;">'
                . csrf_field()
                . method_field('DELETE') .
                '<button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?">
                    <i class="fa fa-trash"></i>
                </button>
                </form>';

            $website = '-';

            if ($mistake->website) {
                $website = '<a href="' . e($mistake->website) . '" target="_blank">
                    Visit
                </a>';
            }

            return [
                $start + $index + 1,
                e($mistake->college_name),
                e($mistake->contact_person),
                e($mistake->location),
                $website,
                e(Str::limit($mistake->description, 60)),
                $mistake->created_at?->format('d M Y'),
                $actions,
            ];
        });
    }

    public function create()
    {
        return view('college_mistakes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_name'   => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'location'       => 'nullable|string|max:255',
            'website'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
        ]);

        CollegeMistake::create($validated);

        return redirect()
            ->route('college-mistakes.index')
            ->with('success', 'College mistake created successfully');
    }

    public function edit($id)
    {
        $mistake = CollegeMistake::findOrFail($id);

        return view('college_mistakes.edit', compact('mistake'));
    }

    public function update(Request $request, $id)
    {
        $mistake = CollegeMistake::findOrFail($id);

        $validated = $request->validate([
            'college_name'   => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'location'       => 'nullable|string|max:255',
            'website'        => 'nullable|string|max:255',
            'description'    => 'nullable|string',
        ]);

        $mistake->update($validated);

        return redirect()
            ->route('college-mistakes.index')
            ->with('success', 'College mistake updated successfully');
    }

    public function destroy(CollegeMistake $collegeMistake)
    {
        $collegeMistake->delete();

        return redirect()
            ->route('college-mistakes.index')
            ->with('success', 'College mistake deleted successfully');
    }
}