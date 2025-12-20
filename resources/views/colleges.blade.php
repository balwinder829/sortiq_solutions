@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Colleges Data</h2>
    <table class="table table-bordered" id="colleges-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>College Name</th>
                <th>Total Students</th>
                <th>Joined</th>
                <th>Unjoined</th>
                <th>Technologies</th>
            </tr>
        </thead>
    </table>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

@push('scripts')
<script>
    $(document).ready(function() {
        $('#colleges-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("colleges.data") }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'college_name', name: 'college_name' },
                { data: 'total_students', name: 'total_students' },
                { data: 'joined', name: 'joined' },
                { data: 'unjoined', name: 'unjoined' },
                { data: 'technologies', name: 'technologies' }
            ]
        });
    });
</script>
@endpush

@endsection
