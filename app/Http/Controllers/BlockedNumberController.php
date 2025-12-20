<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BlockedNumber;
use App\Services\BlockNumberService;
use Illuminate\Http\Request;

class BlockedNumberController extends Controller
{
    public function index()
    {
        $blockedNumbers = BlockedNumber::with('logs')
            ->latest('blocked_at')
            ->paginate(20);

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

    public function destroy(BlockedNumber $blockedNumber, BlockNumberService $service)
    {
        $service->unblock($blockedNumber);

        return redirect()
            ->route('admin.blocked-numbers.index')
            ->with('success', 'Number unblocked and records restored.');
    }
}
