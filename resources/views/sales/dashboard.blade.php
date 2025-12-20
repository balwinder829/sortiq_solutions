@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Sales Dashboard</h3>
    <div class="row mb-4">

    {{-- TODAY'S ASSIGNED LEADS --}}
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5 class="fw-bold">Today's Assigned</h5>
            <span class="badge bg-primary" style="font-size: 18px;">
                {{ $todaysAssigned }}
            </span>
        </div>
    </div>

    {{-- TODAY FOLLOWUPS --}}
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5 class="fw-bold">Today's Follow-ups</h5>
            <span class="badge bg-warning text-dark" style="font-size: 18px;">
                {{ $todayFollowups->count() }}
            </span>
        </div>
    </div>

    {{-- MISSED FOLLOWUPS --}}
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5 class="fw-bold">Missed Follow-ups</h5>
            <span class="badge bg-danger" style="font-size: 18px;">
                {{ $missedFollowups }}
            </span>
        </div>
    </div>

    {{-- UPCOMING FOLLOWUPS --}}
    <div class="col-md-3">
        <div class="card shadow-sm p-3 text-center">
            <h5 class="fw-bold">Upcoming Follow-ups</h5>
            <span class="badge bg-info text-dark" style="font-size: 18px;">
                {{ $upcomingFollowups->count() }}
            </span>
        </div>
    </div>

</div>

    {{-- ====================== --}}
    {{-- STATS CARDS --}}
    {{-- ====================== --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h4>{{ $totalAssigned }}</h4>
                <p>Total Assigned Leads</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h4>{{ $statusCount['followup'] ?? 0 }}</h4>
                <p>Active Follow-ups</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h4>{{ $statusCount['closed'] ?? 0 }}</h4>
                <p>Closed Leads</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 text-center">
                <h4>{{ $statusCount['joined'] ?? 0 }}</h4>
                <p>Joined Students</p>
            </div>
        </div>

    </div>



    {{-- ====================== --}}
    {{-- TODAY FOLLOWUPS --}}
    {{-- ====================== --}}
    <h4>Today's Follow-Ups</h4>

    <div class="card mb-4">
        <div class="card-body">

            @if($todayFollowups->count() == 0)
                <p class="text-muted">No follow-ups scheduled for today.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($todayFollowups as $f)
                        <tr>
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



    {{-- ====================== --}}
    {{-- UPCOMING FOLLOWUPS --}}
    {{-- ====================== --}}
    <h4>Upcoming Follow-Ups</h4>

    <div class="card">
        <div class="card-body">

            @if($upcomingFollowups->count() == 0)
                <p class="text-muted">No upcoming follow-ups.</p>
            @else
                <table class="table table-bordered">
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

</div>
@endsection
