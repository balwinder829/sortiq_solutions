
@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="page_heading">
                {{ $letterFor === 'intern' ? 'Intern Additional Letters' : 'Student Additional Letters' }}
            </h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2 flex-wrap">

                <button type="button" id="bulkDownloadLetters" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download Selected
                </button>

                <button type="button" id="bulkDeleteBtn" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Bulk Delete
                </button>

                <a href="{{ route('student-additional-letters.create', ['letter_for' => $letterFor]) }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Generate Letter
                </a>

            </div>
        </div>
    </div>

    {{-- Student / Intern Tabs --}}
    <ul class="nav nav-tabs mb-3">

        <li class="nav-item">
            <a class="nav-link {{ $letterFor === 'student' ? 'active' : '' }}"
               href="{{ route('student-additional-letters.index', ['letter_for' => 'student']) }}">
                <i class="fas fa-user-graduate"></i>
                Student Letters
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ $letterFor === 'intern' ? 'active' : '' }}"
               href="{{ route('student-additional-letters.index', ['letter_for' => 'intern']) }}">
                <i class="fas fa-user-tie"></i>
                Intern Letters
            </a>
        </li>

    </ul>

    {{-- Filter --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-8">

            <form method="GET" id="filterForm" class="row g-2">

                <input type="hidden" name="letter_for" value="{{ $letterFor }}">

                <div class="col-md-6">

                    <select name="internship_type" class="form-control filterchange">

                        <option value="">All Letter Types</option>

                        @if($letterFor === 'student')

                            <option value="free" {{ request('internship_type') === 'free' ? 'selected' : '' }}>Free Internship Letter</option>
                            <option value="internship" {{ request('internship_type') === 'internship' ? 'selected' : '' }}>Internship Letter</option>
                            <option value="internship_with_package" {{ request('internship_type') === 'internship_with_package' ? 'selected' : '' }}>Internship With Package Letter</option>
                            <option value="stipend" {{ request('internship_type') === 'stipend' ? 'selected' : '' }}>Stipend Internship Letter</option>
                            <option value="offer" {{ request('internship_type') === 'offer' ? 'selected' : '' }}>Offer Letter</option>
                            <option value="custom" {{ request('internship_type') === 'custom' ? 'selected' : '' }}>Custom Type Letter</option>
                            <option value="noc" {{ request('internship_type') === 'noc' ? 'selected' : '' }}>Non-Consent Letter</option>
                            <option value="mutual_consent" {{ request('internship_type') === 'mutual_consent' ? 'selected' : '' }}>Mutual Consent Letter</option>
                            <option value="training_consent" {{ request('internship_type') === 'training_consent' ? 'selected' : '' }}>Training Consent Letter</option>
                            <option value="placement" {{ request('internship_type') === 'placement' ? 'selected' : '' }}>Student Placement Letter</option>
                            <option value="strict_offer_letter" {{ request('internship_type') === 'strict_offer_letter' ? 'selected' : '' }}>Strict Offer Letter</option>
                            <option value="strict_consent_letter" {{ request('internship_type') === 'strict_consent_letter' ? 'selected' : '' }}>Strict Consent Letter</option>
                            <option value="stipend_policy" {{ request('internship_type') === 'stipend_policy' ? 'selected' : '' }}>Stipend Policy Letter</option>
                            <option value="internship_consent" {{ request('internship_type') === 'internship_consent' ? 'selected' : '' }}>Internship Consent Letter</option>
                            <option value="part_time_job_opportunity" {{ request('internship_type') === 'part_time_job_opportunity' ? 'selected' : '' }}>Part Time Job Opportunity Letter</option>
                            <option value="student_responsibility" {{ request('internship_type') === 'student_responsibility' ? 'selected' : '' }}>Student Responsibility Letter</option>
                            <option value="placed_student_responsibility" {{ request('internship_type') === 'placed_student_responsibility' ? 'selected' : '' }}>Placed Student Responsibility Letter</option>
                            <option value="additional_job_opportunity" {{ request('internship_type') === 'additional_job_opportunity' ? 'selected' : '' }}>Additional Job Opportunity Letter</option>

                        @else

                            <option value="intern_confirmation_letter" {{ request('internship_type') === 'intern_confirmation_letter' ? 'selected' : '' }}>Intern Confirmation Letter</option>
                            <option value="intern_internship_letter" {{ request('internship_type') === 'intern_internship_letter' ? 'selected' : '' }}>Intern Internship Letter</option>
                            <option value="offer" {{ request('internship_type') === 'offer' ? 'selected' : '' }}>Offer Letter</option>
                            <option value="strict_offer_letter" {{ request('internship_type') === 'strict_offer_letter' ? 'selected' : '' }}>Strict Offer Letter</option>

                        @endif

                    </select>

                </div>

                <div class="col-md-4 d-flex gap-2">

                    <a href="{{ route('student-additional-letters.index', ['letter_for' => $letterFor]) }}"
                       class="btn btn-secondary">
                        Reset
                    </a>

                </div>

            </form>

        </div>

    </div>

    {{-- Bulk Download Form --}}
    <form id="bulkLettersForm"
          action="{{ route('student-additional-letters.bulk-download') }}"
          method="POST"
          class="d-none">
        @csrf
    </form>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Letters Table --}}
    <div class="table-responsive">

        <table id="lettersTable" class="table table-bordered table-striped">

            <thead>
                <tr>

                    <th class="text-center" style="width:40px;">
                        <input type="checkbox"
                               id="selectAllLetters"
                               aria-label="Select current page">
                    </th>

                    <th style="width:50px;">#</th>
                    <th>Letter Type</th>

                    @if($letterFor === 'student')
                        <th>Student Name</th>
                    @else
                        <th>Intern Name</th>
                    @endif

                    <th>Email</th>
                    <th>Created Date</th>
                    <th>Updated Date</th>
                    <th width="210">Actions</th>

                </tr>
            </thead>

            <tbody>

                @foreach($letters as $letter)

                    @php
                        $person = $letterFor === 'intern'
                            ? $letter->intern
                            : $letter->student;

                        $types = [
                            'free' => 'Free Internship Letter',
                            'stipend' => 'Stipend Internship Letter',
                            'offer' => 'Offer Letter',
                            'custom' => 'Custom Type Letter',
                            'noc' => 'Non-Consent Letter',
                            'mutual_consent' => 'Mutual Consent Letter',
                            'training_consent' => 'Training Consent Letter',
                            'placement' => 'Student Placement Letter',
                            'internship' => 'Internship Letter',
                            'internship_with_package' => 'Internship With Package Letter',
                            'strict_offer_letter' => 'Strict Offer Letter',
                            'strict_consent_letter' => 'Strict Consent Letter',
                            'stipend_policy' => 'Stipend Policy Letter',
                            'internship_consent' => 'Internship Consent Letter',
                            'part_time_job_opportunity' => 'Part Time Job Opportunity Letter',
                            'with_roll_number' => 'With Roll Number Letter',
                            'with_roll_number_internship' => 'With Roll Number Internship Letter',
                            'confirmation_letter' => 'Confirmation Letter',
                            'intern_confirmation_letter' => 'Intern Confirmation Letter',
                            'intern_internship_letter' => 'Intern Internship Letter',
                            'internship_confirmation_letter' => 'Internship Confirmation Letter',
                            'student_responsibility' => 'Student Responsibility Letter',
                            'placed_student_responsibility' => 'Placed Student Responsibility Letter',
                        ];
                    @endphp

                    <tr>

                        <td class="text-center">
                            <input type="checkbox"
                                   class="letter-row-checkbox"
                                   value="{{ $letter->id }}"
                                   aria-label="Select letter {{ $letter->id }}">
                        </td>

                        <td></td>

                        <td>
                            <span class="badge bg-success">
                                {{ $types[$letter->internship_type] ?? ucfirst(str_replace('_', ' ', $letter->internship_type)) }}
                            </span>
                        </td>

                        <td>{{ $person->student_name ?? 'N/A' }}</td>

                        <td>{{ $person->email_id ?? 'N/A' }}</td>

                        <td>{{ optional($letter->created_at)->format('d M Y') }}</td>

                        <td>{{ optional($letter->updated_at)->format('d M Y') }}</td>

                        <td>

                            {{-- Existing actions --}}
                            <a href="{{ route('student-additional-letters.edit', $letter) }}"
                               class="btn btn-sm"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="{{ route('student-additional-letters.preview', $letter) }}"
                               target="_blank"
                               class="btn btn-sm"
                               title="Preview">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('student-additional-letters.pdf', $letter) }}"
                               class="btn btn-sm"
                               title="Download">
                                <i class="fas fa-download"></i>
                            </a>

                            

                            
