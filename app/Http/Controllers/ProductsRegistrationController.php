<?php

namespace App\Http\Controllers;

use App\Models\ProductsRegistration;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Exports\ProductsRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\NotBlockedNumber;
 use App\Http\DataTables\DataTablesServerSide;
 use Illuminate\Support\Str;

class ProductsRegistrationController extends Controller
{

    protected string $permissionPrefix = 'products_registrations';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'export'         => 'view',
    ];

    public function __construct()
    {
        // $this->middleware('auth');
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
        // ✅ AJAX → DataTable
        if ($request->ajax()) {

            $query = ProductsRegistration::with('courseData')
            ->latest();

            // ✅ FILTERS
            if ($request->technology) {
                $query->where('technology', $request->technology);
            }

            if ($request->slug) {
                $query->where('slug', 'like', '%' . $request->slug . '%');
            }

         
            

            return DataTablesServerSide::response($request, $query, [
                'orderable'  => ['id','full_name','email'],
                'searchable' => ['full_name','email','phone'],
            ], function ($row, $index, $start) {

                $actions = '
                    <a href="' . route('products-registrations.show', $row->id) . '" class="btn btn-sm">
                        <i class="fa fa-eye"></i>
                    </a>

                    <form action="' . route('products-registrations.destroy', $row->id) . '" 
                          method="POST" 
                          style="display:inline-block;"
                          >

                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '

                        <button type="submit" class="btn btn-sm ">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>

                    
                ';
                $rowNum = $start + $index + 1;
                return [
                    $rowNum,
                    e($row->full_name),
                    e($row->email),
                    e($row->phone ?? '-'),
                    e($row->location ?? '-'),
                    e($row->technology),
                    $row->created_at->format('d M Y'),
                    $actions
                ];
            });
        }

        // ✅ NORMAL PAGE LOAD (NO CHANGE)
        $technologies = Course::orderBy('course_name')->get([
            'id',
            'course_name'
        ]);

        $slugs = ProductsRegistration::select('slug')
            ->distinct()
            ->orderBy('slug')
            ->pluck('slug');

        return view('products-registrations.index', compact('technologies','slugs'));
    }
 
    public function show(ProductsRegistration $products_registration)
    {
        $services_registration = $products_registration;
        return view('products-registrations.show', compact('services_registration'));
    }

    public function destroy(ProductsRegistration $products_registration)
    {
        $products_registration->delete();

        return back()->with('success', 'Registration deleted successfully');
    }

    public function export(Request $request)
    {
        $fileName = 'products_registration';

        if ($request->technology) {
            $fileName .= '_' . Str::slug($request->technology, '_');
        }

        if ($request->slug) {
            $fileName .= '_' . Str::slug($request->slug, '_');
        }

        if ($request->limit) {
            $fileName .= '_limit_' . $request->limit;
        }

        $fileName .= '_' . now()->format('d_F') . '.xlsx';
        return Excel::download(
            new ProductsRegistrationsExport($request),
            $fileName
        );
    }
}
