@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Student Evaluations</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('student-evaluations.create') }}"
               class="btn mb-3"
               style="background-color:#6b51df;color:#fff;">
                Add Evaluation
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table id="evaluationTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Student</th>
                <th>Trainer</th>
                <th>Attendance</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($evaluations as $ev)
            <tr>
                <td>{{ ucwords($ev->student?->student_name) }}</td>
                <td>{{ ucwords($ev->trainer?->name) }}</td>
                <td>{{ $ev->attendance_percentage }}%</td>
                <td>{{ $ev->created_at->format('d M Y') }}</td>
                <td>
    
        <a href="{{ route('student-evaluations.download.full', $ev) }}"
           class="btn btn-sm"
           title="Download Full">
           <i class="fas fa-file-pdf"></i>
        </a>

        <a href="{{ route('student-evaluations.download.empty', $ev) }}"
           class="btn btn-sm"
           title="Download Empty">
           <i class="fas fa-file-alt"></i>
        </a>

         {{-- SINGLE EMAIL --}}
           <form method="POST"
                  action="{{ route('student-evaluations.email', $ev) }}"
                  style="display:inline;">
                @csrf
                <button type="submit"
                        class="btn btn-sm"
                        title="Email">
                    <i class="fas fa-envelope"></i>
                </button>
            </form>


    <a href="{{ route('student-evaluations.edit',$ev) }}"
       class="btn btn-sm"
       title="Edit">
       <i class="fas fa-edit"></i>
    </a>

    <form action="{{ route('student-evaluations.destroy',$ev) }}"
          method="POST"
          style="display:inline;">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm"
                data-swal-confirm="Delete evaluation?"
                title="Delete">
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
$(function(){
    $('#evaluationTable').DataTable();
});
</script>
@endpush
