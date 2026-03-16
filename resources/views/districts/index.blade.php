@extends('layouts.app')

@section('content')
<style>
.batch-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}
.batch-circle:hover {
    background: #0b5ed7;
    transform: scale(1.1);
}
 
table.dataTable td {
    text-transform: capitalize;
}
 </style>

<div class="container">

<div class="row mb-2 align-items-end">

    {{-- LEFT: PAGE TITLE --}}
    <div class="col-md-4">
        <h1 class="page_heading">Districts</h1>
    </div>
    
    <div class="col-md-4">
        <label>Filter by State</label>
        <select id="stateFilter" class="form-control">
            <option value="">All States</option>
            @foreach($states as $state)
                <option value="{{ $state->id }}">
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- RIGHT: ADD MENTOR BUTTON --}}
    <div class="col-md-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('districts.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add District
            </a>
        </div>
    </div>



</div>
  
    </div>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table id="trainers-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>State</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody></tbody>
        </table>
    </div>

</div>

@endsection

@push('scripts')
<!-- <script>
$(document).ready(function () {
    var params = new URLSearchParams(window.location.search);
    $('#trainers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('workshops.data') }}",
            data: function (d) {
                // d.course = params.get('course') || '';
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 }
            
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100]
    });
});
</script> -->

<script>
$(document).ready(function () {

    var table = $('#trainers-table').DataTable({
    processing: true,
    serverSide: true,
     ajax: {
            url: "{{ route('districts.data') }}",
            data: function (d) {
                d.state_id = $('#stateFilter').val();
            }
        },
    columns: [
        { data: 0, name: 'id' },
        { data: 1, name: 'state' },
        { data: 2, name: 'name' },
        { data: 3, name: 'actions', orderable:false, searchable:false }
    ],
    pageLength: 50,
    lengthMenu: [5, 10, 25, 50, 100]
});

      // Reload table when state changes
    $('#stateFilter').change(function () {
        table.ajax.reload();
    });

});
</script>

 

@endpush
