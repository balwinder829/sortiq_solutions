@extends('layouts.app')

@section('title', 'Attendance Detail')

@section('content')

<div class="container mt-4">
    <div class="row mb-2">
    <div class="col-md-6">
        
        <!-- <h1 class="page_heading">
            Attendance Detail –
            {{ $actorType === 'trainer' ? $actor->name : $actor->name }}
        </h1> -->

        <h1 class="page_heading">
            {{ auth()->guard('sales_staff')->check() ? 'Calling Detail' : 'Attendance Detail' }} –
            {{ $actor->name }}
        </h1>
    </div>

    <div class="col-md-6">
        <form method="GET" class="row g-2 mb-3 justify-content-end">
            <div class="col-md-4">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>
</div>



    @php
        $start = \Carbon\Carbon::parse($month . '-01');
        $end   = $start->copy()->endOfMonth();

        $presentCount = 0;
        $absentCount  = 0;

        // Pre-calculate counts (exclude Sundays + Holidays)
        $temp = $start->copy();
        while ($temp <= $end) {

            $record = $attendance->first(fn($att) =>
                $att->login_time->isSameDay($temp)
            );

            if (
                !$temp->isSunday() &&
                !in_array($temp->format('Y-m-d'), $holidays)
            ) {
                if ($record && $record->logout_time) {
                    $presentCount++;
                } else {
                    $absentCount++;
                }
            }

            $temp->addDay();
        }
    @endphp

    <div class="card p-3 shadow-sm">
        <div class="row mb-2 align-items-center">
            <div class="row mb-2 align-items-center">
                <div class="col-md-6">
                    <h1 class="page_heading mb-0">
                        Monthly Record - {{ \Carbon\Carbon::parse($month)->format('F Y') }}
                    </h1>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        <div class="border border-success rounded px-3 py-2 text-center">
                            <div class="text-success small fw-semibold">Present</div>
                            <div class="fw-bold">{{ $presentCount }}</div>
                        </div>

                        <div class="border border-secondary rounded px-3 py-2 text-center">
                            <div class="text-secondary small fw-semibold">Absent / Leave</div>
                            <div class="fw-bold">{{ $absentCount }}</div>
                        </div>
                    </div>
                </div>
            </div>


        <div class="table-responsive">
            <table id="attendanceDetailTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                        <th>Total Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @php $current = $start->copy(); @endphp

                    @while($current <= $end)
                        @php
                            $record = $attendance->first(fn($att) =>
                                $att->login_time->isSameDay($current)
                            );

                            $isHoliday = $current->isSunday()
                                || in_array($current->format('Y-m-d'), $holidays);
                        @endphp

                        <tr>
                            <td>{{ $current->format('d M Y') }}</td>

                            <td>{{ $record ? $record->login_time->format('h:i A') : '—' }}</td>

                            <td>
                                {{ $record && $record->logout_time
                                    ? $record->logout_time->format('h:i A')
                                    : '—' }}
                            </td>

                            <td>
                                @if($record && $record->logout_time)
                                    @php
                                        $mins = $record->login_time->diffInMinutes($record->logout_time);
                                    @endphp
                                    {{ floor($mins/60) }} hrs {{ $mins%60 }} mins
                                @else
                                    —
                                @endif
                            </td>

                            <td>
    @if($current->isSunday())
        <span class="badge bg-secondary">Sunday</span>

    @elseif(in_array($current->format('Y-m-d'), $holidays))
        <span class="badge bg-info">Holiday</span>

    @elseif(!$record)
        <span class="badge bg-danger">Absent</span>

    @elseif(!$record->logout_time)
        <span class="badge bg-warning">Incomplete</span>

    @else
        <span class="badge bg-success">Present</span>
    @endif
</td>

                        </tr>

                        @php $current->addDay(); @endphp
                    @endwhile
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#attendanceDetailTable').DataTable({
        pageLength: 31,
        lengthMenu: [31]
    });
});
</script>
@endpush
