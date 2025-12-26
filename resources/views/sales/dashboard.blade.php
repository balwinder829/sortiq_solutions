@extends('layouts.app')

@section('content')

<style>
/* =========================
   TABLE ROW STATUS COLORS
========================= */
.row-call-done { background-color: #198754 !important; color: #ffffff !important; }
.row-follow-up { background-color: #0d6efd !important; color: #ffffff !important; }
.row-not-picked { background-color: #fd7e14 !important; color: #ffffff !important; }
.row-registered { background-color: #ffc107 !important; color: #000000 !important; }

/* FORCE COLOR ON TABLE CELLS */
.table tbody tr.row-call-done td { background-color: #198754 !important; color: #ffffff !important; }
.table tbody tr.row-follow-up td { background-color: #0d6efd !important; color: #ffffff !important; }
.table tbody tr.row-not-picked td { background-color: #fd7e14 !important; color: #ffffff !important; }
.table tbody tr.row-registered td { background-color: #ffc107 !important; color: #000000 !important; }

/* =========================
   DASHBOARD CARDS
========================= */
.dashboard-card {
    border-radius: 10px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

/* =========================
   BIG BADGES
========================= */
.badge-xl {
    font-size: 20px;
    padding: 10px 14px;
}
</style>

<div class="container">

    <h3 class="mb-4">Sales Dashboard</h3>

    {{-- ================= DASHBOARD COUNTS ================= --}}
    <div class="row mb-4">

        {{-- TODAY ASSIGNED --}}
       <div class="col-md-3">
            <a href="{{ route('sales.enquiries.index', ['quick_date' => 'today']) }}"
               class="text-decoration-none text-dark">

                <div class="card dashboard-card shadow-sm p-3 text-center">
                    <h6 class="fw-bold">Today's Assigned</h6>
                    <span class="badge bg-primary badge-xl">
                        {{ $todaysAssigned }}
                    </span>
                </div>

            </a>
        </div>


        {{-- TODAY FOLLOWUPS --}}
        <div class="col-md-3">
            <div class="card dashboard-card shadow-sm p-3 text-center">
                <h6 class="fw-bold">Today's Follow-ups</h6>
                <span class="badge bg-warning text-dark badge-xl">
                    {{ $todayFollowups->count() }}
                </span>
            </div>
        </div>

        {{-- TODAY REGISTERED --}}
        <div class="col-md-3">
            <div class="card dashboard-card shadow-sm p-3 text-center">
                <h6 class="fw-bold">Today Registered</h6>
                <span class="badge bg-success badge-xl">
                    {{ $todayRegistered }}
                </span>
            </div>
        </div>

        {{-- NOT PICKED --}}
        <div class="col-md-3">
            <div class="card dashboard-card shadow-sm p-3 text-center">
                <h6 class="fw-bold">Not Picked</h6>
                <span class="badge bg-danger badge-xl">
                    {{ $notPickedCount }}
                </span>
            </div>
        </div>

    </div>

    {{-- ================= STATUS STATS ================= --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card dashboard-card p-3 text-center">
                <h4>{{ $totalAssigned }}</h4>
                <p>Total Assigned Leads</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3 text-center">
                <h4>{{ $statusCount['followup'] ?? 0 }}</h4>
                <p>Active Follow-ups</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3 text-center">
                <h4>{{ $statusCount['closed'] ?? 0 }}</h4>
                <p>Closed Leads</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card p-3 text-center">
                <h4>{{ $statusCount['registered'] ?? 0 }}</h4>
                <p>Registered Leads</p>
            </div>
        </div>

    </div>

    {{-- ================= TODAY FOLLOWUPS ================= --}}
    <h4>Today's Follow-Ups</h4>

    <div class="card mb-4">
        <div class="card-body">

            @if($todayFollowups->count() == 0)
                <div class="alert alert-light text-center">
                    No follow-ups scheduled for today.
                </div>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($todayFollowups as $f)

                        @php
                            $rowClass = '';

                            if (in_array($f->call_status, ['Interested', 'Visited Office'])) {
                                $rowClass = 'row-call-done';
                            } elseif (in_array($f->call_status, [
                                'Follow-up Required','Call Back Later',
                                'WhatsApp Replied','WhatsApp Seen Only'
                            ])) {
                                $rowClass = 'row-follow-up';
                            } elseif (in_array($f->call_status, [
                                'Not Answered','Ringing','Switched Off',
                                'Busy','Wrong Number'
                            ])) {
                                $rowClass = 'row-not-picked';
                            } elseif ($f->call_status === 'Registered') {
                                $rowClass = 'row-registered';
                            }
                        @endphp

                        <tr class="{{ $rowClass }}">
                            <td>{{ $f->enquiry->name }}</td>
                            <td>{{ $f->enquiry->mobile }}</td>
                            <td>{{ $f->note }}</td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    {{ $f->call_status ?? 'Pending' }}
                                </span>
                                <div class="small text-muted mt-1">
                                    {{ $f->updated_at->format('d M Y, h:i A') }}
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('sales.enquiries.show', $f->enquiry_id) }}"
                                   class="btn btn-sm btn-primary">
                                   View
                                </a>
                            </td>
                        </tr>

                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>

    {{-- ================= UPCOMING FOLLOWUPS ================= --}}
    <h4>Upcoming Follow-Ups</h4>

    <div class="card">
        <div class="card-body">

            @if($upcomingFollowups->count() == 0)
                <div class="alert alert-light text-center">
                    No upcoming follow-ups.
                </div>
            @else
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($upcomingFollowups as $f)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($f->next_followup_date)->format('d M Y') }}</td>
                            <td>{{ $f->enquiry->name }}</td>
                            <td>{{ $f->enquiry->mobile }}</td>
                            <td>{{ $f->note }}</td>
                            <td>
                                <a href="{{ route('sales.enquiries.show', $f->enquiry_id) }}"
                                   class="btn btn-sm btn-primary">
                                   View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>


    {{-- ================= COLLEGE WISE ASSIGNED ================= --}}
<h4 class="mt-5">College-wise Assigned Leads</h4>

<div class="card mb-4">
    <div class="card-body">

        @if($collegeWiseAssigned->count() === 0)
            <div class="alert alert-light text-center">
                No college data available.
            </div>
        @else
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>College</th>
                        <th class="text-center">Assigned Leads</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($collegeWiseAssigned as $row)
                        <tr>
                            <td>
                                {{ $row->collegeData->FUllName ?? 'Unknown College' }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-primary">
                                    {{ $row->total }}
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('sales.enquiries.index', ['college' => $row->college]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        @endif

    </div>
</div>


</div>
@endsection
