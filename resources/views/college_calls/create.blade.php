@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-3">Log Calls</h3>

    <form method="POST" action="{{ route('admin.college-calls.store') }}" id="callForm">
        @csrf

        <div class="row mb-3">

            <div class="col-md-6">
                <label>Purpose</label>
                <input type="text" name="purpose" class="form-control">
            </div>

            <div class="col-md-6">
                <label>Notes</label>
                <input type="text" name="notes" class="form-control">
            </div>

        </div>

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
            <th>Action</th> {{-- ✅ NEW --}}
        </tr>
    </thead>

    <tbody>

        @foreach($colleges as $college)

            @php $hod = $college->hod; @endphp

            <tr>

                {{-- SELECT --}}
                <td>
                    <input type="checkbox" class="row_checkbox" value="{{ $college->id }}">
                </td>

                {{-- COLLEGE --}}
                <td>
                    {{ $college->full_name }}
                </td>

                {{-- HOD --}}
                <td>
                    @if($hod && $hod->hod_contact)
                        <input type="checkbox"
                               name="types[{{ $college->id }}][]"
                               value="hod"
                               class="type_checkbox"
                               data-row="{{ $college->id }}">
                        <br>
                        <small>{{ $hod->hod_contact }}</small>
                    @else
                        <input type="checkbox" disabled>
                        <br>
                        <span class="text-danger small">No Number</span>
                    @endif
                </td>

                {{-- TPO --}}
                <td>
                    @if($hod && $hod->tpo_contact)
                        <input type="checkbox"
                               name="types[{{ $college->id }}][]"
                               value="tpo"
                               class="type_checkbox"
                               data-row="{{ $college->id }}">
                        <br>
                        <small>{{ $hod->tpo_contact }}</small>
                    @else
                        <input type="checkbox" disabled>
                        <br>
                        <span class="text-danger small">No Number</span>
                    @endif
                </td>

                {{-- ACTION --}}
                <td>

                    @if($hod)

                        <a href="{{ route('hods.edit', $hod->id) }}"
                           target="_blank"
                           class="btn btn-sm btn-secondary">
                            Edit Contact
                        </a>

                    @else

                        <a href="{{ route('hods.create', ['college_id' => $college->id]) }}"
                           target="_blank"
                           class="btn btn-sm btn-success">
                            Add Contact
                        </a>

                    @endif

                </td>

            </tr>

        @endforeach

    </tbody>

</table>
 
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">Save Calls</button>
        </div>

    </form>

</div>

@endsection

@push('scripts')

<script>

$('#checkAllRows').on('change', function () {

    let checked = this.checked;

    $('.row_checkbox').prop('checked', checked);

    $('.type_checkbox').each(function () {
        $(this).prop('checked', checked);
    });
});


$(document).on('change', '.row_checkbox', function () {

    let rowId = $(this).val();
    let checked = $(this).is(':checked');

    $('input[data-row="'+rowId+'"]').prop('checked', checked);
});


$('#callForm').submit(function (e) {

    e.preventDefault(); // STOP default submit immediately

    let valid = false;

    $('.type_checkbox:checked').each(function () {
        valid = true;
    });

    if (!valid) {

        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one HOD or TPO'
        });

        return;
    }

    Swal.fire({
        title: 'Add Call Record?',
        text: 'Are you sure?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (result.isConfirmed) {
            $('#callForm')[0].submit(); // manually submit
        }

    });

});

 
</script>

@endpush