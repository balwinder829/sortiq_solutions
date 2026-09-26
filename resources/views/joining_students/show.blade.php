@extends('layouts.app')

@section('content')

<style>
    .payment-proof-thumbnail {
        max-width: 220px;
        max-height: 220px;
        object-fit: contain;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px;
        transition: transform .2s ease;
    }

    .payment-proof-thumbnail:hover {
        transform: scale(1.04);
    }
</style>

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h1 class="page_heading">Joining Student Details</h1>
        </div>

        <div class="col-md-4 text-md-end">
            <a
                href="{{ route('admin.joining_students.index') }}"
                class="btn btn-secondary mb-3"
            >
                <i class="fa fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>

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

    <div class="row">

        {{-- STUDENT INFORMATION --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">

                <div class="card-header">
                    <strong>Student Information</strong>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">

                        <tr>
                            <th width="35%">Student Name</th>
                            <td>{{ $student->student_name }}</td>
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
                            <th>College</th>
                            <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Course / Technology</th>
                            <td>{{ $student->courseData->course_name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Duration</th>
                            <td>{{ $student->durationData->name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Date of Joining</th>
                            <td>
                                @if($student->date_of_joining)
                                    {{ \Carbon\Carbon::parse($student->date_of_joining)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Sent to Student Details</th>
                            <td>
                                @if($student->is_sent_to_detail)
                                    <span class="badge bg-success">Sent</span>
                                @else
                                    <span class="badge bg-secondary">Not Sent</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Sent Date</th>
                            <td>
                                @if($student->sent_to_detail_at)
                                    {{ \Carbon\Carbon::parse($student->sent_to_detail_at)->format('d M Y, h:i A') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

        {{-- PAYMENT INFORMATION --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">

                <div class="card-header">
                    <strong>Payment Information</strong>
                </div>

                <div class="card-body">

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
                            <td>{{ $student->paymentUpiAccount->name ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Provider</th>
                            <td>{{ $student->paymentUpiAccount->provider ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>UPI ID</th>
                            <td>{{ $student->paymentUpiAccount->upi_id ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Transaction / UTR ID</th>
                            <td>{{ $student->payment_transaction_id ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Payment Date</th>
                            <td>
                                @if($student->payment_date)
                                    {{ \Carbon\Carbon::parse($student->payment_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Verified At</th>
                            <td>
                                @if($student->payment_verified_at)
                                    {{ \Carbon\Carbon::parse($student->payment_verified_at)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th>Admin Note</th>
                            <td>
                                @if($student->payment_admin_note)
                                    <div class="text-break">
                                        {{ $student->payment_admin_note }}
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>

                    </table>

                    {{-- PAYMENT SCREENSHOT --}}
                    <h6 class="mt-4">Payment Screenshot</h6>

                    @if($student->payment_proof)

                        <div class="mb-4">
                            <img
                                src="{{ route('admin.joining_students.payment-proof', $student->id) }}"
                                alt="Payment Screenshot"
                                class="payment-proof-thumbnail"
                                style="cursor: zoom-in;"
                                data-bs-toggle="modal"
                                data-bs-target="#paymentProofModal"
                            >

                            <small class="text-muted d-block mt-2">
                                Click to view the full screenshot.
                            </small>
                        </div>

                        {{-- Screenshot modal --}}
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
                                            src="{{ route('admin.joining_students.payment-proof', $student->id) }}"
                                            alt="Full Payment Screenshot"
                                            class="img-fluid"
                                            style="max-height:80vh;"
                                        >
                                    </div>

                                    <div class="modal-footer">
                                        <a
                                            href="{{ route('admin.joining_students.payment-proof', $student->id) }}"
                                            target="_blank"
                                            class="btn btn-primary"
                                        >
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
                        <p class="text-muted">No payment screenshot uploaded.</p>
                    @endif

                    {{-- VERIFY / REJECT --}}
                    @if(!in_array($paymentStatus, ['verified', 'rejected'], true))

                        <div class="row mt-4">

                            {{-- VERIFY FORM --}}
                            <div class="col-md-6 mb-3">
                                <form
                                    method="POST"
                                    action="{{ route('admin.joining_students.verify-payment', $student->id) }}"
                                    class="payment-action-form"
                                    data-action="verify"
                                >
                                    @csrf

                                    <label class="form-label">
                                        Admin Note (Optional)
                                    </label>

                                    <textarea
                                        name="payment_admin_note"
                                        class="form-control mb-3"
                                        rows="3"
                                        maxlength="5000"
                                        placeholder="Optional note for verification"
                                    >{{ old('payment_admin_note', $student->payment_admin_note) }}</textarea>

                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                    >
                                        Verify Payment
                                    </button>
                                </form>
                            </div>

                            {{-- REJECT FORM --}}
                            <div class="col-md-6 mb-3">
                                <form
                                    method="POST"
                                    action="{{ route('admin.joining_students.reject-payment', $student->id) }}"
                                    class="payment-action-form"
                                    data-action="reject"
                                >
                                    @csrf

                                    <label class="form-label">
                                        Admin Note (Optional)
                                    </label>

                                    <textarea
                                        name="payment_admin_note"
                                        class="form-control mb-3"
                                        rows="3"
                                        maxlength="5000"
                                        placeholder="Optional note for rejection"
                                    >{{ old('payment_admin_note', $student->payment_admin_note) }}</textarea>

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                    >
                                        Reject Payment
                                    </button>
                                </form>
                            </div>

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
$(document).ready(function () {

    /*
     * SweetAlert confirmation for payment actions
     */
    $('.payment-action-form').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const action = $(form).data('action');

        const isVerify = action === 'verify';

        Swal.fire({
            title: isVerify
                ? 'Verify this payment?'
                : 'Reject this payment?',
            text: isVerify
                ? 'Confirm that the payment details are correct.'
                : 'Confirm that you want to reject this payment.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: isVerify
                ? 'Yes, verify'
                : 'Yes, reject',
            cancelButtonText: 'Cancel',
            confirmButtonColor: isVerify
                ? '#198754'
                : '#dc3545'
        }).then(function (result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    /*
     * Flash messages
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