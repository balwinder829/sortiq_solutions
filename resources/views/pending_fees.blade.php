@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Pending Fees</h2>

    <div class="card shadow-sm p-3">
        <table id="pendingTable" class="table table-bordered table-striped">
            <thead class="table">
                <tr>
                    <th>Student</th>
                    <th>Session</th>
                    <th>Batch</th>
                    <th>Contact</th>
                    <th>Due Date</th>
                    <th>Pending Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $s)
                <tr>
                    <td>{{ $s->student_name }}</td>
                    <td>{{ $s->sessionData->session_name ?? '-' }}</td>
                    <td>{{ $s->batchData->batch_name ?? '-' }}</td>
                    <td>{{ $s->contact }}</td>
                    <td>{{ $s->next_due_date }}</td>
                    <td>₹{{ number_format($s->pending_fees, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Laravel pagination --}}
        <div class="mt-3">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#pendingTable').DataTable({
        paging: false,     // disable DataTable pagination
        searching: false,  // optional
        ordering: false,   // optional
        info: false,       // optional
        responsive: true
    });
});
</script>
@endpush
