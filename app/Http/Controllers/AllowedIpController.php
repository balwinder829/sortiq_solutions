<?php

namespace App\Http\Controllers;

use App\Models\AllowedIp;
use Illuminate\Http\Request;

class AllowedIpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $allowedIps = AllowedIp::latest('created_at')->paginate(20);

        return view('allowed-ips.index', compact('allowedIps'));
    }

    public function create(Request $request)
    {
        $prefillIp = $request->get('ip');

        return view('allowed-ips.create', compact('prefillIp'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => [
                'required',
                'string',
                'max:45',
                'unique:allowed_ips,ip_address',
                function ($attribute, $value, $fail) {
                    $value = trim($value);
                    if (str_contains($value, '/')) {
                        if (!preg_match('/^[0-9a-f.:]+\/\d{1,3}$/i', $value)) {
                            $fail('Invalid CIDR format (e.g. 192.168.1.0/24).');
                        }
                    } elseif (!filter_var($value, FILTER_VALIDATE_IP)) {
                        $fail('The IP address is invalid.');
                    }
                },
            ],
            'label' => 'nullable|string|max:255',
        ]);

        AllowedIp::create([
            'ip_address' => trim($request->ip_address),
            'label'      => $request->filled('label') ? trim($request->label) : null,
            'added_by'   => auth()->user()?->name ?? auth()->user()?->email ?? null,
        ]);

        return redirect()
            ->route('admin.allowed-ips.index')
            ->with('success', 'IP address added to whitelist. The script can now be used from this IP.');
    }

    public function destroy(AllowedIp $allowed_ip)
    {
        $allowed_ip->delete();

        return redirect()
            ->route('admin.allowed-ips.index')
            ->with('success', 'IP removed from whitelist. Access from this IP will be denied when IP whitelist is enabled.');
    }
}