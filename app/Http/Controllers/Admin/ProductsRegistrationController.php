<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServicesRegistration;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Exports\ServicesRegistrationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\NotBlockedNumber;
 use App\Http\DataTables\DataTablesServerSide;

class ProductsRegistrationController extends Controller
{

    protected string $permissionPrefix = 'services_registrations';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'export'         => 'view',
    ];

    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('auth')->except(['create', 'store']);

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

            $query = ServicesRegistration::with('courseData');

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
                    <a href="' . route('services-registrations.show', $row->id) . '" class="btn btn-sm">
                        <i class="fa fa-eye"></i>
                    </a>

                    <form action="' . route('services-registrations.destroy', $row->id) . '" 
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
                    e(optional($row->courseData)->course_name),
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

        $slugs = ServicesRegistration::select('slug')
            ->distinct()
            ->orderBy('slug')
            ->pluck('slug');

        return view('services-registrations.index', compact('technologies','slugs'));
    }
public function index34(Request $request)
{
    // 👉 AJAX REQUEST (DataTable)
    if ($request->ajax()) {

        $query = ServicesRegistration::with('courseData');

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
                <a href="' . route('services-registrations.show', $row->id) . '" class="btn btn-sm">
                    <i class="fa fa-eye"></i>
                </a>

                <button type="button"
                    class="btn btn-sm btn-danger delete-btn"
                    data-id="' . $row->id . '">
                    <i class="fa fa-trash"></i>
                </button>
            ';

            return [
                $row->id,
                e($row->full_name),
                e($row->email),
                e($row->phone ?? '-'),
                e($row->location ?? '-'),
                e(optional($row->courseData)->course_name),
                $actions
            ];
        });
    }

    // 👉 NORMAL PAGE LOAD (Blade)
    $technologies = Course::orderBy('course_name')->get(['id','course_name']);

    $slugs = ServicesRegistration::select('slug')
        ->distinct()
        ->orderBy('slug')
        ->pluck('slug');

    return view('services-registrations.index', compact('technologies','slugs'));
}
    public function in2dex2(Request $request)
    {
        $query = ServicesRegistration::query()
            ->when($request->technology, function ($query) use ($request) {
                $query->where('technology', $request->technology);
            })
            ->when($request->slug, function ($query) use ($request) {
                $query->where('slug', 'like', '%' . $request->slug . '%');
            });
             

        $limit = $request->limit && $request->limit <= 100
        ? (int) $request->limit
        : 20;

    $registrations = $query
        ->latest()
        ->paginate($limit)
        ->withQueryString();

        $technologies = Course::orderBy('course_name')->get([
            'id',
            'course_name'
        ]);

         $slugs = ServicesRegistration::select('slug')
        ->distinct()
        ->orderBy('slug')
        ->pluck('slug');

        return view('services-registrations.index', compact('registrations', 'technologies','slugs'));
    }

   public function index2(Request $request)
    {
        $registrations = ServicesRegistration::query()
            ->when($request->technology, function ($query) use ($request) {
                $query->where('course_id', $request->technology);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $technologies = Course::orderBy('course_name')->get([
            'id',
            'course_name'
        ]);

        return view('services-registrations.index', compact('registrations', 'technologies'));
    }

    public function create()
    {
        return view('services-registrations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            // 'phone'      => 'required|string|max:50',
            'phone' => ['required', 'string', new NotBlockedNumber],
            'location'   => 'required|string|max:255',
            'technology' => 'required|string|max:255',
            'message'    => 'required|string',
            'slug'       => 'required|string|max:255',
        ]);

        // Prevent duplicate email + technology
        $exists = ServicesRegistration::where('email', $data['email'])
            ->where('technology', $data['technology'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['email' => 'This email is already registered for this technology.'])
                ->withInput();
        }

        // Add server-side data (SAFE)
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = substr($request->userAgent(), 0, 500);

        ServicesRegistration::create($data);

        return back()->with('success', 'Service request added successfully');
    }


    public function show(ServicesRegistration $services_registration)
    {
        return view('services-registrations.show', compact('services_registration'));
    }

    public function destroy(ServicesRegistration $services_registration)
    {
        $services_registration->delete();

        return back()->with('success', 'Registration deleted successfully');
    }

    public function export(Request $request)
    {
        return Excel::download(
            new ServicesRegistrationsExport($request),
            'services_registrations_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
