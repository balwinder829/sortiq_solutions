@extends('layouts.app')

@section('content')

<style>
    .payment-proof-thumbnail {
        width: 200px;
        max-height: 150px;
        object-fit: contain;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        transform-origin: left center;
        cursor: zoom-in;
    }

    .payment-proof-thumbnail:hover {
        transform: scale(1.6);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        position: relative;
        z-index: 10;
    }

    .payment-admin-note {
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }
</style>

<div class="container">

    {{-- PAGE HEADER --}}
<div class="row mb-2 align-items-center">

    <div class="col-md-7">
        <h1 class="page_heading">Student Details</h1>

        <!-- <div class="text-muted">
            {{ $student->student_name ?? '-' }}
        </div> -->
    </div>

    <div class="col-md-5">
        <div class="d-flex justify-content-end gap-2 flex-wrap">

            <a
                href="{{ route('admin.pending_request.index') }}"
                class="btn mb-3"
                style="background-color:#6b51df; color:#fff;"
            >
                <i class="fa fa-arrow-left"></i>
                Back to List
            </a>

        </div>
    </div>

</div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        {{-- STUDENT DETAILS --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">

                <div class="card-header">
                    <strong>Student Information</strong>
                </div>

                <div class="card-body">

                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Student Name</th>
                            <td>{{ $student->student_name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Father Name</th>
                            <td>{{ $student->father_name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Contact</th>
                            <td>{{ $student->contact ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{ $student->email ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Gender</th>
                            <td>{{ $student->gender ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>College</th>
                            <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Course</th>
                            <td>
                                {{ $student->courseData->course_name ?? $student->course_name_input ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Semester</th>
                            <td>{{ $student->semester ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Study Mode</th>
                            <td>{{ $student->study_mode ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Start Date</th>
                            <td>
                                @if($student->start_date)
                                    {{ \Carbon\Carbon::parse($student->start_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>

        {{-- PAYMENT DETAILS --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">

                <div class="card-header">
                    <strong>Payment Information</strong>
                </div>

                <div class="card-body">

                    @php
                        $paymentStatus = $student->payment_status ?? 'pending';

                        $statusClass = match($paymentStatus) {
                            'verified' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'submitted' => 'bg-warning text-dark',
                            'refunded' => 'bg-info text-dark',
                            default => 'bg-secondary',
                        };

                        $statusLabel = match($paymentStatus) {
                            'verified' => 'Verified',
                            'rejected' => 'Rejected',
                            'submitted' => 'Submitted',
                            'refunded' => 'Refunded',
                            default => 'Pending',
                        };
                    @endphp

                    <table class="table table-bordered">

                        <tr>
                            <th width="35%">Payment Status</th>
                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th>Amount</th>
                            <td>
                                @if($student->payment_amount !== null)
                                    ₹{{ number_format((float) $student->payment_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>UPI / QR Account</th>
                            <td>
                                {{ $student->paymentUpiAccount->name ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Provider</th>
                            <td>
                                {{ $student->paymentUpiAccount->provider ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>UPI ID</th>
                            <td>
                                {{ $student->paymentUpiAccount->upi_id ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Transaction / UTR ID</th>
                            <td>
                                {{ $student->payment_transaction_id ?? '-' }}
                            </td>
                        </tr>

                        <tr>
                            <th>Payment Date</th>
                            <td>
                                @if($student->payment_date)
                                    {{ \Carbon\Carbon::parse($student->payment_date)->format('d M Y, h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Verified At</th>
                            <td>
                                @if($student->payment_verified_at)
                                    {{ \Carbon\Carbon::parse($student->payment_verified_at)->format('d M Y, h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        {{-- ADMIN NOTE --}}
                        <tr>
                            <th>Admin Note</th>
                            <td>
                                @if($student->payment_admin_note)
                                    <div class="payment-admin-note">
                                        {{ $student->payment_admin_note }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                    </table>

                    {{-- PAYMENT SCREENSHOT --}}
                    <h6 class="mt-4 mb-3">Payment Screenshot</h6>

                    @if($student->payment_proof)

                        <div class="mb-4">

                            <img
                                src="{{ route('admin.pending_request.payment-proof', $student->id) }}"
                                alt="Payment Screenshot"
                                class="payment-proof-thumbnail"
                                data-bs-toggle="modal"
                                data-bs-target="#paymentProofModal"
                            >

                            <small class="text-muted d-block mt-2">
                                Hover to enlarge or click to view the full screenshot.
                            </small>

                        </div>

                        {{-- SCREENSHOT POPUP --}}
                        <div
                            class="modal fade"
                            id="paymentProofModal"
                            tabindex="-1"
                            aria-labelledby="paymentProofModalLabel"
                            aria-hidden="true"
                        >
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5
                                            class="modal-title"
                                            id="paymentProofModalLabel"
                                        >
                                            Payment Screenshot
                                        </h5>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"
                                        ></button>
                                    </div>

                                    <div class="modal-body text-center">
                                        <img
                                            src="{{ route('admin.pending_request.payment-proof', $student->id) }}"
                                            alt="Full Payment Screenshot"
                                            class="img-fluid"
                                            style="max-height: 80vh;"
                                        >
                                    </div>

                                    <div class="modal-footer">

                                        <a
                                            href="{{ route('admin.pending_request.payment-proof', $student->id) }}"
                                            target="_blank"
                                            class="btn btn-primary"
                                        >
                                            <i class="fa fa-external-link"></i>
                                            Open in New Tab
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-bs-dismiss="modal"
                                        >
                                            Close
                                        </button>

                                    </div>

                                </div>
                            </div>
                        </div>

                    @else

                        <p class="text-muted">
                            No payment screenshot uploaded.
                        </p>

                    @endif

                    {{-- VERIFY / REJECT PAYMENT --}}
                    @if(!in_array($paymentStatus, ['verified', 'rejected'], true))

                        <div class="row g-3 mt-4">

                            {{-- VERIFY PAYMENT --}}
                            <div class="col-md-6">
                                <div class="card border-success h-100">

                                    <div class="card-header bg-success text-white">
                                        <strong>Verify Payment</strong>
                                    </div>

                                    <div class="card-body">

                                        <form
                                            method="POST"
                                            action="{{ route('admin.pending_request.verify-payment', $student->id) }}"
                                        >
                                            @csrf

                                            <div class="mb-3">
                                                <label
                                                    for="verify_payment_admin_note"
                                                    class="form-label"
                                                >
                                                    Admin Note
                                                    <small class="text-muted">(Optional)</small>
                                                </label>

                                                <textarea
                                                    name="payment_admin_note"
                                                    id="verify_payment_admin_note"
                                                    class="form-control"
                                                    rows="3"
                                                    maxlength="5000"
                                                    placeholder="Enter verification note..."
                                                >{{ old('payment_admin_note', $student->payment_admin_note) }}</textarea>
                                            </div>

                                            <button type="button" class="btn btn-success verify-payment-btn">
                                                <i class="fa fa-check"></i>
                                                Verify Payment
                                            </button>

                                        </form>

                                    </div>
                                </div>
                            </div>

                            {{-- REJECT PAYMENT --}}
                            <div class="col-md-6">
                                <div class="card border-danger h-100">

                                    <div class="card-header bg-danger text-white">
                                        <strong>Reject Payment</strong>
                                    </div>

                                    <div class="card-body">

                                        <form
                                            method="POST"
                                            action="{{ route('admin.pending_request.reject-payment', $student->id) }}"
                                            class="reject-payment-form"
                                        >
                                            @csrf

                                            <div class="mb-3">
                                                <label
                                                    for="reject_payment_admin_note"
                                                    class="form-label"
                                                >
                                                    Admin Note
                                                    <small class="text-muted">(Optional)</small>
                                                </label>

                                                <textarea
                                                    name="payment_admin_note"
                                                    id="reject_payment_admin_note"
                                                    class="form-control"
                                                    rows="3"
                                                    maxlength="5000"
                                                    placeholder="Enter rejection note..."
                                                >{{ old('payment_admin_note', $student->payment_admin_note) }}</textarea>
                                            </div>

                                            <button
                                                type="button"
                                                class="btn btn-danger reject-payment-btn"
                                            >
                                                <i class="fa fa-times"></i>
                                                Reject Payment
                                            </button>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>

                    @elseif($paymentStatus === 'verified')

                        <div class="alert alert-success mt-4 mb-0">
                            <i class="fa fa-check-circle"></i>
                            This payment has been verified.
                        </div>

                    @elseif($paymentStatus === 'rejected')

                        <div class="alert alert-danger mt-4 mb-0">
                            <i class="fa fa-times-circle"></i>
                            This payment has been rejected.
                        </div>

                    @endif

                </div>
            </div>
        </div>

    </div>

</div>

@endsection

 

@push('scripts')
<script>
    // Verify Payment confirmation
    $(document).on('click', '.verify-payment-btn', function () {
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Verify this payment?',
            text: 'The payment status will be changed to verified.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, verify payment',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#198754'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Reject Payment confirmation
    $(document).on('click', '.reject-payment-btn', function () {
        const form = $(this).closest('form');

        Swal.fire({
            title: 'Reject this payment?',
            text: 'The payment status will be changed to rejected.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reject payment',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush