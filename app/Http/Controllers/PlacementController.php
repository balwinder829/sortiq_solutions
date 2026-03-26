<?php

namespace App\Http\Controllers;

use App\Models\Placement;
use App\Models\PlacementImage;
use App\Models\PlacementVideo;
use Illuminate\Http\Request;

use App\Models\StudentSession;
use App\Models\College;
use App\Models\State;
use App\Models\StudentCourse;
use App\Models\PlacementCompany;
use App\Http\DataTables\DataTablesServerSide;

class PlacementController extends Controller
{

    protected string $permissionPrefix = 'placements';

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
    
    /* ============================================================
       INDEX
    ============================================================ */
    public function indexOld()
    {
        $placements = Placement::latest()->get();
        return view('placements.index', compact('placements'));
    }

    public function index(Request $request)
{
    if ($request->ajax()) {

        $query = Placement::with([
            'college',
            'state',
            'course',
            'session',
            'images',
            'videos'
        ]);

        // 🔍 FILTERS
        // dd($request->session_id);
        if ($request->college_id) {
            $query->where('college_name', $request->college_id);
        }

        if ($request->state_id) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->tech) {
            $query->where('tech', $request->tech);
        }

        if (!empty($request->session_id)) {
            $query->where('session_id', (int) $request->session_id);
        }

        // if ($request->session_id) {
        //     $query->where('session_id', $request->session_id);
        // }

        if ($request->location) {
            $query->where('location', 'LIKE', "%{$request->location}%");
        }

        if ($request->student_name) {
            $query->where('student_name', 'LIKE', "%{$request->student_name}%");
        }

        if ($request->company) {
            $query->where('company', $request->company);
        }

        // 📅 DATE FILTER (optional if you add later)
        if ($request->date_from) {
            $query->whereDate('placement_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('placement_date', '<=', $request->date_to);
        }

        // ⚠️ MISSING DATA FILTER
        if ($request->missing == 'yes') {
            $query->where(function ($q) {
                $q->whereNull('college_name')->orWhere('college_name','')
                  ->orWhereNull('phone_no')->orWhere('phone_no','')
                  ->orWhereNull('company')->orWhere('company','')
                  ->orWhereNull('tech')->orWhere('tech','')
                  ->orWhereNull('placement_date')
                  ->orWhereNull('state_id')
                  ->orWhereNull('location')->orWhere('location','')
                  ->orWhereNull('session_id');
            });
        }

        // 📷 MEDIA FILTER
        if ($request->media == 'with') {
            $query->where(function ($q) {
                $q->has('images')->orHas('videos');
            });
        }

        if ($request->media == 'without') {
            $query->doesntHave('images')->doesntHave('videos');
        }

        $query->latest();

        return DataTablesServerSide::response(
            $request,
            $query,
            [
                'orderable'  => ['id','student_name','location'],
                'searchable' => ['student_name','location'],
            ],
            function ($row, $index, $start) {

                // 🖼️ GET ALL IMAGES
                $images = $row->images->pluck('path')->toArray();

                // first image priority → cover → first uploaded
                $firstImage = $row->cover_image ?? ($images[0] ?? null);

                // IMAGE HTML (for popup)
                $mediaHtml = $firstImage
                    ? '<img src="'.asset($firstImage).'"
                            class="placement-img"
                            data-images=\''.json_encode($images).'\'
                            style="width:50px;height:50px;object-fit:cover;border-radius:6px;cursor:pointer;">'
                    : '<span class="text-muted">No Image</span>';

                return [
                    'id' => $start + $index + 1,

                    'student' => e($row->student_name),

                    'college' => optional($row->college)->full_name ?? '-',

                    'state' => optional($row->state)->name ?? '-',

                    'location' => $row->location ?? '-',

                    'tech' => optional($row->course)->course_name ?? '-',

                    'session' => optional($row->session)->session_name ?? '-',

                    'media' => $mediaHtml,

                    'actions' => '
                        <a href="'.route('placements.show', $row->id).'" class="btn btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="'.route('placements.edit', $row->id).'" class="btn btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="'.route('placements.destroy', $row->id).'" method="POST" style="display:inline-block;">
                            '.csrf_field().method_field('DELETE').'
                            <button type="submit" class="btn btn-sm" onclick="return confirm(\'Delete this?\')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    ',
                ];
            }
        );
    }

    // NORMAL LOAD (NON-AJAX)
    $sessions  = StudentSession::orderBy('session_name')->get();
    $colleges  = College::orderBy('college_name')->get();
    $states    = State::orderBy('name')->get();
    $courses   = StudentCourse::orderBy('course_name')->get();
    $companies = PlacementCompany::orderBy('name')->get();

    return view('placements.index', compact(
        'sessions',
        'colleges',
        'states',
        'courses',
        'companies'
    ));
}
    public function index1(Request $request)
{
    $placements = Placement::query()
        ->when($request->college_id, fn ($q) =>
            $q->where('college_name', $request->college_id)
        )
        ->when($request->state_id, fn ($q) =>
            $q->where('state_id', $request->state_id)
        )
        ->when($request->location, fn ($q) =>
            $q->where('location', 'LIKE', '%' . $request->location . '%')
        )
        ->when($request->tech, fn ($q) =>
            $q->where('tech', 'LIKE', '%' . $request->tech . '%')
        )
        ->when($request->session_id, fn ($q) =>
            $q->where('session_id', $request->session_id)
        )
        ->latest()
        ->get();

    $sessions = StudentSession::orderBy('session_name')->get();
    $colleges = College::with(['state','district'])->orderBy('college_name')->get();
    $states   = State::orderBy('name')->get();
    $courses   = StudentCourse::orderBy('course_name')->get();

    return view('placements.index', compact(
        'placements',
        'sessions',
        'colleges',
        'courses',
        'states'
    ));
}


    /* ============================================================
       CREATE
    ============================================================ */
    public function create()
    {
        $sessions = StudentSession::orderBy('session_name')->get();
        $colleges = College::with(['state','district'])->orderBy('college_name')->get();
        $states   = State::orderBy('name')->get();
        $courses  = StudentCourse::orderBy('course_name')->get();
        $companies = PlacementCompany::orderBy('name')->get();

        return view('placements.create', compact(
            'sessions',
            'colleges',
            'states',
            'companies',
            'courses'
        ));
    }


    /* ============================================================
       STORE
    ============================================================ */

    public function store(Request $request)
{
    $request->validate([
        'student_name'   => 'required|string|max:255',
        'tech'           => 'required|string|max:255',
        'placement_date' => 'required|date',
        'college_name'   => 'nullable|max:255',
        'phone_no'       => 'required|string|max:20',
        // 'address'        => 'nullable|string',
        'company'        => 'required|string|max:255',
        'state_id'        => 'required|max:255',
        'location'        => 'required|max:255',
        'session_id'        => 'required|max:255',

        'description'    => 'nullable|string',
        'youtube_link'    => 'nullable|string',
        'gdrive'    => 'nullable|string',
        'versal'    => 'nullable|string',
        'media'          => 'required',
        'media.*'        => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi',
        'cover_image'    => 'required'
    ]);

    /* ---------------------------------------------------------
       CREATE PLACEMENT
    --------------------------------------------------------- */
    $placement = Placement::create([
        'student_name'   => $request->student_name,
        'tech'           => $request->tech,
        'placement_date' => $request->placement_date,
        'college_name'   => $request->college_name,
        'phone_no'       => $request->phone_no,
        // 'address'        => $request->address,
        'company'        => $request->company,
        'description'    => $request->description,
        'state_id'    => $request->state_id,
        'location'    => $request->location,
        'session_id'    => $request->session_id,
        'youtube_link'    => $request->youtube_link,
        'gdrive'    => $request->gdrive,
        'versal'    => $request->versal,
    ]);

    /* ---------------------------------------------------------
       Upload Media
    --------------------------------------------------------- */
    foreach ($request->file('media') as $file) {

        $filename = time() . '_' . $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {

            $file->move(public_path('placement/images'), $filename);

            $img = PlacementImage::create([
                'placement_id' => $placement->id,
                'path'         => 'placement/images/' . $filename
            ]);

            if ($request->cover_image === $file->getClientOriginalName()) {
                $placement->update([
                    'cover_image' => $img->path
                ]);
            }

        } else {

            $file->move(public_path('placement/videos'), $filename);

            PlacementVideo::create([
                'placement_id' => $placement->id,
                'path'         => 'placement/videos/' . $filename
            ]);
        }
    }

    return redirect()->route('placements.index')
                     ->with('success', 'Placement created successfully!');
}

     

    /* ============================================================
       EDIT
    ============================================================ */
    public function edit(Placement $placement)
    {   
        $sessions = StudentSession::orderBy('session_name')->get();
        $colleges = College::with(['state','district'])->orderBy('college_name')->get();
        $states   = State::orderBy('name')->get();
        $courses  = StudentCourse::orderBy('course_name')->get();
        $companies = PlacementCompany::orderBy('name')->get();

        $placement->load('images', 'videos');
        return view('placements.edit', compact(
            'placement',
            'sessions',
            'colleges',
            'states',
            'courses',
            'companies'
        ));
    }

    /* ============================================================
       UPDATE
    ============================================================ */
    public function update(Request $request, Placement $placement)
{
    $request->validate([
        'student_name'   => 'required|string|max:255',
        'tech'           => 'required|string|max:255',
        'placement_date' => 'required|date',
        'college_name'   => 'nullable|max:255',
        'phone_no'       => 'required|string|max:20',
        // 'address'        => 'nullable|string',
        'company'        => 'required|string|max:255',
        'state_id'        => 'required|max:255',
        'location'        => 'required|max:255',
        'session_id'        => 'required|max:255',

        'description'    => 'nullable|string',
        'youtube_link'    => 'nullable|string',
        'gdrive'    => 'nullable|string',
        'versal'    => 'nullable|string',
    ]);

    /* ---------------------------------------------------------
       UPDATE PLACEMENT DATA
    --------------------------------------------------------- */
    $placement->update([
        'student_name'   => $request->student_name,
        'tech'           => $request->tech,
        'placement_date' => $request->placement_date,
        'college_name'   => $request->college_name,
        'phone_no'       => $request->phone_no,
        // 'address'        => $request->address,
        'company'        => $request->company,
        'state_id'    => $request->state_id,
        'location'    => $request->location,
        'session_id'    => $request->session_id,
        'description'    => $request->description,
        'youtube_link'    => $request->youtube_link,
        'gdrive'    => $request->gdrive,
        'versal'    => $request->versal,
    ]);

    /* ---------------------------------------------------------
       Upload New Media (Optional)
    --------------------------------------------------------- */
    if ($request->hasFile('media')) {

        foreach ($request->file('media') as $file) {

            $filename = time() . '_' . $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());

            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {

                $file->move(public_path('placement/images'), $filename);

                PlacementImage::create([
                    'placement_id' => $placement->id,
                    'path'         => 'placement/images/' . $filename
                ]);

            } else {

                $file->move(public_path('placement/videos'), $filename);

                PlacementVideo::create([
                    'placement_id' => $placement->id,
                    'path'         => 'placement/videos/' . $filename
                ]);
            }
        }
    }

    return redirect()->route('placements.index')
                     ->with('success', 'Placement updated successfully!');
}


    public function updateOld(Request $request, Placement $placement)
    {
        $request->validate([
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'media.*'     => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,avi',
        ]);

        $placement->update([
            'name'        => $request->name,
            'description' => $request->description
        ]);

        /* Upload New Media */
        if ($request->hasFile('media')) {

            foreach ($request->file('media') as $file) {
                $filename = time() . "_" . $file->getClientOriginalName();
                $ext = strtolower($file->getClientOriginalExtension());

                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {

                    $file->move(public_path('placement/images'), $filename);

                    PlacementImage::create([
                        'placement_id' => $placement->id,
                        'path'         => 'placement/images/' . $filename
                    ]);

                } else {

                    $file->move(public_path('placement/videos'), $filename);

                    PlacementVideo::create([
                        'placement_id' => $placement->id,
                        'path'         => 'placement/videos/' . $filename
                    ]);
                }
            }
        }

        return redirect()->route('placements.index')
                         ->with('success', 'Placement updated successfully!');
    }


    public function show(Placement $placement)
    {
        $placement->load([
            'images',
            'videos',
            'college.state',
            'college.district',
            'session',
            'companyRelation',
        ]);

        return view('placements.show', compact('placement'));
    }



    /* ============================================================
       DELETE PLACEMENT
    ============================================================ */
    public function destroy(Placement $placement)
    {
        $placement->delete();
        return redirect()->route('placements.index')
                         ->with('success', 'Placement deleted!');
    }

    /* ============================================================
       AJAX DELETE IMAGE
    ============================================================ */
    public function deleteImage($id)
    {
        $image = PlacementImage::findOrFail($id);

        if (file_exists(public_path($image->path))) {
            unlink(public_path($image->path));
        }

        $image->delete();
        return response()->json(['success' => true]);
    }

    /* ============================================================
       AJAX DELETE VIDEO
    ============================================================ */
    public function deleteVideo($id)
    {
        $video = PlacementVideo::findOrFail($id);

        if (file_exists(public_path($video->path))) {
            unlink(public_path($video->path));
        }

        $video->delete();
        return response()->json(['success' => true]);
    }

    /* ============================================================
       AJAX SET COVER IMAGE
    ============================================================ */
    public function setCover($id)
    {
        $image = PlacementImage::findOrFail($id);

        $image->placement->update([
            'cover_image' => $image->path
        ]);

        return response()->json(['success' => true]);
    }
}
