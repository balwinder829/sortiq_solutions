@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-4">
        <h1 class="page_heading">Hard Data</h1>
    </div>

    <div class="col-md-8 text-end">
        <button id="moveToEnquiry" class="btn btn-warning mb-3">
            Move to Enquiries
        </button>

        <a href="{{ route('admin.hard_data.create') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Add
        </a>
        <a href="{{ route('admin.hard_data.import.form') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Import
        </a>
               <a href="#" id="exportExcel"
   class="btn btn-success mb-3">
    Export Excel
</a>
    </div>
<form id="filterForm" class="row mb-3">
   

    {{-- ✅ NEW: COLLEGE --}}
    <div class="col-md-2">
        <select name="college_id" class="form-control select2">
            <option value="">All College</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}">
                    {{ $college->college_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- ✅ NEW: COURSE TYPE --}}
    <div class="col-md-2">
        <select name="course_type" class="form-control">
            <option value="">All Course</option>
            <option value="Degree">Degree</option>
            <option value="Diploma">Diploma</option>
        </select>
    </div>
     <div class="col-md-2">
        <select name="gender" class="form-control">
            <option value="">All Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="is_moved" class="form-control">
            <option value="">All Status</option>
            <option value="1">Moved</option>
            <option value="0">Not Moved</option>
        </select>
    </div>

     <div class="col-md-3">
        <input type="text" name="email" class="form-control" placeholder="Email">
    </div>

    <div class="col-md-3">
        <input type="text" name="mobile" class="form-control mt-2" placeholder="Mobile">
    </div>
<div class="col-md-2">
    <button type="button" id="resetFilters" class="btn btn-secondary w-100 mt-2">
        Reset
    </button>
</div>
</form>
</div>

{{-- SUCCESS --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- TABLE --}}
<div class="table-responsive">
<table id="trainers-table" class="table table-bordered table-striped">

<thead>
<tr>
    <th>
        <input type="checkbox" id="checkAll">
    </th>
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
    <th>Actions</th>
</tr>
</thead>

<tbody></tbody>

</table>
</div>

</div>

@endsection


@push('scripts')

<script>

let selectedIds = new Set();

// ✅ DATATABLE
var table = $('#trainers-table').DataTable({
    processing: true,
    serverSide: true,
    // ajax: "{{ route('admin.hard_data.index') }}",
    ajax: {
    url: "{{ route('admin.hard_data.index') }}",
    data: function (d) {
        d.email = $('input[name=email]').val();
        d.mobile = $('input[name=mobile]').val();
        d.gender = $('select[name=gender]').val();
        d.college_id = $('select[name=college_id]').val();
        d.course_type = $('select[name=course_type]').val();
         d.is_moved = $('select[name=is_moved]').val();
    }
},
    columns: [

        // ✅ CHECKBOX COLUMN
        {
            data: 0,
            render: function (data, type, row) {

                if (row.is_moved_to_enquiry == 1) {
                    return '<span class="badge bg-info">Moved</span>';
                }

                return `<input type="checkbox" class="record_checkbox" value="${data}">`;
            },
            orderable: false,
            searchable: false
        },

        { data: 0 },
        { data: 1 },
        { data: 2 },
        { data: 3 },
        { data: 4 },
        { data: 5 },
        { data: 6 },
        { data: 7 },
        { data: 8 },
        { data: 9 },
        { data: 10 }

    ]
});
$('#filterForm input, #filterForm select').on('keyup change', function () {
    table.ajax.reload();
});

// ✅ SELECT SINGLE
$(document).on('change', '.record_checkbox', function () {
    let id = $(this).val();

    if ($(this).is(':checked')) {
        selectedIds.add(id);
    } else {
        selectedIds.delete(id);
    }
});


// ✅ SELECT ALL
$('#checkAll').on('change', function () {
    let checked = this.checked;

    $('.record_checkbox').each(function () {
        let id = $(this).val();

        if (checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        $(this).prop('checked', checked);
    });
});


// ✅ MOVE TO ENQUIRY
$('#moveToEnquiry').click(function () {

    if (selectedIds.size === 0) {
        Swal.fire('No selection', 'Select at least one student', 'warning');
        return;
    }

    let sessionOptions = @json($sessionsList ?? []);
    let optionsHtml = '';

    Object.keys(sessionOptions).forEach(function(key) {
        optionsHtml += `<option value="${key}">${sessionOptions[key]}</option>`;
    });

    Swal.fire({
        title: 'Select Session',
        html: `<select id="session_id" class="form-control">${optionsHtml}</select>`,
        showCancelButton: true,
        confirmButtonText: 'Move'
    }).then((result) => {

        if (!result.isConfirmed) return;

        let session_id = $('#session_id').val();

        if (!session_id) {
            Swal.fire('Error', 'Session is required', 'error');
            return;
        }

        $.ajax({
            url: "{{ route('admin.hard_data.move.enquiries') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ids: Array.from(selectedIds),
                session_id: session_id
            },
            success: function (res) {
                Swal.fire('Success', res.message, 'success');
                location.reload();
            },
            error: function () {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        });

    });

});
$('#exportExcel').click(function () {

    let query = $('#filterForm').serialize();

    window.location.href = "{{ route('admin.hard_data.export') }}?" + query;
});

$('#resetFilters').click(function () {

    // Reset all inputs
    $('#filterForm')[0].reset();

    // If using select2 (optional)
    $('#filterForm select').val('').trigger('change');

    // Reload table
    table.ajax.reload();
});

</script>

@endpush