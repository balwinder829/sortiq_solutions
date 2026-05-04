<?php
namespace App\Http\Controllers;

use App\Models\College;
use App\Models\State;
use App\Models\Hod;
use Illuminate\Http\Request;
use App\Rules\NotBlockedNumber;
class HodController extends Controller
{	

    protected string $permissionPrefix = 'hods';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',
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
	 
     public function index(Request $request)
{
    $states = State::all();

    $hods = Hod::with(['college.state', 'college.district'])
        ->when($request->state, function ($q) use ($request) {
            $q->whereHas('college.state', function ($s) use ($request) {
                $s->where('name', $request->state);
            });
        })
        // ->orderBy('hod_name')
        ->latest('updated_at')
        ->get();

    return view('hods.index', compact('hods', 'states'));
}
    // public function index(Request $request)
    // {
    //     $states = State::all();

    //     $colleges = College::with(['state', 'district', 'hod'])
    //         ->when($request->state, function ($q) use ($request) {
    //             $q->whereHas('state', function ($s) use ($request) {
    //                 $s->where('name', $request->state);
    //             });
    //         })
    //         ->when($request->hod_status === 'yes', function ($q) {
    //             $q->whereHas('hod');
    //         })
    //         ->when($request->hod_status === 'no', function ($q) {
    //             $q->whereDoesntHave('hod');
    //         })
    //         ->orderBy('college_name')
    //         ->get();

    //     return view('hods.index', compact('colleges', 'states'));
    // }


     

    public function create(Request $request)
    {
        $colleges = College::whereDoesntHave('hod')->get();
         $selectedCollegeId = $request->college_id;
        return view('hods.create', compact('colleges', 'selectedCollegeId'));
    }

    public function store(Request $request)
{
    // 1️⃣ Clean empty email strings
    $request->merge([
        'hod_emails' => array_filter($request->hod_emails ?? []),
        'tpo_emails' => array_filter($request->tpo_emails ?? []),
    ]);

    // 2️⃣ Remove arrays if name empty
    if (!$request->hod_name) $request->request->remove('hod_emails');
    if (!$request->tpo_name) $request->request->remove('tpo_emails');

    // 3️⃣ Validate
    $request->validate([
        'college_id' => 'required|unique:hods,college_id',

        // HOD
        'hod_name'    => 'required_without:tpo_name|nullable',
        'hod_gender'  => 'required_with:hod_name|nullable',
        'hod_contact' =>  ['required_with:hod_name', 'nullable', 'digits:10', new NotBlockedNumber],
        'hod_emails'  => 'required_with:hod_name|array|min:1',
        'hod_emails.*'=> 'email',
        'hod_primary' => 'nullable|integer',

        // TPO
        'tpo_name'    => 'required_without:hod_name|nullable',
        'tpo_gender'  => 'required_with:tpo_name|nullable',
        // 'tpo_contact' => 'required_with:tpo_name|nullable|digits:10',
        'tpo_contact' => ['required_with:tpo_name', 'nullable', 'digits:10', new NotBlockedNumber],
        'tpo_emails'  => 'required_with:tpo_name|array|min:1',
        'tpo_emails.*'=> 'email',
        'tpo_primary' => 'nullable|integer',
        'description' => 'nullable',
    ]);

    // 4️⃣ Save main record
    $hod = Hod::create($request->only([
        'college_id',
        'hod_name','hod_gender','hod_contact',
        'tpo_name','tpo_gender','tpo_contact',
        'description',
    ]));

    // 5️⃣ Save HOD emails
    foreach ($request->hod_emails ?? [] as $i => $email) {
        $hod->emails()->create([
            'type'       => 'hod',
            'email'      => $email,
            'is_primary' => count($request->hod_emails) == 1
                            ? true
                            : ($request->hod_primary == $i)
        ]);
    }

    // 6️⃣ Save TPO emails
    foreach ($request->tpo_emails ?? [] as $i => $email) {
        $hod->emails()->create([
            'type'       => 'tpo',
            'email'      => $email,
            'is_primary' => count($request->tpo_emails) == 1
                            ? true
                            : ($request->tpo_primary == $i)
        ]);
    }

    return redirect()->route('hods.index')->with('success','Saved successfully');
}



     

    public function edit(Hod $hod)
    {
     
        $colleges = College::all();
        return view('hods.edit', compact('hod', 'colleges'));

    }

    public function update(Request $request, Hod $hod)
    {
        // 1️⃣ Clean empty email strings
        $request->merge([
            'hod_emails' => array_filter($request->hod_emails ?? []),
            'tpo_emails' => array_filter($request->tpo_emails ?? []),
        ]);

        // 2️⃣ Remove arrays if name empty
        if (!$request->hod_name) $request->request->remove('hod_emails');
        if (!$request->tpo_name) $request->request->remove('tpo_emails');

        // 3️⃣ Validate
        $request->validate([
            'college_id' => 'required|unique:hods,college_id,' . $hod->id,

            'hod_name'    => 'required_without:tpo_name|nullable',
            'hod_gender'  => 'required_with:hod_name|nullable',
            // 'hod_contact' => 'required_with:hod_name|nullable|digits:10',
            'hod_contact' => ['required_with:hod_name', 'nullable', 'digits:10', new NotBlockedNumber],
            'hod_emails'  => 'required_with:hod_name|array|min:1',
            'hod_emails.*'=> 'email',
            'hod_primary' => 'nullable|integer',

            'tpo_name'    => 'required_without:hod_name|nullable',
            'tpo_gender'  => 'required_with:tpo_name|nullable',
            // 'tpo_contact' => 'required_with:tpo_name|nullable|digits:10',
            'tpo_contact' => ['required_with:tpo_name', 'nullable', 'digits:10', new NotBlockedNumber],
            'tpo_emails'  => 'required_with:tpo_name|array|min:1',
            'tpo_emails.*'=> 'email',
            'tpo_primary' => 'nullable|integer',
            'description' => 'nullable',
        ]);

        // 4️⃣ Update main
        $hod->update($request->only([
            'college_id',
            'hod_name','hod_gender','hod_contact',
            'tpo_name','tpo_gender','tpo_contact',
            'description',
        ]));

        // 5️⃣ Replace emails
        $hod->emails()->delete();
        // dd($request->hod_emails);
        foreach ($request->hod_emails ?? [] as $i => $email) {
            $hod->emails()->create([
                'type'=>'hod',
                'email'=>$email,
                'is_primary'=> count($request->hod_emails)==1
                                ? true
                                : ($request->hod_primary==$i)
            ]);
        }

        foreach ($request->tpo_emails ?? [] as $i => $email) {
            $hod->emails()->create([
                'type'=>'tpo',
                'email'=>$email,
                'is_primary'=> count($request->tpo_emails)==1
                                ? true
                                : ($request->tpo_primary==$i)
            ]);
        }

        return redirect()->route('hods.index')->with('success','Updated successfully');
    }



    

    public function destroy(Hod $hod)
    {
        $hod->delete();
        return back()->with('success', 'Record deleted');
    }
}

