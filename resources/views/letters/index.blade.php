@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Official Letters</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('letters.create') }}" class="btn" style="background-color:#6b51df;color:#fff;">
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
                        <option value="intern" {{ ($selectedType ?? '') === 'intern' ? 'selected' : '' }}>Intern Letter</option>
                        <option value="intern_with_package" {{ ($selectedType ?? '') === 'intern_with_package' ? 'selected' : '' }}>Intern With Package Letter</option>
                        <option value="intern_custom" {{ ($selectedType ?? '') === 'intern_custom' ? 'selected' : '' }}>Intern Custom Letter</option>
                        <option value="offer" {{ ($selectedType ?? '') === 'offer' ? 'selected' : '' }}>Offer Letter</option>
                        <option value="experience" {{ ($selectedType ?? '') === 'experience' ? 'selected' : '' }}>Experience Letter</option>
                        <option value="relieving" {{ ($selectedType ?? '') === 'relieving' ? 'selected' : '' }}>Relieving Letter</option>
                        <option value="appointment" {{ ($selectedType ?? '') === 'appointment' ? 'selected' : '' }}>Appointment Letter</option>
                        <option value="appointment_with_bond" {{ ($selectedType ?? '') === 'appointment_with_bond' ? 'selected' : '' }}>Appointment With Bond Letter</option>
                        <option value="increment" {{ ($selectedType ?? '') === 'increment' ? 'selected' : '' }}>Increment Letter</option>
                        <option value="bond" {{ ($selectedType ?? '') === 'bond' ? 'selected' : '' }}>Employment Bond Letter</option>
                        <option value="custom_bond" {{ ($selectedType ?? '') === 'custom_bond' ? 'selected' : '' }}>Custom Bond Letter</option>
                         <option value="noc" {{ ($selectedType ?? '') === 'noc' ? 'selected' : '' }}>NOC Letter</option>
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <!-- <button type="submit" class="btn btn-primary">Search</button> -->
                    <a href="{{ route('letters.index') }}" class="btn btn-secondary">Reset</a>
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
                <th>Type</th>
                <th>Emp Code</th>
                <th>Name</th>
                <th>Position</th>
                <th>Issue Date</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td>
                    <span class="badge bg-info">
                        {{ ucfirst(str_replace('_',' ', $letter->letter_type)) }}
                    </span>
                </td>

                <td>
                    {{ $letter->employee->emp_code ?? '-' }}
                </td>

                <td>
                    {{ $letter->employee->emp_name ?? '-' }}
                </td>

                <td>
                    {{ $letter->employee->position ?? '-' }}
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

                <td class="d-flex gap-1">
                    <a href="{{ route('letters.edit', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="{{ route('letters.download', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-download"></i>
                    </a>

                    @if($letter->letter_type != 'custom')
                    <form action="{{ route('letters.email', $letter) }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-sm">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </form>
                    @endif

                    <form
                        action="{{ route('letters.destroy', $letter) }}"
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
        order: [[6, 'desc']] 
    });
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
