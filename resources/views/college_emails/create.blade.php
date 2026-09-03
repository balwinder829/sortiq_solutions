@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-3">Send Email</h3>

    <form method="POST" action="{{ route('admin.college-emails.store') }}" id="emailForm">
        @csrf

        {{-- TOP FIELDS --}}
        <div class="row mb-3">

            <div class="col-md-4">
                <label>Purpose(Email Template)</label>
                <select name="purpose_id" class="form-control" required>
                    <option value="">Select</option>
                    @foreach($purposes as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Sender Email ID</label>
                <select name="sender_id" class="form-control" required>
                    <option value="">Select</option>
                    @foreach($senders as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control" required>
            </div>

        </div>

        <!-- <div class="mb-3">
            <label>Body</label>
            <textarea name="body" class="form-control" rows="4"></textarea>
        </div> -->

        {{-- COLLEGE TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="checkAllRows">
                        </th>
                        <th>College</th>
                        <th>HOD</th>
                        <th>TPO</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($colleges as $college)

                        @php
                            $hod = $college->hod;

                            $hodEmail = $hod?->firstHodEmail;
                            $tpoEmail = $hod?->firstTpoEmail;
                        @endphp

                        <tr>

                            {{-- SELECT COLLEGE --}}
                            <td>
                                <input type="checkbox" class="row_checkbox" value="{{ $college->id }}">
                            </td>

                            <td>
                                {{ $college->full_name }}
                            </td>

                            {{-- HOD --}}
                            <td>
                                @if($hodEmail)
                                    <input type="checkbox"
                                           name="types[{{ $college->id }}][]"
                                           value="hod"
                                           class="type_checkbox hod_checkbox"
                                           data-row="{{ $college->id }}">
                                @else
                                    <input type="checkbox" disabled>
                                    <span class="text-danger small">No Email</span>
                                @endif
                            </td>

                            {{-- TPO --}}
                            <td>
                                @if($tpoEmail)
                                    <input type="checkbox"
                                           name="types[{{ $college->id }}][]"
                                           value="tpo"
                                           class="type_checkbox tpo_checkbox"
                                           data-row="{{ $college->id }}">
                                @else
                                    <input type="checkbox" disabled>
                                    <span class="text-danger small">No Email</span>
                                @endif
                            </td>

                            {{-- EDIT --}}
                            <td>

                                 {{-- PREVIEW BUTTON --}}
    <button type="button"
            class="btn btn-sm btn-info college-preview-btn"
            data-bs-toggle="modal"
            data-bs-target="#collegePreviewModal"
            data-college="{{ $college->full_name }}"
            data-hod-email="{{ $hodEmail?->email ?? '' }}"
            data-tpo-email="{{ $tpoEmail?->email ?? '' }}">
        Preview
    </button>

    @if($hod)

        <a href="{{ route('hods.edit', $hod->id) }}" target="_blank" 
           class="btn btn-sm btn-secondary">
            Edit Email
        </a>

    @else

        <a href="{{ route('hods.create', ['college_id' => $college->id]) }}"
           class="btn btn-sm btn-success" target="_blank" >
            Add Email
        </a>

    @endif

</td>

                        </tr>

                    @endforeach

                </tbody>

            </table>
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-success">
                Send Emails
            </button>
        </div>

    </form>
    
{{-- COLLEGE PREVIEW MODAL --}}
<div class="modal fade"
     id="collegePreviewModal"
     tabindex="-1"
     aria-labelledby="collegePreviewModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"
                    id="collegePreviewModalLabel">
                    College Email Details
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <div class="modal-body">

                {{-- COLLEGE --}}
                <div class="mb-3">

                    <label class="fw-bold">
                        College
                    </label>

                    <div id="previewCollegeName"
                         class="mt-1">
                    </div>

                </div>


                {{-- HOD --}}
                <div class="mb-3">

                    <label class="fw-bold">
                        HOD Email
                    </label>

                    <div id="previewHodEmail"
                         class="mt-1">
                    </div>

                </div>


                {{-- TPO --}}
                <div class="mb-3">

                    <label class="fw-bold">
                        TPO Email
                    </label>

                    <div id="previewTpoEmail"
                         class="mt-1">
                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

</div>

@endsection


@push('scripts')

<script>

// SELECT ALL ROWS
$('#checkAllRows').on('change', function () {

    let checked = this.checked;

    $('.row_checkbox').prop('checked', checked);

    $('.type_checkbox').each(function () {
        if (!this.disabled) {
            $(this).prop('checked', checked);
        }
    });

});


// WHEN ROW CHECKBOX CHANGES
$(document).on('change', '.row_checkbox', function () {

    let rowId = $(this).val();
    let checked = $(this).is(':checked');

    $('input[data-row="'+rowId+'"]').each(function () {
        if (!this.disabled) {
            $(this).prop('checked', checked);
        }
    });

});


// VALIDATION BEFORE SUBMIT
$('#emailForm').submit(function (e) {

    let valid = false;

    $('.type_checkbox:checked').each(function () {
        valid = true;
    });

    if (!valid) {

        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one HOD or TPO'
        });

        return false;
    }

    Swal.fire({
        title: 'Send Emails?',
        text: 'Are you sure you want to send emails?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) {
            e.preventDefault();
        }

    });

});

// COLLEGE PREVIEW
$(document).on('click', '.college-preview-btn', function () {

    let collegeName = $(this).attr('data-college');
    let hodEmail = $(this).attr('data-hod-email');
    let tpoEmail = $(this).attr('data-tpo-email');


    // College Name
    $('#previewCollegeName').text(collegeName);


    // HOD Email
    if (hodEmail) {

        $('#previewHodEmail')
            .text(hodEmail)
            .removeClass('text-danger')
            .addClass('text-success');

    } else {

        $('#previewHodEmail')
            .text('No mail added')
            .removeClass('text-success')
            .addClass('text-danger');

    }


    // TPO Email
    if (tpoEmail) {

        $('#previewTpoEmail')
            .text(tpoEmail)
            .removeClass('text-danger')
            .addClass('text-success');

    } else {

        $('#previewTpoEmail')
            .text('No mail added')
            .removeClass('text-success')
            .addClass('text-danger');

    }

});
</script>


@endpush