@extends('layouts.app')

@section('content')

<style>
    .joining-action-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .joining-action-buttons .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .joining-action-buttons form {
        margin: 0;
    }
</style>

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-4">
            <h1 class="page_heading">Joining Students</h1>
        </div>

        <div class="col-md-8">
            <div class="d-flex justify-content-end gap-2 flex-wrap">

                <a
                    href="{{ route('admin.joining_students.export', ['status' => $status]) }}"
                    class="btn mb-3"
                    style="background-color:#6b51df; color:#fff;"
                >
                    <i class="fa fa-download"></i>
                    Export Excel
                </a>

                <button
                    type="button"
                    id="sendSelected"
                    class="btn mb-3"
                    style="background-color:#6b51df; color:#fff;"
                >
                    Send to Session
                </button>

                <button
                    type="button"
                    class="btn mb-3 copy-link"
                    style="background-color:#6b51df; color:#fff;"
                    data-link="{{ route('joining_student.front') }}"
                >
                    <i class="fa fa-link"></i>
                    Copy Joining Student Link
                </button>

                <button
                    type="button"
                    id="deleteSelectedBtn"
                    class="btn btn-danger mb-3"
                    disabled
                >
                    <i class="fas fa-trash"></i>
                    Delete Selected
                </button>

            </div>
        </div>

    </div>

    {{-- PAYMENT STATUS TABS --}}
    <ul class="nav nav-tabs mb-4">

        <li class="nav-item">
            <a
                class="nav-link {{ $status === 'all' ? 'active' : '' }}"
                href="{{ route('admin.joining_students.index', ['status' => 'all']) }}"
            >
                All
                <span class="badge bg-secondary ms-1">
                    {{ $paymentCounts['all'] }}
                </span>
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link {{ $status === 'awaiting' ? 'active' : '' }}"
                href="{{ route('admin.joining_students.index', ['status' => 'awaiting']) }}"
            >
                Awaiting Verification
                <span class="badge bg-warning text-dark ms-1">
                    {{ $paymentCounts['awaiting'] }}
                </span>
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link {{ $status === 'verified' ? 'active' : '' }}"
                href="{{ route('admin.joining_students.index', ['status' => 'verified']) }}"
            >
                Verified
                <span class="badge bg-success ms-1">
                    {{ $paymentCounts['verified'] }}
                </span>
            </a>
        </li>

        <li class="nav-item">
            <a
                class="nav-link {{ $status === 'rejected' ? 'active' : '' }}"
                href="{{ route('admin.joining_students.index', ['status' => 'rejected']) }}"
            >
                Rejected
                <span class="badge bg-danger ms-1">
                    {{ $paymentCounts['rejected'] }}
                </span>
            </a>
        </li>

    </ul>

    {{-- TABLE --}}
    <div class="table-responsive">

        <table
            class="table table-bordered table-striped align-middle"
            id="joiningStudentsTable"
            width="100%"
        >
            <thead>
                <tr>
                    <th>
                        <input
                            type="checkbox"
                            id="checkAll"
                            title="Select all on this page"
                        >
                    </th>
                    <th>#</th>
                    <th>Student</th>
                    <th>Father</th>
                    <th>College</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>UPI / QR</th>
                    <th>Transaction ID</th>
                    <th>Payment Status</th>
                    <th>Date of Joining</th>
                    <th>Sent Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($students as $student)

                    @php
                        $paymentStatus = $student->payment_status ?? 'pending';

                        $paymentStatusLabels = [
                            'pending' => 'Pending',
                            'submitted' => 'Submitted',
                            'verified' => 'Verified',
                            'rejected' => 'Rejected',
                            'refunded' => 'Refunded',
                        ];

                        $paymentStatusClasses = [
                            'pending' => 'bg-secondary',
                            'submitted' => 'bg-warning text-dark',
                            'verified' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'refunded' => 'bg-info text-dark',
                        ];

                        $paymentStatusLabel =
                            $paymentStatusLabels[$paymentStatus] ?? ucfirst($paymentStatus);

                        $paymentStatusClass =
                            $paymentStatusClasses[$paymentStatus] ?? 'bg-secondary';
                    @endphp

                    <tr>
                        <td>
                            @if(!$student->is_sent_to_detail)
                                <input
                                    type="checkbox"
                                    class="record_checkbox"
                                    value="{{ $student->id }}"
                                    aria-label="Select {{ $student->student_name }}"
                                >
                            @else
                                <span class="badge bg-success">Sent</span>
                            @endif
                        </td>

                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->father_name ?? '-' }}</td>
                        <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                        <td>{{ $student->contact ?? '-' }}</td>
                        <td>{{ $student->email ?? '-' }}</td>

                        <td>
                            @if($student->payment_amount !== null)
                                ₹{{ number_format((float) $student->payment_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>

                        <td>{{ $student->paymentUpiAccount->name ?? '-' }}</td>
                        <td>{{ $student->payment_transaction_id ?? '-' }}</td>

                        <td>
                            <span class="badge {{ $paymentStatusClass }}">
                                {{ $paymentStatusLabel }}
                            </span>
                        </td>

                        <td>
                            @if($student->date_of_joining)
                                {{ \Carbon\Carbon::parse($student->date_of_joining)->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($student->is_sent_to_detail)
                                <span class="badge bg-success">Sent</span>
                            @else
                                <span class="badge bg-secondary">Not Sent</span>
                            @endif
                        </td>

                        <td>
                            <div class="joining-action-buttons">

                                <a
                                    href="{{ route('admin.joining_students.show', $student->id) }}"
                                    class="btn btn-sm "
                                    title="View Details"
                                    aria-label="View Details"
                                >
                                    <i class="fa fa-eye"></i>
                                </a>

                                <form
                                    action="{{ route('admin.joining_students.destroy', $student->id) }}"
                                    method="POST"
                                    class="delete-joining-form"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="button"
                                        class="btn btn-sm delete-joining-btn"
                                        title="Delete"
                                        aria-label="Delete"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    let selectedIds = new Set();

    let table = $('#joiningStudentsTable').DataTable({
        pageLength: 50,
        order: [],
        responsive: false,
        autoWidth: false,
        scrollX: true,
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]
    });

    $('#checkAll').on('click', function (e) {
        e.stopPropagation();
    });

    function updateCheckAllState() {
        let $checkboxes = table
            .rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.record_checkbox');

        let total = $checkboxes.length;
        let checked = $checkboxes.filter(':checked').length;
        let checkAll = document.getElementById('checkAll');

        if (!checkAll) return;

        checkAll.checked = total > 0 && checked === total;
        checkAll.indeterminate = checked > 0 && checked < total;
    }

    function updateDeleteSelectedButton() {
        $('#deleteSelectedBtn').prop('disabled', selectedIds.size === 0);
    }

    function syncCheckboxes() {
        table
            .rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.record_checkbox')
            .each(function () {
                let id = String($(this).val());
                $(this).prop('checked', selectedIds.has(id));
            });

        updateCheckAllState();
        updateDeleteSelectedButton();
    }

    $(document).on('change', '.record_checkbox', function () {
        let id = String($(this).val());

        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        updateCheckAllState();
        updateDeleteSelectedButton();
    });

    $('#checkAll').on('change', function () {
        let shouldCheck = this.checked;

        table
            .rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.record_checkbox')
            .each(function () {
                let id = String($(this).val());

                $(this).prop('checked', shouldCheck);

                if (shouldCheck) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
            });

        updateCheckAllState();
        updateDeleteSelectedButton();
    });

    table.on('draw', syncCheckboxes);
    syncCheckboxes();

    /*
     * Send selected students to session
     */
    $('#sendSelected').on('click', function () {
        if (selectedIds.size === 0) {
            Swal.fire(
                'Select students first',
                'Please select at least one student.',
                'warning'
            );
            return;
        }

        let sessionOptions = @json($sessionsList);
        let optionsHtml = '';

        Object.keys(sessionOptions).forEach(function (key) {
            optionsHtml += `
                <option value="${key}">
                    ${sessionOptions[key]}
                </option>
            `;
        });

        Swal.fire({
            title: 'Select Session',
            html: `
                <select id="session_id" class="form-control">
                    <option value="">Select Session</option>
                    ${optionsHtml}
                </select>
            `,
            confirmButtonText: 'Send',
            showCancelButton: true,
            preConfirm: function () {
                let selectedSession = $('#session_id').val();

                if (!selectedSession) {
                    Swal.showValidationMessage('Please select a session');
                    return false;
                }

                return selectedSession;
            }
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ route('admin.joining_students.sendToSession') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: Array.from(selectedIds),
                    session_id: result.value
                },
                success: function (response) {
                    Swal.fire(
                        'Success',
                        response.message,
                        'success'
                    ).then(function () {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    let message = 'Unable to send selected students.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire('Error', message, 'error');
                }
            });
        });
    });

    /*
     * Copy joining link
     */
    $(document).on('click', '.copy-link', function () {
        let link = $(this).data('link');

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(link)
                .then(function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Joining form link copied to clipboard.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                })
                .catch(function () {
                    fallbackCopyLink(link);
                });
        } else {
            fallbackCopyLink(link);
        }
    });

    function fallbackCopyLink(link) {
        let temporaryInput = $('<textarea>');
        temporaryInput.val(link);
        $('body').append(temporaryInput);
        temporaryInput.select();

        let copied = document.execCommand('copy');
        temporaryInput.remove();

        if (copied) {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Joining form link copied to clipboard.',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire('Copy Failed', 'Could not copy the joining link.', 'error');
        }
    }

    /*
     * Single soft delete
     */
    $(document).on('click', '.delete-joining-btn', function () {
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Delete this joining student?',
            text: 'The record will be moved to trash.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /*
     * Bulk soft delete
     */
    $('#deleteSelectedBtn').on('click', function () {
        let ids = Array.from(selectedIds);

        if (ids.length === 0) {
            Swal.fire(
                'No records selected',
                'Please select at least one joining student.',
                'warning'
            );
            return;
        }

        Swal.fire({
            title: 'Delete selected students?',
            text: `${ids.length} joining student(s) will be moved to trash.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete selected',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ route('admin.joining_students.bulk-destroy') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids
                },
                success: function (response) {
                    Swal.fire('Deleted', response.message, 'success')
                        .then(function () {
                            location.reload();
                        });
                },
                error: function (xhr) {
                    let message = 'Unable to delete selected records.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire('Error', message, 'error');
                }
            });
        });
    });

    /*
     * SweetAlert flash messages
     */
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: @json(session('success'))
        });
    @elseif(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @json(session('error'))
        });
    @elseif(session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: @json(session('info'))
        });
    @endif

});
</script>
@endpush