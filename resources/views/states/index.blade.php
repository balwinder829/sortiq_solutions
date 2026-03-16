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
    <div class="col-md-8">
        <h1 class="page_heading">States</h1>
    </div>

    {{-- RIGHT: ADD MENTOR BUTTON --}}
    <div class="col-md-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('states.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add State
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
                    <th>Code</th>
                    
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

     $('#trainers-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('states.data') }}",
    columns: [
        { data: 0, name: 'id' },
        { data: 1, name: 'name' },
        { data: 2, name: 'code' },
        { data: 3, name: 'actions', orderable:false, searchable:false }
    ],
    pageLength: 50,
    lengthMenu: [5, 10, 25, 50, 100]
});

});
</script>

 

@endpush