<form action="{{ route('student-additional-letters.destroy', $letter->id) }}"
      method="POST"
      class="delete-letter-form"
      style="display:inline-block;">
    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-sm"
            title="Delete">
        <i class="fa fa-trash"></i>
    </button>
</form>


                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

<form id="bulkDeleteForm"
      action="{{ route('student-additional-letters.bulk-delete') }}"
      method="POST"
      style="display:none;">
    @csrf
    <div id="bulkDeleteInputs"></div>
</form>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {

    // Selected IDs remain preserved across pagination, search and redraw.
    var selectedLetterIds = {};

    var table = $('#lettersTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        ordering: false,
        columnDefs: [
            {
                targets: [0, 1],
                searchable: false,
                orderable: false
            }
        ]
    });

    function updateSelectAllState() {

        var currentPageCheckboxes = table.rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.letter-row-checkbox');

        var totalOnPage = currentPageCheckboxes.length;

        var checkedOnPage = currentPageCheckboxes.filter(function() {
            return !!selectedLetterIds[String($(this).val())];
        }).length;

        $('#selectAllLetters')
            .prop('checked', totalOnPage > 0 && checkedOnPage === totalOnPage)
            .prop('indeterminate', checkedOnPage > 0 && checkedOnPage < totalOnPage);
    }

    function restoreSelectedCheckboxes() {

        $('.letter-row-checkbox').each(function() {

            var id = String($(this).val());

            $(this).prop('checked', !!selectedLetterIds[id]);

        });

        updateSelectAllState();
    }

    // Individual checkbox selection.
    $('#lettersTable').on('change', '.letter-row-checkbox', function() {

        var id = String($(this).val());

        if (this.checked) {
            selectedLetterIds[id] = true;
        } else {
            delete selectedLetterIds[id];
        }

        updateSelectAllState();
    });

    // Select / deselect current DataTable page only.
    $('#selectAllLetters').on('change', function() {

        var checked = this.checked;

        table.rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.letter-row-checkbox')
            .each(function() {

                var id = String($(this).val());

                $(this).prop('checked', checked);

                if (checked) {
                    selectedLetterIds[id] = true;
                } else {
                    delete selectedLetterIds[id];
                }

            });

        updateSelectAllState();
    });

    // Restore selection after pagination, search or redraw.
    table.on('draw.dt', function() {
        restoreSelectedCheckboxes();
    });

    // Bulk Download.
    $('#bulkDownloadLetters').on('click', function() {

        var ids = Object.keys(selectedLetterIds);

        if (ids.length === 0) {

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No letters selected',
                    text: 'Please select at least 1 letter.'
                });
            } else {
                alert('Please select at least 1 letter.');
            }

            return;
        }

        var downloadMessage = ids.length === 1
            ? '1 letter will be downloaded as a PDF.'
            : ids.length + ' letters will be downloaded together in one ZIP file.';

        var submitDownload = function() {

            var form = $('#bulkLettersForm');

            form.find('input[name="ids[]"]').remove();

            ids.forEach(function(id) {

                $('<input>', {
                    type: 'hidden',
                    name: 'ids[]',
                    value: id
                }).appendTo(form);

            });

            form.trigger('submit');
        };

        if (typeof Swal !== 'undefined') {

            Swal.fire({
                icon: 'question',
                title: 'Download selected letters?',
                text: downloadMessage,
                showCancelButton: true,
                confirmButtonText: 'Download',
                cancelButtonText: 'Cancel',
                reverseButtons: false
            }).then(function(result) {

                if (result.isConfirmed) {
                    submitDownload();
                }

            });

        } else if (confirm(downloadMessage)) {

            submitDownload();

        }

    });

    // Serial numbers.
    table.on('draw.dt', function() {

        var pageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function(cell, i) {
            cell.innerHTML = pageInfo.start + i + 1;
        });

    }).draw();

    let timer;

    $('.filterchange').on('change', function() {
        $('#filterForm').submit();
    });

    $('.filterchangetext').on('input', function() {

        clearTimeout(timer);

        timer = setTimeout(function() {
            $('#filterForm').submit();
        }, 500);

    });

 // Bulk Delete.
$('#bulkDeleteBtn').on('click', function () {

    // Use the existing selected IDs.
    var selectedIds = Object.keys(selectedLetterIds);

    if (selectedIds.length === 0) {

        Swal.fire({
            icon: 'warning',
            title: 'No letters selected',
            text: 'Please select at least 1 letter.',
            confirmButtonText: 'OK',
            reverseButtons: false
        });

        return;
    }

    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: 'You are about to delete ' + selectedIds.length + ' selected letter(s). This action cannot be undone.',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete them!',
        cancelButtonText: 'Cancel',
        reverseButtons: false
    }).then(function (result) {

        if (result.isConfirmed) {

            var form = $('#bulkDeleteForm');

            form.find('input[name="ids[]"]').remove();

            selectedIds.forEach(function (id) {

                $('<input>', {
                    type: 'hidden',
                    name: 'ids[]',
                    value: id
                }).appendTo(form);

            });

            form.trigger('submit');
        }

    });

});

    $(document).on('submit', '.delete-letter-form', function(e) {

    e.preventDefault();

    var form = this;

    if (typeof Swal !== 'undefined') {

        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to delete this letter?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function(result) {

            if (result.isConfirmed) {
                form.submit();
            }

        });

    } else if (confirm('Do you want to delete this letter?')) {

        form.submit();

    }

});

});




</script>

@endpush
