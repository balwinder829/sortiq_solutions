@extends('layouts.app')

@section('content')
@php
    $assignedLink = auth()->user()->role == 3
        ? route('leads.index', ['assigned_to' => auth()->id()])
        : route('leads.index'); // admin -> show all leads
@endphp
<div class="container">

    <h2 class="mb-4">Sales Dashboard</h2>

    {{-- Stats Row --}}
    <div class="row">

        {{-- Total Leads Assigned --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ $assignedLink }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['total'] }}</h4>
                        <p class="text-muted m-0">Total Leads Assigned</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- New Leads --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['status' => 'new']) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['new'] }}</h4>
                        <p class="text-muted m-0">New Leads</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Contacted --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['status' => 'contacted']) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['contacted'] }}</h4>
                        <p class="text-muted m-0">Contacted</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Follow Ups --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['status' => 'follow_up']) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['follow_up'] }}</h4>
                        <p class="text-muted m-0">Follow Ups</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Not Interested --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['status' => 'not_interested']) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['not_interested'] }}</h4>
                        <p class="text-muted m-0">Not Interested</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Onboarded --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['status' => 'onboarded']) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['onboarded'] }}</h4>
                        <p class="text-muted m-0">Onboarded</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Calls Today --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['calls' => 'today']) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['calls_today'] }}</h4>
                        <p class="text-muted m-0">Calls Today</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Today's Follow-ups --}}
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', ['follow_up_date' => now()->format('Y-m-d')]) }}" class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['today_followups'] }}</h4>
                        <p class="text-muted m-0">Today's Follow-ups</p>
                    </div>
                </a>
            </div>
        </div>

    </div>

    {{-- Recent Leads --}}
    <h4 class="mt-4">Recent Leads</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Follow-up Date</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
                @foreach($recentLeads as $lead)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</td>
                    <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('d-m-Y') : '-' }}</td>
                    <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>
@endsection
