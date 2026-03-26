@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-3">
        Call Logs - {{ $college->full_name }}
    </h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Name</th>
                    <th>Number</th>
                    <th>Status</th>
                    <th>Called At</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($logs as $log)

                    <tr>
                        <td>{{ $log->id }}</td>

                        <td>
                            <span class="badge bg-primary">
                                {{ strtoupper($log->type) }}
                            </span>
                        </td>

                        <td>{{ $log->recipient_name }}</td>

                        <td>{{ $log->contact_number }}</td>

                        <td>
                            @php
                                $color = $log->status == 'connected' ? 'success' : 'danger';
                            @endphp

                            <span class="badge bg-{{ $color }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>

                        <td>
                            {{ optional($log->called_at)->format('d M Y h:i A') }}
                        </td>

                        <td>
                            <a href="{{ route('admin.college-calls.view',$log->id) }}"
                               class="btn btn-sm btn-info">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7" class="text-center">No Logs Found</td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>

</div>

@endsection