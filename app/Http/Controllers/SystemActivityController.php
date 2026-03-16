<?php

namespace App\Http\Controllers;

use App\Models\BlockedIp;
use App\Models\SystemActivityLog;
use App\Models\User;
use App\Http\DataTables\DataTablesServerSide;
use Illuminate\Http\Request;

class SystemActivityController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('name')->get(['id', 'name', 'username']);
        return view('system_activity.index', compact('users'));
    }

    public function data(Request $request)
    {
        $query = SystemActivityLog::with(['user', 'trainer']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('guard')) {
            $query->where('guard', $request->guard);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $blockedIpList = BlockedIp::pluck('ip_address')->toArray();

        return DataTablesServerSide::response($request, $query, [
            'orderable'  => [
                1 => 'created_at',
                3 => 'guard',
                4 => 'action',
                5 => 'ip_address',
                6 => 'url',
            ],
            'searchable' => function ($q, $search) {
                $q->where(function ($q2) use ($search) {
                    $q2->orWhere('ip_address', 'like', '%' . $search . '%')
                        ->orWhere('url', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($sq) use ($search) {
                            $sq->where('name', 'like', '%' . $search . '%')
                                ->orWhere('username', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('trainer', function ($sq) use ($search) {
                            $sq->where('name', 'like', '%' . $search . '%')
                                ->orWhere('username', 'like', '%' . $search . '%');
                        });
                });
            },
        ], function ($log, $index, $start) use ($blockedIpList) {
            $guard = $log->guard ?? 'guest';
            $actionLabel = \Illuminate\Support\Str::headline(str_replace('_', ' ', $log->action));
            $urlShort = \Illuminate\Support\Str::limit($log->url ?? '—', 60);
            $actions = '—';
            if ($log->ip_address) {
                if (in_array($log->ip_address, $blockedIpList)) {
                    $actions = '<span class="badge bg-danger">Blocked</span> <a href="' . route('admin.blocked-ips.index') . '" class="btn btn-sm btn-link p-0 ms-1">Manage</a>';
                } else {
                    $actions = '<a href="' . route('admin.blocked-ips.create', ['ip' => $log->ip_address, 'actor' => $log->actor_name]) . '" class="btn btn-sm btn-outline-danger">Block IP</a>';
                }
            }
            return [
                $start + $index + 1,
                $log->created_at->format('d-m-Y H:i:s'),
                e($log->actor_name),
                '<span class="badge bg-secondary">' . e($guard) . '</span>',
                '<span class="badge bg-info">' . e($actionLabel) . '</span>',
                '<code>' . e($log->ip_address ?? '—') . '</code>',
                '<span class="text-break small" style="max-width:280px;" title="' . e($log->url ?? '') . '">' . e($urlShort) . '</span>',
                $actions,
            ];
        });
    }
}
