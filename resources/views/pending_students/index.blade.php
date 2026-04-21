@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2">
    <div class="col-md-3">
        <h3>Pending Students</h3>
    </div>

    <div class="col-md-9 text-end">
        <button id="sendSelected" class="btn btn-primary">
            Send to Session
        </button>
        <button class="btn btn-primary copy-link" 
            data-link="{{ route('student.register.form') }}">
        <i class="fa fa-link"></i> Copy Student Registration Link
    </button>
    </div>
     
    
 
</div>

<table class="table table-bordered" id="studentsTable">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkAll"></th>
            <th>#</th>
            <th>Name</th>
            <th>Father</th>
            <th>College</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Start Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach($students as $student)
        <tr>
            <td>
                
                 @if(!$student->is_sent_to_detail)
                    <input type="checkbox" class="record_checkbox" value="{{ $student->id }}">
                @else
                    <span class="badge bg-success">Sent</span>
                @endif
            </td>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $student->student_name }}</td>
            <td>{{ $student->father_name }}</td>
            <td>{{ $student->collegeData->FullName ?? '-' }}</td>
            <td>{{ $student->contact ?? '-' }}</td>
            <td>{{ $student->email ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($student->start_date)->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>

@endsection

@push('scripts')

<script>

let selectedIds = new Set();

// select row
$(document).on('change', '.record_checkbox', function () {
    let id = $(this).val();

    if ($(this).is(':checked')) {
        selectedIds.add(id);
    } else {
        selectedIds.delete(id);
    }
});

// select all
$('#checkAll').on('change', function () {
    $('.record_checkbox').prop('checked', this.checked).trigger('change');
});

// send
$('#sendSelected').click(function () {

    if (selectedIds.size === 0) {
        Swal.fire('Select students first');
        return;
    }

    let sessionOptions = @json($sessionsList);
    let optionsHtml = '';

    Object.keys(sessionOptions).forEach(function(key) {
        optionsHtml += `<option value="${key}">${sessionOptions[key]}</option>`;
    });

    Swal.fire({
        title: 'Select Sessions',
        html: `<select id="session_ids" class="form-control">${optionsHtml}</select>`,
        confirmButtonText: 'Send',
        showCancelButton: true,
        preConfirm: () => {
            let selected = $('#session_ids').val();
            if (!selected || selected.length === 0) {
                Swal.showValidationMessage('Select at least one session');
            }
            return selected;
        }
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('admin.pending.send') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ids: Array.from(selectedIds),
                session_id: result.value
            },
            success: function (res) {
                Swal.fire('Success', res.message, 'success');
                location.reload();
            }
        });

    });
});

</script>


<script>
$(document).on('click', '.copy-link', function () {

    let link = $(this).data('link');

    navigator.clipboard.writeText(link).then(function () {

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Form link copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });

    }, function () {

        Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Could not copy link'
        });

    });

});
</script>
@endpush