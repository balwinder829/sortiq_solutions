@extends('layouts.app')

@section('content')
<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">👥 Sales Team Overview</h3>
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
            ← Back
        </a>
    </div>

    {{-- ================= FILTERS ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <strong>🔍 Filters</strong>
        </div>

        <div class="card-body">
            <form method="GET">
                <div class="row g-3 align-items-end">

                    {{-- Quick Date --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Period</label>
                        <select name="period" class="form-control">
                            <option value="">Overall</option>
                            <option value="today" {{ request('period')=='today'?'selected':'' }}>Today</option>
                            <option value="month" {{ request('period')=='month'?'selected':'' }}>This Month</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <!-- <div class="col-md-3">
                        <label class="form-label fw-semibold">Sort By</label>
                        <select name="sort" class="form-control">
                            <option value="">Default</option>
                            <option value="leads" {{ request('sort')=='leads'?'selected':'' }}>Total Leads</option>
                            <option value="followups" {{ request('sort')=='followups'?'selected':'' }}>Follow-ups</option>
                            <option value="registrations" {{ request('sort')=='registrations'?'selected':'' }}>Registrations</option>
                        </select>
                    </div> -->

                    {{-- Buttons --}}
                    <div class="col-md-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button class="btn btn-primary w-100">
                            <i class="fa fa-search me-1"></i> Apply
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <small class="text-muted">Total Salespersons</small>
                    <h4 class="fw-bold">{{ $salespersons->count() }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <small class="text-muted">Total Leads</small>
                    <h4 class="fw-bold">
                        {{ $salespersons->sum('total_leads') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <small class="text-muted">Total Follow-ups</small>
                    <h4 class="fw-bold">
                        {{ $salespersons->sum('total_followups') }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <small class="text-muted">Total Registrations</small>
                    <h4 class="fw-bold">
                        {{ $salespersons->sum('registered_leads') ?? 0 }}
                    </h4>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>📊 Performance Table</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>

                        <th class="text-center">Leads</th>
                        <th class="text-center">Follow-ups</th>
                        <th class="text-center">Registered</th>
                        <th class="text-center">Conversion %</th>
                        <th class="text-center">Last Activity</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($salespersons as $sp)
                    @php
                        $conversion = $sp->total_leads
                            ? round(($sp->registered_leads / $sp->total_leads) * 100)
                            : 0;
                    @endphp

                    <tr>
                        <td class="fw-semibold">{{ $sp->name }}</td>
                        <td>{{ $sp->phone ?? '-' }}</td>

                        <td class="text-center">
                            <span class="badge bg-info">{{ $sp->total_leads }}</span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-warning text-dark">
                                {{ $sp->total_followups }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-success">
                                {{ $sp->registered_leads ?? 0 }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-primary">
                                {{ $conversion }}%
                            </span>
                        </td>

                        <td class="text-center">
                            @if($sp->last_activity_at)
                                <small class="text-muted">
                                    {{ $sp->last_activity_at->diffForHumans() }}
                                </small>
                            @else
                                <span class="text-muted">No activity</span>
                            @endif
                        </td>

                        <td class="text-center">
                            <a href="{{ route('salespersons.show', $sp->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted p-4">
                            No salespersons found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
