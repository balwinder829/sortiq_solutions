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


</script>


@endpush