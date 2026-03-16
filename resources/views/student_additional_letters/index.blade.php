@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Student Additional Letters</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('student-additional-letters.create') }}"
                   class="btn" style="background-color:#6b51df;color:#fff;">
                    Generate Letter
                </a>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <form method="GET"
                   id="filterForm" 
                  class="row g-2">

                <div class="col-md-6">
                    <select name="internship_type" class="form-control filterchange">
                        <option value="">All Internship Types</option>
                        <option value="free" {{ request('internship_type') === 'free' ? 'selected' : '' }}>Free Internship Letter
                        </option>
                        <option value="stipend" {{ request('internship_type') === 'stipend' ? 'selected' : '' }}>
                            Stipend Internship Letter
                        </option>
                        <option value="offer" {{ request('internship_type') === 'offer' ? 'selected' : '' }}>Offer Letter</option>
                        <option value="custom" {{ request('internship_type') === 'custom' ? 'selected' : '' }}>Custom Type Letter</option>
                        <option value="noc" {{ request('internship_type') === 'noc' ? 'selected' : '' }}>Non-Consent Letter</option>
                        <option value="mutual_consent" {{ request('internship_type') === 'mutual_consent' ? 'selected' : '' }}>Mutual Consent Letter</option>
                        <option value="training_consent" {{ request('internship_type') === 'training_consent' ? 'selected' : '' }}>Training Consent Letter</option>
                        <option value="placement" {{ request('internship_type') === 'placement' ? 'selected' : '' }}>Student Placement Letter</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <!-- <button type="submit" class="btn btn-primary">
                        Search
                    </button> -->
                    <a href="{{ route('student-additional-letters.index') }}"
                       class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    {{-- Table --}}
    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Letter Type</th>
                <th>Student Name</th>
                <th>Email</th>
                
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td>
                    @php
                    $types = [
                        'free' => 'Free Internship Letter',
                        'stipend' => 'Stipend Internship Letter',
                        'offer' => 'Offer Letter',
                        'custom' => 'Custom Type Letter',
                        'noc' => 'Non-Consent Letter',
                        'mutual_consent' => 'Mutual Consent Letter',
                        'training_consent' => 'Training Consent Letter',
                        'placement' => 'Student Placement Letter'
                    ];
                    @endphp

                    <span class="badge bg-success">
                        {{ $types[$letter->internship_type] ?? ucfirst(str_replace('_',' ',$letter->internship_type)) }}
                    </span>
                    </td>
                <td>{{ $letter->student->student_name ?? 'N/A' }}</td>
                <td>{{ $letter->student->email_id ?? 'N/A' }}</td>
                <!-- <td>{{ $letter->email }}</td> -->
                
                <td>{{ $letter->created_at->format('d M Y') }}</td>
                <td>
                    {{-- Edit --}}
                    <a href="{{ route('student-additional-letters.edit', $letter) }}"
                       class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Download --}}
                    <a href="{{ route('student-additional-letters.pdf', $letter) }}"
                       class="btn btn-sm" title="Download">
                        <i class="fas fa-download"></i>
                    </a>

                    {{-- Email --}}
                    <form action="{{ route('student-additional-letters.email', $letter) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-sm" title="Send Email">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </form>

                    <form action="{{ route('student-additional-letters.destroy', $letter) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-swal-confirm="Do you want to delete this?">
                                    <i class="fa fa-trash"></i>
                    </form>
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
    $('#lettersTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        order: []
    });
});
</script>
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        $('#filterForm').submit();
        
    });
    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 500); // waits 500ms after typing stops
    });

});
</script>
@endpush
