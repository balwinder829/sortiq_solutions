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
            <h1 class="page_heading">CV Management</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('cvs.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                     Add CV Record
                </a>
            </div>
        </div>
    </div>
   

    {{-- Filter Form --}}
    <div class="card mb-3 p-3">
        <form method="GET" action="{{ route('cvs.index') }}" class="row g-3 align-items-end">
            
            {{-- Technology Filter (Dropdown) --}}
            <div class="col-md-4">
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

            {{-- Status Filter (Fresher/Exp) --}}
            <div class="col-md-3">
                <label for="status_filter" class="form-label">Status</label>
                <select name="status" id="status_filter" class="form-select">
                    <option value="">-- All Statuses --</option>
                    @foreach($available_status as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submission Buttons --}}
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                <a href="{{ route('cvs.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
    {{-- End Filter Form --}}

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="cvsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Mobile</th>
                <th>Technology</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($cvs as $cv)
            <tr>
                <td>{{ $cv->employee_name }}</td>
                <td>{{ $cv->phone_number }}</td>
                <td>{{ $cv->technology }}</td>
                <td>
                    <span class="badge 
                        @if($cv->experience_status == 'Fresher') bg-success
                        @else bg-info
                        @endif">
                        {{ $cv->experience_status }}
                    </span>
                </td>
                
                <td>
                    {{-- View Link (Opens GDrive Link) --}}
                    <a href="{{ $cv->gdrive_link }}" target="_blank"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="View CV (Google Drive)">
                        <i class="fas fa-file-alt"></i> 
                    </a>

                    {{-- Edit --}}
                    <a href="{{ route('cvs.edit', $cv) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('cvs.destroy', $cv) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                onclick="return confirm('Delete CV Record?')"
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
    
    {{ $cvs->links('pagination::bootstrap-5') }}

</div>
@endsection

@push('scripts')
{{-- Include your DataTables scripts here if you prefer client-side handling, 
     otherwise, just keep jQuery and Bootstrap for the tooltips/styling --}}
 
<script>
$(document).ready(function() {
    $('#cvsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
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