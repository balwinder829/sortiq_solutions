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

    <div class="col-md-8">
        <h1 class="page_heading">Manual Data</h1>
    </div>

    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.manual_data.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add 
            </a>
        </div>
    </div>

</div>

{{-- FILTER FORM --}}
<div class="col-md-12 mb-3">
    <form class="row g-2">

    {{-- COLLEGE --}}
    <div class="col-md-3">
        <select name="college_id" class="form-select select2">
            <option value="">College</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}">
                    {{ $college->FullName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- CLASS --}}
    <div class="col-md-2">
        <select name="class" class="form-select">
            <option value="">Class</option>
            <option>BCA</option>
            <option>MCA</option>
            <option>BTech</option>
            <option>BSc</option>
        </select>
    </div>

    {{-- SEMESTER --}}
    <div class="col-md-1">
        <select name="semester" class="form-select">
            <option value="">Sem</option>
            @for($i=1;$i<=8;$i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
    </div>

    {{-- COURSE TYPE --}}
    <div class="col-md-2">
        <select name="course_type" class="form-select">
            <option value="">Course</option>
            <option value="Degree">Degree</option>
            <option value="Diploma">Diploma</option>
        </select>
    </div>

    {{-- GENDER --}}
    <div class="col-md-1">
        <select name="gender" class="form-select">
            <option value="">Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
    </div>

    

    {{-- EMAIL --}}
    <div class="col-md-2">
        <input type="text" name="email" class="form-control" placeholder="Email">
    </div>

    {{-- MOBILE --}}
    <div class="col-md-2">
        <input type="text" name="mobile" class="form-control" placeholder="Mobile">
    </div>

    {{-- DATE --}}
    <div class="col-md-2">
        <input type="date" name="date" class="form-control">
    </div>

    {{-- RANGE --}}
    <div class="col-md-2">
        <select name="range" class="form-select">
            <option value="">Range</option>
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="last_7_days">Last 7 Days</option>
            <option value="last_30_days">Last 30 Days</option>
            <option value="this_month">This Month</option>
        </select>
    </div>

    <div class="col-md-2">
        <button type="button" id="resetFilters" class="btn btn-secondary">
            Reset
        </button>
    </div>

</form>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
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
                <th>College</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Class</th>
                <th>Semester</th>
                <th>Course Type</th>
                <th>Gender</th>
                <th>Created</th>
                <th width="100">Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

</div>

@endsection

@push('scripts')

<script>

    
let colleges = @json($colleges);

function loadFilteredColleges(){

    let state    = $('#filter-state').val();
    let district = $('#filter-district').val();
    let type     = $('#filter-college-type').val();

    let collegeDropdown = $('select[name=college_id]');
    collegeDropdown.empty();
    collegeDropdown.append('<option value="">College</option>');

    colleges.forEach(function(c){

        if(state && c.state_id != state) return;
        if(district && c.district_id != district) return;
        if(type !== '' && c.college_type != type) return;

        collegeDropdown.append(
            `<option value="${c.id}">${c.full_name ?? c.college_name}</option>`
        );

    });

}
</script>

<script>
$(document).ready(function () {

    let params = new URLSearchParams(window.location.search);

    if (params.get('range')) {
        $('select[name=range]').val(params.get('range'));
    }

    var table = $('#trainers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.manual_data.index') }}",
            data: function (d) {
                d.college_id  = $('select[name=college_id]').val();
                d.class       = $('select[name=class]').val();
                d.semester    = $('select[name=semester]').val();
                d.course_type = $('select[name=course_type]').val();
                d.gender      = $('select[name=gender]').val();
                // d.source      = $('select[name=source]').val();

                d.email       = $('input[name=email]').val();
                d.mobile      = $('input[name=mobile]').val();
                d.date        = $('input[name=date]').val();
                d.range       = $('select[name=range]').val();
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'student_name' },
            { data: 2, name: 'college_id', orderable:false, searchable:false },
            { data: 3, name: 'student_email' },
            { data: 4, name: 'student_mobile' },
            { data: 5, name: 'class' },
            { data: 6, name: 'semester' },
            { data: 7, name: 'course_type' },
            { data: 8, name: 'gender' },
            { data: 9, name: 'created_at' },
            { data: 10, name: 'actions', orderable:false, searchable:false }
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']]
    });

    $('select, input').on('change', function () {
        table.ajax.reload();
    });

    $('#resetFilters').on('click', function () {

        let form = $(this).closest('form')[0];
        form.reset();

        table.ajax.reload();
    });

});

</script>

@endpush

