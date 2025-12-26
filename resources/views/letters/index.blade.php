@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Letters</h4>

    {{-- Top Actions & Filters --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('letters.create') }}"
           class="btn"
           style="background-color:#6b51df;color:#fff;">
            Add Letter
        </a>

        {{-- FILTER FORM --}}
        <form method="GET"
              action="{{ route('letters.index') }}"
              class="d-flex gap-2 align-items-center">

            <select name="letter_type" class="form-control">
                <option value="">All Letter Types</option>
                <option value="offer"
                    {{ ($selectedType ?? '') === 'offer' ? 'selected' : '' }}>
                    Offer Letter
                </option>
                <option value="experience"
                    {{ ($selectedType ?? '') === 'experience' ? 'selected' : '' }}>
                    Experience Letter
                </option>
                <option value="appointment"
                    {{ ($selectedType ?? '') === 'appointment' ? 'selected' : '' }}>
                    Appointment Letter
                </option>
            </select>

            <button type="submit" class="btn btn-primary">
                Search
            </button>

            <a href="{{ route('letters.index') }}"
               class="btn btn-secondary">
                Reset
            </a>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- LETTERS TABLE --}}
    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>Emp Code</th>
                <th>Name</th>
                <th>Position</th>
                <th>Issue Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td>
                    <span class="badge bg-info">
                        {{ ucfirst($letter->letter_type) }}
                    </span>
                </td>
                <td>{{ $letter->emp_code ?? '-' }}</td>
                <td>{{ $letter->emp_name }}</td>
                <td>{{ $letter->position }}</td>
                <td>{{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}</td>
                <td>
                    {{-- Edit --}}
                    <a href="{{ route('letters.edit', $letter) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit Letter">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- Download --}}
                    <a href="{{ route('letters.download', $letter) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Download PDF">
                        <i class="fas fa-download"></i>
                    </a>

                    {{-- Email --}}
                    <form action="{{ route('letters.email', $letter) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        <button class="btn btn-sm"
                                data-bs-toggle="tooltip"
                                title="Send Email">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#lettersTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
@endpush
