@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-4">
        <h1 class="page_heading">Manual Data</h1>
    </div>

    <div class="col-md-8 text-end">
        @include('partials.whatsapp-popover', [
                'add_margin' => 'mb-3'
            ])
        <button id="moveToEnquiry" class="btn btn-warning mb-3">
            Move to Enquiries
        </button>

        <a href="{{ route('admin.manual_data.create') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Add
        </a>
        <a href="{{ route('admin.manual_data.import.form') }}"
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
let selectedRecordIds = new Set();

// ✅ DATATABLE
var table = $('#trainers-table').DataTable({
    processing: true,
    serverSide: true,
    // ajax: "{{ route('admin.manual_data.index') }}",
    ajax: {
    url: "{{ route('admin.manual_data.index') }}",
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

                return `<input type="checkbox" id="${row.row_id}" class="record_checkbox" value="${data}">`;
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
    let record_id = $(this).attr('id');
    // console.log('Checkbox changed:', { id, record_id, checked: $(this).is(':checked') });
    if ($(this).is(':checked')) {
        selectedIds.add(id);
        selectedRecordIds.add(record_id);
    } else {
        selectedIds.delete(id);
        selectedRecordIds.delete(record_id);
    }
});


// ✅ SELECT ALL
$('#checkAll').on('change', function () {
    let checked = this.checked;

    $('.record_checkbox').each(function () {
        let id = $(this).val();
        let record_id = $(this).attr('id');
        if (checked) {
            selectedIds.add(id);
            selectedRecordIds.add(record_id);
        } else {
            selectedIds.delete(id);
            selectedRecordIds.delete(record_id);
        }

        $(this).prop('checked', checked);
    });
});
$('#exportExcel').click(function () {

    let query = $('#filterForm').serialize();

    window.location.href = "{{ route('admin.manual_data.export') }}?" + query;
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
            url: "{{ route('admin.manual_data.move.enquiries') }}",
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

$('#resetFilters').click(function () {

    // Reset all inputs
    $('#filterForm')[0].reset();

    // If using select2 (optional)
    $('#filterForm select').val('').trigger('change');

    // Reload table
    table.ajax.reload();
});

new bootstrap.Popover(document.getElementById('whatsappBtn'), {
    html: true,
    sanitize: false,
    customClass: 'whatsapp-popover',
    content: function () {
        return $('#whatsappPopoverContent').html();
    }
});

$(document).on('click', '#sendWhatsappNotification', function () {
    
    let popover = $(this).closest('.popover');
    let custom_message = popover.find('textarea[name="customMessage"]').val();
    let append_name = $('input[name="append_name"]:checked').val();

    if (selectedRecordIds.size === 0) {
        Swal.fire('No selection', 'Select at least one student', 'warning');
        return;
    }
    
    if (custom_message?.length > 0) {
        custom_message = custom_message;
    } else {
        Swal.fire('Message Required', 'Please enter a custom message', 'warning');
        return;
    }

    let formData = new FormData();
    formData.append('append_name',append_name);
    formData.append('message',custom_message);
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('message_type', append_name ? 'with_name' : 'same_message');
    Array.from(selectedRecordIds).forEach(function(id) {
        formData.append('ids[]', id);
    });
    formData.append('existing_file_path', popover.find('#existingFile').val());
    let fileInput = popover.find('input[type="file"]')[0];
    // alert(fileInput.files.length);
    if (fileInput && fileInput.files.length > 0) {
        formData.append('whatsappFile', fileInput.files[0]);
        console.log(fileInput.files[0].name);
    }

    $.ajax({
        url: "{{ route('admin.message.send_whatsapp') }}",
        type: "POST",
        processData: false,
        contentType: false,
        data: formData,
        beforeSend: function () {
            popover.find('#message_loader').show();
            popover.find('#sendWhatsappNotification').prop('disabled', true);
        },
        success: function (res) {
            if(res.status === 'error' || res.status === false) {
                Swal.fire('Error', res.message, 'error');
                return;
            }
            Swal.fire('Success', res.message, 'success');
        },
        error: function () {
            Swal.fire('Error', 'Something went wrong', 'error');
        },
        complete: function () {
            popover.find('#message_loader').hide();
            popover.find('#sendWhatsappNotification').prop('disabled', false);
        }
    });
});

</script>
@endpush