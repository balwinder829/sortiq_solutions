<?php

namespace App\Http\Controllers;

use App\Models\SingleProductRegistration;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Exports\ServicesRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\NotBlockedNumber;
 use App\Http\DataTables\DataTablesServerSide;

class SingleProductRegistrationController extends Controller
{

    protected string $permissionPrefix = 'single_product_registration';

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

            $query = SingleProductRegistration::with('courseData');

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
            ], function ($row) {

                $actions = '
                    <a href="' . route('single-product-registrations.show', $row->id) . '" class="btn btn-sm">
                        <i class="fa fa-eye"></i>
                    </a>

                    <form action="' . route('single-product-registrations.destroy', $row->id) . '" 
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

                return [
                    $row->id,
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

        $slugs = SingleProductRegistration::select('slug')
            ->distinct()
            ->orderBy('slug')
            ->pluck('slug');

        return view('single-product-registrations.index', compact('technologies','slugs'));
    }
 
    public function show(SingleProductRegistration $single_product_registration)
    {
        $services_registration = $single_product_registration;
        return view('single-product-registrations.show', compact('services_registration'));
    }

    public function destroy(SingleProductRegistration $single_product_registration)
    {
        $single_product_registration->delete();

        return back()->with('success', 'Registration deleted successfully');
    }

    public function export(Request $request)
    {
        return Excel::download(
            new SingleProductRegistration($request),
            'single_product_registrations_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
