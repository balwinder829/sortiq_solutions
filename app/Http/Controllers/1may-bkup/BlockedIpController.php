<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class BlockedIpController extends Controller
{
    public function __construct()
{
     $this->middleware('auth');
    $this->middleware('permission:blocked_ip.view')->only(['index']);
    $this->middleware('permission:blocked_ip.create')->only(['create','store']);
    $this->middleware('permission:blocked_ip.edit')->only(['edit','update']);
    $this->middleware('permission:blocked_ip.delete')->only('destroy');
}

    public function index()
    {
        $blockedIps = BlockedIp::latest('blocked_at')->paginate(20);
        return view('blocked-ips.index', compact('blockedIps'));
    }

    public function create(Request $request)
    {
        $prefillIp = $request->get('ip');
        $actorName = $request->get('actor');
        return view('blocked-ips.create', compact('prefillIp', 'actorName'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|string|max:45|unique:blocked_ips,ip_address',
            'reason'     => 'nullable|string|max:255',
            'actor_name' => 'nullable|string|max:255',
        ]);

        BlockedIp::create([
            'ip_address' => trim($request->ip_address),
            'reason'     => $request->reason,
            'actor_name' => $request->actor_name ? trim($request->actor_name) : null,
            'blocked_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('admin.blocked-ips.index')
            ->with('success', 'IP address blocked successfully.');
    }

    public function destroy(BlockedIp $blocked_ip)
    {
        $blocked_ip->delete();
        return redirect()
            ->route('admin.blocked-ips.index')
            ->with('success', 'IP address unblocked.');
    }
}
