@extends('layouts.app')

@section('content')

<div class="container">

    {{-- ================================
        PAGE HEADER
    ================================= --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading">
                Shift / Merge College
            </h1>

            <p class="text-muted mb-0">
                Move all dependent records from a duplicate college to the correct college.
            </p>

        </div>


        {{-- ACTION BUTTON --}}
        <div class="col-md-6">

            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('colleges.index') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df; color:#fff;">

                    Back to Colleges

                </a>

            </div>

        </div>

    </div>


    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST"
          action="{{ route('colleges.processShift') }}"
          id="shiftForm">

        @csrf


        {{-- SOURCE COLLEGE --}}
<div class="form-group">

    <label>
        <strong>Source / Duplicate College</strong>
    </label>

    <select
        name="source_college_id"
        id="source_college_id"
        class="form-control select2-college"
        required>

        <option value="">Search duplicate/source college...</option>

        @foreach($colleges as $college)

            <option
                value="{{ $college->id }}"
                {{ old('source_college_id') == $college->id ? 'selected' : '' }}>

                {{ $college->college_name }} — ID: {{ $college->id }}

            </option>

        @endforeach

    </select>

    <small class="text-muted">
        Search by college name or ID.
    </small>

</div>


       {{-- TARGET COLLEGE --}}
<div class="form-group">

    <label>
        <strong>Target / Correct College</strong>
    </label>

    <select
        name="target_college_id"
        id="target_college_id"
        class="form-control select2-college"
        required>

        <option value="">Search correct/target college...</option>

        @foreach($colleges as $college)

            <option
                value="{{ $college->id }}"
                {{ old('target_college_id') == $college->id ? 'selected' : '' }}>

                {{ $college->college_name }} — ID: {{ $college->id }}

            </option>

        @endforeach

    </select>

    <small class="text-muted">
        Search by college name or ID.
    </small>

</div>


        {{-- RECORD COUNTS --}}
        <div
            class="card mb-4"
            id="recordsCard"
            style="display:none;">

            <div class="card-header">
                <strong>3. Records That Will Be Shifted</strong>
            </div>

            <div class="card-body">

                <div id="collegeSummary"
                     class="mb-3">
                </div>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead>
                            <tr>
                                <th>Table / Module</th>
                                <th width="150">Records</th>
                            </tr>
                        </thead>

                        <tbody id="recordsBody">
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th id="totalRecords">0</th>
                            </tr>
                        </tfoot>

                    </table>

                </div>

            </div>

        </div>


        {{-- VERIFICATION --}}
        <div
            class="card mb-4"
            id="verificationCard"
            style="display:none;">

            <div class="card-header">
                <strong>4. Verification</strong>
            </div>

            <div class="card-body">

                <div class="alert alert-warning">

                    <strong>Please verify carefully before continuing.</strong>

                    <ul class="mb-0 mt-2">

                        <li>
                            Source and target college are correct.
                        </li>

                        <li>
                            The records displayed above are the records that will be shifted.
                        </li>

                        <li>
                            The target college record itself will remain unchanged.
                        </li>

                        <li>
                            The source college record will NOT be deleted.
                        </li>

                    </ul>

                </div>


                <div class="form-check">

                    <input
                        type="checkbox"
                        name="verified"
                        value="1"
                        id="verified"
                        class="form-check-input"
                        {{ old('verified') ? 'checked' : '' }}>

                    <label
                        for="verified"
                        class="form-check-label">

                        <strong>
                            I have personally verified the source college,
                            target college and the records shown above.
                        </strong>

                    </label>

                </div>

            </div>

        </div>


        {{-- SUBMIT --}}
        <div
            id="submitSection"
            style="display:none;">

            <button
                type="submit"
                id="shiftButton"
                class="btn btn-danger btn-lg">

                Shift / Merge College

            </button>

            <a
                href="{{ route('colleges.index') }}"
                class="btn btn-secondary btn-lg">

                Cancel

            </a>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    // if ($.fn.select2) {

    //     $('#source_college_id').select2({
    //         width: '100%',
    //         placeholder: 'Search college name',
    //         allowClear: true
    //     });

    //     $('#target_college_id').select2({
    //         width: '100%',
    //         placeholder: 'Search college name',
    //         allowClear: true
    //     });

    // }

    const source = document.getElementById('source_college_id');
    const target = document.getElementById('target_college_id');

    const recordsCard = document.getElementById('recordsCard');
    const verificationCard = document.getElementById('verificationCard');
    const submitSection = document.getElementById('submitSection');

    const recordsBody = document.getElementById('recordsBody');
    const totalRecords = document.getElementById('totalRecords');
    const collegeSummary = document.getElementById('collegeSummary');

    const colleges = @json($colleges);


    function loadSourceData() {

        const sourceId = source.value;
        const targetId = target.value;


        if (!sourceId) {

            recordsCard.style.display = 'none';
            verificationCard.style.display = 'none';
            submitSection.style.display = 'none';

            return;
        }


        // Source and target cannot be same
        if (sourceId === targetId && targetId !== '') {

            Swal.fire({
                icon: 'warning',
                title: 'Invalid Selection',
                text: 'Source and target college cannot be the same.',
                confirmButtonText: 'OK'
            });

            target.value = '';

            verificationCard.style.display = 'none';
            submitSection.style.display = 'none';

            return;
        }


        fetch(
            '{{ route('colleges.shift') }}?source_college_id='
            + encodeURIComponent(sourceId),
            {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        )
        .then(response => {

            if (!response.ok) {
                throw new Error('Request failed');
            }

            return response.json();
        })
        .then(data => {

            if (!data.success) {

                Swal.fire({
                    icon: 'error',
                    title: 'Unable to Load Records',
                    text: 'College records could not be loaded.',
                    confirmButtonText: 'OK'
                });

                return;
            }


            recordsBody.innerHTML = '';

            let total = 0;


            data.counts.forEach(function (record) {

                total += Number(record.count);

                recordsBody.innerHTML += `
                    <tr>
                        <td>${record.name}</td>
                        <td>
                            <strong>${record.count}</strong>
                        </td>
                    </tr>
                `;
            });


            totalRecords.textContent = total;


            const sourceCollege = colleges.find(
                c => String(c.id) === String(sourceId)
            );

            const targetCollege = colleges.find(
                c => String(c.id) === String(targetId)
            );


            collegeSummary.innerHTML = `
                <div class="row">

                    <div class="col-md-6">

                        <div class="alert alert-danger mb-0">

                            <strong>Source / Duplicate</strong><br>

                            ${sourceCollege
                                ? sourceCollege.college_name
                                : ''
                            }

                            <br>

                            <small>
                                ID: ${sourceId}
                            </small>

                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="alert alert-success mb-0">

                            <strong>Target / Correct</strong><br>

                            ${targetCollege
                                ? targetCollege.college_name
                                : 'Select target college'
                            }

                            <br>

                            <small>
                                ID: ${targetId || '-'}
                            </small>

                        </div>

                    </div>

                </div>
            `;


            recordsCard.style.display = 'block';


            if (targetId && sourceId !== targetId) {

                verificationCard.style.display = 'block';
                submitSection.style.display = 'block';

            } else {

                verificationCard.style.display = 'none';
                submitSection.style.display = 'none';

            }

        })
        .catch(function (error) {

            console.error(error);

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Unable to load college records. Please refresh the page and try again.',
                confirmButtonText: 'OK'
            });

        });

    }


    // Source change
    source.addEventListener('change', loadSourceData);


    // Target change
    target.addEventListener('change', loadSourceData);


    // Submit
    document.getElementById('shiftForm')
        .addEventListener('submit', function (e) {

            e.preventDefault();


            const form = this;

            const sourceId = source.value;
            const targetId = target.value;
            const verified = document.getElementById('verified').checked;


            // Source missing
            if (!sourceId) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Source College Required',
                    text: 'Please select the duplicate/source college.',
                    confirmButtonText: 'OK'
                });

                return;
            }


            // Target missing
            if (!targetId) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Target College Required',
                    text: 'Please select the correct/target college.',
                    confirmButtonText: 'OK'
                });

                return;
            }


            // Same college
            if (sourceId === targetId) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Selection',
                    text: 'Source and target college cannot be the same.',
                    confirmButtonText: 'OK'
                });

                return;
            }


            // Verification checkbox
            if (!verified) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Verification Required',
                    text: 'Please verify the source college, target college and records before shifting.',
                    confirmButtonText: 'OK'
                });

                return;
            }


            const sourceCollege = colleges.find(
                c => String(c.id) === String(sourceId)
            );

            const targetCollege = colleges.find(
                c => String(c.id) === String(targetId)
            );


            // Final confirmation
            Swal.fire({

                
    icon: 'warning',

    title: 'Confirm College Shift',

    width: '500px',

    padding: '1.25rem',

    html: `
        <div style="text-align:left; font-size:15px; line-height:1.4;">

            <p style="margin-bottom:12px;">
                You are about to shift all dependent records.
            </p>

            <p style="margin-bottom:10px;">
                <strong>Source:</strong><br>
                ${sourceCollege.college_name}
                <small>(ID: ${sourceId})</small>
            </p>

            <p style="margin-bottom:12px;">
                <strong>Target:</strong><br>
                ${targetCollege.college_name}
                <small>(ID: ${targetId})</small>
            </p>

            <div class="alert alert-warning"
                 style="margin-bottom:0; padding:10px 12px;">

                <strong>Important:</strong><br>

                The source college record itself will NOT be deleted.
                Only its dependent records will be reassigned.

            </div>

        </div>
    `,

    showCancelButton: true,

    confirmButtonText: 'Yes, Shift Records',

    cancelButtonText: 'Cancel',

    confirmButtonColor: '#d33',

    cancelButtonColor: '#6c757d',

    reverseButtons: false

            }).then(function (result) {

                if (!result.isConfirmed) {
                    return;
                }


                const button =
                    document.getElementById('shiftButton');


                button.disabled = true;

                button.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Shifting Records...';


                // Submit actual form
                form.submit();

            });

        });

});

</script>

@endsection