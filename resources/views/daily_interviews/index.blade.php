@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
    text-transform: capitalize;
}
</style>
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Daily Interview Management</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('daily-interviews.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                     Schedule Interview
                </a>
            </div>
        </div>
    </div>
    

    {{-- Filter Form (UPDATED) --}}
    <div class="card mb-3 p-3">
        <form method="GET" action="{{ route('daily-interviews.index') }}" class="row g-3 align-items-end">
            
            {{-- Technology Filter --}}
            <div class="col-md-3">
                <label for="technology_filter" class="form-label">Filter by Technology</label>
                <select name="technology" id="technology_filter" class="form-select">
                    <option value="">-- All Technologies --</option>
                    @foreach($available_tech as $tech)
                        <option value="{{ $tech }}" {{ request('technology') == $tech ? 'selected' : '' }}>
                            {{ $tech }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="col-md-3">
                <label for="status_filter" class="form-label">Filter by Status</label>
                <select name="status" id="status_filter" class="form-select">
                    <option value="">-- All Statuses --</option>
                    @foreach($available_status as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Interview Type Filter --}}
            <div class="col-md-3">
                <label for="type_filter" class="form-label">Filter by Type</label>
                <select name="type" id="type_filter" class="form-select">
                    <option value="">-- All Types --</option>
                    @foreach($available_type as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Date Quick Select Filter (New) --}}
            <div class="col-md-3">
                <label for="date_filter" class="form-label">Date Quick Select</label>
                <select name="date_filter" id="date_filter" class="form-select">
                    <option value="">-- Custom Date Range --</option>
                    @foreach($date_options as $key => $label)
                        <option value="{{ $key }}" {{ request('date_filter', 'upcoming') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Range Start (New) --}}
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            {{-- Date Range End (New) --}}
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            {{-- Submission Buttons --}}
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                <a href="{{ route('daily-interviews.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
    {{-- End Filter Form --}}
    
    

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Your existing table structure follows... --}}
    <table id="interviewsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Candidate</th>
                <th>Technology</th>
                <th>Type</th>
                <th>Date/Time</th>
                <th>Interviewer</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($interviews as $interview)
            <tr>
                <td>{{ $interview->candidate_name }}</td>
                <td>{{ $interview->technology }}</td>
                <td>{{ $interview->interview_type }}</td>
                <td>{{ $interview->availability_datetime?->format('M d, Y H:i A') ?? 'N/A' }}</td>
                <td>{{ $interview->interviewer_name ?? 'N/A' }}</td>
                <td>
                    @php
                        $badgeClass = match($interview->interview_status) {
                            'Scheduled' => 'bg-warning text-dark',
                            'Completed' => 'bg-info',
                            'Offered' => 'bg-success',
                            'Rejected', 'No Show' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $interview->interview_status }}</span>
                </td>
                <td>
                    {{-- Show --}}
                    <a href="{{ route('daily-interviews.show', $interview) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="View Details">
                        <i class="fas fa-eye"></i> 
                    </a>

                    {{-- Edit --}}
                    <a href="{{ route('daily-interviews.edit', $interview) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('daily-interviews.destroy', $interview) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                onclick="return confirm('Delete Interview Record?')"
                                data-bs-toggle="tooltip"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    {{ $interviews->links('pagination::bootstrap-5') }}

</div>
@endsection

@push('scripts')
{{-- Include your necessary scripts --}}
<script>
$(document).ready(function() {
     $('#interviewsTable').DataTable({
        "pageLength": 50,
        "lengthMenu": [5, 10, 25, 50, 100],
        paging: false,       
        info: false,           
        lengthChange: false
    });
    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });
});
</script>
@endpush