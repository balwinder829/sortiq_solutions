@extends('layouts.app')

@section('content')
<div class="container">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Your Assigned Enquiries</h3>
    </div>

    {{-- ================= FILTERS ================= --}}
    <form method="GET" action="{{ route('sales.enquiries.index') }}" class="mb-4">

        <div class="row">

            {{-- QUICK DATE --}}
            <div class="col-md-3 mb-2">
                <label class="fw-semibold">Quick Date</label>
                <select name="quick_date" class="form-control">
                    <option value="">All</option>
                    <option value="today" {{ request('quick_date')=='today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('quick_date')=='yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last7" {{ request('quick_date')=='last7' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="this_month" {{ request('quick_date')=='this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('quick_date')=='last_month' ? 'selected' : '' }}>Last Month</option>
                </select>
            </div>

            {{-- LEAD STATUS --}}
            <div class="col-md-3 mb-2">
                <label class="fw-semibold">Lead Status</label>
                <select name="lead_status" class="form-control">
                    <option value="">All</option>
                    <option value="new" {{ request('lead_status')=='new' ? 'selected' : '' }}>New</option>
                    <option value="followup" {{ request('lead_status')=='followup' ? 'selected' : '' }}>Follow-up</option>
                    <option value="registered" {{ request('lead_status')=='registered' ? 'selected' : '' }}>Registered</option>
                    <option value="closed" {{ request('lead_status')=='closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            {{-- FOLLOW-UP STATUS --}}
            <div class="col-md-3 mb-2">
                <label class="fw-semibold">Follow-up Status</label>
                <select name="followup_filter" class="form-control">
                    <option value="">All</option>
                    <option value="today" {{ request('followup_filter')=='today' ? 'selected' : '' }}>
                        Due Today
                    </option>
                    <option value="overdue" {{ request('followup_filter')=='overdue' ? 'selected' : '' }}>
                        Overdue
                    </option>
                    <option value="upcoming" {{ request('followup_filter')=='upcoming' ? 'selected' : '' }}>
                        Upcoming
                    </option>
                    <option value="none" {{ request('followup_filter')=='none' ? 'selected' : '' }}>
                        Not Set
                    </option>
                </select>
            </div>

            {{-- SEARCH --}}
            <div class="col-md-3 mb-2">
                <label class="fw-semibold">Search</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="form-control"
                       placeholder="Name or mobile">
            </div>

        </div>

        <div class="mt-3 text-end">
            <button class="btn btn-primary">
                <i class="fa fa-search"></i> Search
            </button>

            <a href="{{ route('sales.enquiries.index') }}"
               class="btn btn-secondary">
                Reset
            </a>
        </div>

    </form>

    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="table-responsive">

            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Lead Status</th>
                        <th>Next Follow-up</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($enquiries as $e)
                    <tr>
                        {{-- NAME --}}
                        <td class="fw-semibold">
                            {{ $e->name }}
                        </td>

                        {{-- CONTACT --}}
                        <td>
                            {{ $e->mobile }}

                            <div class="mt-2 d-flex gap-2">
                                <a href="tel:{{ $e->mobile }}"
                                   class="btn btn-success btn-sm">
                                    📞 Call
                                </a>

                                <a href="https://wa.me/{{ $e->mobile }}"
                                   target="_blank"
                                   class="btn btn-info btn-sm text-white">
                                    💬 WhatsApp
                                </a>
                            </div>
                        </td>

                        {{-- STATUS --}}
                        <td>
                            <span class="badge bg-info">
                                {{ ucfirst($e->lead_status) }}
                            </span>
                        </td>

                        {{-- FOLLOW-UP --}}
                        <td>
                            @if($e->next_followup_at)
                                @if($e->next_followup_at->isToday())
                                    <span class="badge bg-warning text-dark">Today</span>
                                @elseif($e->next_followup_at->isPast())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-success">
                                        {{ $e->next_followup_at->format('d M Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Not Set</span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="text-center">
                            <a href="{{ route('sales.enquiries.show', $e->id) }}"
                               class="btn btn-primary btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No enquiries found.
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-3 d-flex justify-content-center">
        {{ $enquiries->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection
