@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-8">
        <h1 class="page_heading">Hard Data</h1>
    </div>

    <div class="col-md-4 text-end">
        <button id="moveToEnquiry" class="btn btn-warning mb-3">
            Move to Enquiries
        </button>

        <a href="{{ route('admin.hard_data.create') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Add
        </a>
    </div>

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
    ajax: "{{ route('admin.hard_data.index') }}",

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

</script>

@endpush