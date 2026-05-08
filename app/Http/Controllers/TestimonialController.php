<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\DataTables\DataTablesServerSide;

class TestimonialController extends Controller
{
    protected string $permissionPrefix = 'testimonial';

    protected array $permissionMap = [
        'index' => 'view',
        'create' => 'create',
        'store' => 'create',
        'destroy' => 'delete',
        'edit' => 'edit',
        'update' => 'edit',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        foreach ($this->permissionMap as $method => $action) {
            $this->middleware("permission:{$this->permissionPrefix}.{$action}")
                ->only($method);
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Testimonial::query();

            // ✅ TYPE FILTER
            if ($request->type) {
                $query->where('type', $request->type);
            }

            // ✅ STATUS FILTER
            if ($request->status !== null && $request->status !== '') {
                $query->where('status', $request->status);
            }

            return DataTablesServerSide::response($request, $query, [
                'orderable'  => ['id','name'],
                'searchable' => ['name','description'],
            ], function ($row, $index, $start) {

                $image = $row->image && file_exists(public_path($row->image))
                ? '<img src="'.asset($row->image).'" width="60" height="60" style="object-fit:cover;border-radius:6px;">'
                : '<span class="text-muted">No Image</span>';

                $status = $row->status
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';

                $actions = '
                     <a href="' . route('testimonials.edit', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                    </a>

                    <form action="' . route('testimonials.destroy', $row->id) . '" 
                        method="POST" style="display:inline-block;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                ';
                $rowNum = $start + $index + 1;
                return [
                    $rowNum,
                    e($row->name),
                    $image,
                    e($row->type),
                    $status,
                    $actions
                ];
            });
        }

        return view('testimonials.index');
    }

    public function create()
    {
        return view('testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'type' => 'required|in:services,internship',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            // folder path
            $folder = public_path('testimonial_images');

            // create folder if not exists
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            // unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // move file
            $file->move($folder, $filename);

            // save path in DB
            $data['image'] = 'testimonial_images/' . $filename;
        }

        $data['status'] = $request->has('status') ? 1 : 0;

        Testimonial::create($data);

        return redirect()->route('testimonials.index')->with('success', 'Testimonial added');
    }

    public function edit(Testimonial $testimonial)
{
    return view('testimonials.edit', compact('testimonial'));
}

public function update(Request $request, Testimonial $testimonial)
{
    $data = $request->validate([
        'name' => 'required',
        'description' => 'required',
        'type' => 'required|in:services,internship',
        'image' => 'nullable|image',
    ]);

    if ($request->hasFile('image')) {

        // delete old image
        if ($testimonial->image && file_exists(public_path($testimonial->image))) {
            unlink(public_path($testimonial->image));
        }

        $file = $request->file('image');
        $folder = public_path('testimonial_images');

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($folder, $filename);

        $data['image'] = 'testimonial_images/' . $filename;
    }

    $data['status'] = $request->has('status') ? 1 : 0;

    $testimonial->update($data);

    return redirect()->route('testimonials.index')
        ->with('success', 'Updated successfully');
}
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with('success', 'Deleted successfully');
    }
}