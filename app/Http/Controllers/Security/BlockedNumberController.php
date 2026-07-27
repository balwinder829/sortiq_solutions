<?php

namespace App\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use App\Models\BlockedNumber;
use App\Services\BlockNumberService;
use Illuminate\Http\Request;

class BlockedNumberController extends Controller
{
    protected string $permissionPrefix = 'blocked_numbers';

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
        $blockedNumbers = BlockedNumber::with('logs')
            ->latest('blocked_at')
            ->get();

        return view('blocked-numbers.index', compact('blockedNumbers'));
    }

    public function create()
    {
        return view('blocked-numbers.create');
    }

    public function store(Request $request, BlockNumberService $service)
    {
        $request->validate([
            'number' => 'required|string|unique:blocked_numbers,number',
        ]);

        $service->block($request->number);

        return redirect()
            ->route('admin.blocked-numbers.index')
            ->with('success', 'Number blocked successfully.');
    }

    public function show(BlockedNumber $blockedNumber)
    {
        return view(
            'blocked-numbers.show',
            ['blocked' => $blockedNumber->load('logs')]
        );
    }

    public function edit(BlockedNumber $blockedNumber)
    {
        return view('blocked-numbers.edit', [
            'blocked' => $blockedNumber
        ]);
    }

    public function update(Request $request, BlockedNumber $blockedNumber)
    {
        $request->validate([
            'number' => 'required|string|unique:blocked_numbers,number,' . $blockedNumber->id,
        ]);

        $blockedNumber->update([
            'number' => $request->number,
        ]);

        return redirect()
            ->route('admin.blocked-numbers.index')
            ->with('success', 'Blocked number updated successfully.');
    }

    public function destroy(BlockedNumber $blockedNumber, BlockNumberService $service)
    {
        $service->unblock($blockedNumber);

        return redirect()
            ->route('admin.blocked-numbers.index')
            ->with('success', 'Number unblocked and records restored.');
    }
}
