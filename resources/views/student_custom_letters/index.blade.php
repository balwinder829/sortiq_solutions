@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Student Custom Letters</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('student-custom-letters.create') }}" class="btn" style="background-color:#6b51df;color:#fff;">
                    Generate Letter
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <form method="GET" id="filterForm" class="row g-2">

                <div class="col-md-6">
    <select name="letter_type" class="form-control filterchange">

        <option value="">All Letter Types</option>

        <option value="part_time_job_opportunity"
            {{ request('letter_type') === 'part_time_job_opportunity' ? 'selected' : '' }}>
            Part Time Job Opportunity
        </option>

        <option value="strict_offer_letter"
            {{ request('letter_type') === 'strict_offer_letter' ? 'selected' : '' }}>
            Strict Offer Letter
        </option>

        <option value="offer_letter"
            {{ request('letter_type') === 'offer_letter' ? 'selected' : '' }}>
            Offer Letter
        </option>

        <option value="strict_consent_letter"
            {{ request('letter_type') === 'strict_consent_letter' ? 'selected' : '' }}>
            Strict Consent Letter
        </option>

        <option value="internship_consent"
            {{ request('letter_type') === 'internship_consent' ? 'selected' : '' }}>
            Internship Consent
        </option>

        <option value="stipend_policy"
            {{ request('letter_type') === 'stipend_policy' ? 'selected' : '' }}>
            Stipend Policy
        </option>

    </select>
</div>

                <div class="col-md-4 d-flex gap-2">
                    <!-- <button type="submit" class="btn btn-primary">Search</button> -->
                    <a href="{{ route('student-custom-letters.index') }}" class="btn btn-secondary">Reset</a>
                </div>

            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Name</th>
                <th>Issue Date</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td></td>
                
                <td>
                    <span class="badge bg-info">
                        {{ ucfirst(str_replace('_',' ', $letter->letter_type)) }}
                    </span>
                </td>

                <td>
                    {{ $letter->student_name ?? '-' }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($letter->created_at)->format('d M Y') }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($letter->updated_at)->format('d M Y') }}
                </td>

                <td>
                    <div class="d-flex gap-1">
                    <a href="{{ route('student-custom-letters.edit', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="{{ route('student-custom-letters.download', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-download"></i>
                    </a>

                    <form
                        action="{{ route('student-custom-letters.destroy', $letter) }}"
                        method="POST"
                        style="display:inline;"
                         data-swal-confirm="Are you sure you want to delete this letter?"
                    >
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                </td>
            </tr>
            @endforeach
            </tbody>

    </table>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
   var table = $('#lettersTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        ordering: false,
        columnDefs: [
            {
                targets: 0, // first column
                searchable: false,
                orderable: false
            }
        ]
    });

    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
});
</script>
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('input change', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 100); // waits 500ms after typing stops
    });

});
</script>
@endpush
