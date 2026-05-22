<?php

namespace App\Http\Controllers\Finance;
use App\Http\Controllers\Controller;

use App\Models\VisitingCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Rules\NotBlockedNumber;

class VisitingCardController extends Controller
{
    protected string $permissionPrefix = 'visiting_cards';

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

    public function index()
    {
        $cards = VisitingCard::latest()->get();
        return view('visiting-cards.index', compact('cards'));
    }

    public function create()
    {
        return view('visiting-cards.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            // 'phone_primary' => 'required|string|max:50',
            // 'phone_secondary' => 'nullable|string|max:50',
            'phone_primary' => ['required', 'string', new NotBlockedNumber],
            'phone_secondary' => ['nullable', 'string', new NotBlockedNumber],
            'email' => 'nullable|email',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'card_front' => 'nullable|image|max:2048',
            'card_back' => 'nullable|image|max:2048',
        ]);

       $uploadPath = public_path('visiting_cards');

        // CREATE FOLDER IF NOT EXISTS
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        /* ---------- FRONT IMAGE ---------- */
        if ($request->hasFile('card_front')) {

            // delete old image (for update)
            if (!empty($visiting_card->card_front ?? null) &&
                file_exists(public_path($visiting_card->card_front))) {
                unlink(public_path($visiting_card->card_front));
            }

            $file = $request->file('card_front');
            $filename = uniqid().'_front.'.$file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $data['card_front'] = 'visiting_cards/'.$filename;
        }

        /* ---------- BACK IMAGE ---------- */
        if ($request->hasFile('card_back')) {

            if (!empty($visiting_card->card_back ?? null) &&
                file_exists(public_path($visiting_card->card_back))) {
                unlink(public_path($visiting_card->card_back));
            }

            $file = $request->file('card_back');
            $filename = uniqid().'_back.'.$file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $data['card_back'] = 'visiting_cards/'.$filename;
        }

        VisitingCard::create($data);

        return redirect()->route('visiting-cards.index')->with('success', 'Visiting card added successfully');
    }

    public function show(VisitingCard $visiting_card)
    {
        return view('visiting-cards.show', compact('visiting_card'));
    }

    public function edit(VisitingCard $visiting_card)
    {
        return view('visiting-cards.edit', compact('visiting_card'));
    }

    public function update(Request $request, VisitingCard $visiting_card)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'phone_primary' => ['required', 'string', new NotBlockedNumber],
            'phone_secondary' => ['nullable', 'string', new NotBlockedNumber],
            // 'phone_primary' => 'required|string|max:50',
            // 'phone_secondary' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'card_front' => 'nullable|image|max:2048',
            'card_back' => 'nullable|image|max:2048',
        ]);

        $uploadPath = public_path('visiting_cards');

        // ensure folder exists
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        if ($request->hasFile('card_front')) {

            // delete old front image
            if ($visiting_card->card_front &&
                file_exists(public_path($visiting_card->card_front))) {
                unlink(public_path($visiting_card->card_front));
            }

            // save new image
            $file = $request->file('card_front');
            $filename = uniqid().'_front.'.$file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $data['card_front'] = 'visiting_cards/'.$filename;
        }

        if ($request->hasFile('card_back')) {

            // delete old back image
            if ($visiting_card->card_back &&
                file_exists(public_path($visiting_card->card_back))) {
                unlink(public_path($visiting_card->card_back));
            }

            // save new image
            $file = $request->file('card_back');
            $filename = uniqid().'_back.'.$file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);

            $data['card_back'] = 'visiting_cards/'.$filename;
        }


        $visiting_card->update($data);

        return redirect()->route('visiting-cards.index')->with('success', 'Visiting card updated successfully');
    }

    public function destroy(VisitingCard $visiting_card)
    {
        $visiting_card->delete();
        return back()->with('success', 'Visiting card deleted');
    }
}
