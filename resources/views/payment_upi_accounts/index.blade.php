@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    .qr-preview {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 3px;
        background: #fff;
    }

    .default-badge {
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-active {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #842029;
    }

    .provider-badge {
        background-color: #f1f1f1;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
    }

    .warning-box {
        border-left: 4px solid #ffc107;
    }

    .qr-preview {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 4px;
        background: #fff;
    }

    .qr-preview:hover {
        border-color: #6b51df;
        box-shadow: 0 0 5px rgba(107, 81, 223, 0.35);
    }
</style>

<div class="container">

    {{-- ================================
        PAGE HEADER
    ================================= --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-8">
            <h1 class="page_heading">Payment UPI / QR</h1>
        </div>

        <div class="col-md-4">
            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('payment-upi-accounts.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df; color:#fff;">
                    Add UPI
                </a>

            </div>
        </div>

    </div>


    {{-- ================================
        SUCCESS MESSAGE
    ================================= --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ================================
        ERROR MESSAGE
    ================================= --}}
    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


    {{-- ================================
        VALIDATION ERRORS
    ================================= --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- ================================
        DEFAULT WARNING
    ================================= --}}
    @php
        $activeAccounts = $paymentUpiAccounts->where('is_active', true);
        $defaultAccount = $activeAccounts->where('is_default', true)->first();
    @endphp

    @if(!$defaultAccount && $activeAccounts->count() > 0)

        <div class="alert alert-warning warning-box">

            <strong>No Default UPI/QR is configured.</strong>

            <div class="mt-1">
                Forms without a specific UPI/QR assignment will not have
                a payment QR. Please set an active UPI as the default.
            </div>

        </div>

    @endif


    {{-- ================================
        NO UPI WARNING
    ================================= --}}
    @if($paymentUpiAccounts->count() === 0)

        <div class="alert alert-danger warning-box">

            <strong>No UPI/QR has been added yet.</strong>

            <div class="mt-1">
                Add a UPI account and upload its QR code before using
                UPI payments in forms.
            </div>

            <div class="mt-2">

                <a href="{{ route('payment-upi-accounts.create') }}"
                   class="btn btn-danger btn-sm">
                    Add UPI / QR
                </a>

            </div>

        </div>

    @endif


    {{-- ================================
        STATUS TABS
    ================================= --}}
    <div class="mb-3">

        <ul class="nav nav-tabs">

            <li class="nav-item">

                <a href="javascript:void(0)"
                   class="nav-link upi-status-tab active"
                   data-status="active">

                    Active

                </a>

            </li>

            <li class="nav-item">

                <a href="javascript:void(0)"
                   class="nav-link upi-status-tab"
                   data-status="inactive">

                    Inactive

                </a>

            </li>

        </ul>

    </div>


    {{-- ================================
        UPI TABLE
    ================================= --}}
    <table id="payment-upi-table"
           class="table table-bordered table-striped">

        <thead>

            <tr>

                <th>ID</th>

                <th>UPI Name</th>

                <th>Provider</th>

                <th>UPI ID</th>

                <th>QR Code</th>

                <th>Default</th>

                <th>Assigned Forms</th>

                <th>Status</th>

                <th style="width:220px!important;">
                    Actions
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach($paymentUpiAccounts as $account)

                <tr class="upi-row"
                    data-status="{{ $account->is_active ? 'active' : 'inactive' }}">

                    <td>
                        {{ $account->id }}
                    </td>

                    <td>
                        <strong>{{ $account->name }}</strong>
                    </td>

                    <td>

                        @if($account->provider)

                            <span class="provider-badge">
                                {{ $account->provider }}
                            </span>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </td>

                    <td>

                        @if($account->upi_id)

                            {{ $account->upi_id }}

                        @else

                            <span class="text-muted">
                                Not Added
                            </span>

                        @endif

                    </td>

                    <td class="text-center">

                        <img src="{{ asset($account->qr_image) }}"
                             alt="QR Code"
                             class="qr-preview qr-clickable"
                             data-image="{{ asset($account->qr_image) }}"
                             data-name="{{ $account->name }}"
                             style="cursor:pointer;">

                    </td>

                    <td>

                        @if($account->is_default)

                            <span class="default-badge">
                                ★ Default
                            </span>

                        @else

                            @if($account->is_active)

                                <form method="POST"
                                      action="{{ route('payment-upi-accounts.set-default', $account->id) }}"
                                      class="set-default-form">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-primary">

                                        Set Default

                                    </button>

                                </form>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        @endif

                    </td>

                    <td>

                        <span class="badge bg-secondary">

                            {{ $account->assigned_forms_count }}

                            {{ $account->assigned_forms_count == 1 ? 'Form' : 'Forms' }}

                        </span>

                    </td>

                    <td>

                        @if($account->is_active)

                            <span class="status-badge status-active">
                                Active
                            </span>

                        @else

                            <span class="status-badge status-inactive">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>

                        <div class="d-flex gap-1 flex-wrap">

                            <a href="{{ route('payment-upi-accounts.edit', $account->id) }}"
                               class="btn btn-sm"  title="Edit"
            aria-label="Edit">

                                <i class="fa fa-eye"></i>

                            </a>


                            @if(!$account->is_default && $account->assigned_forms_count == 0)

                                <form method="POST"
                                      action="{{ route('payment-upi-accounts.toggle-status', $account->id) }}"
                                      class="toggle-status-form">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-sm {{ $account->is_active ? 'btn-warning' : 'btn-success' }}">

                                        {{ $account->is_active ? 'Inactive' : 'Active' }}

                                    </button>

                                </form>

                                <form method="POST"
                                      action="{{ route('payment-upi-accounts.destroy', $account->id) }}"
                                      class="delete-upi-form">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm" title="Delete"
                aria-label="Delete">

                                        <i class="fa fa-trash"></i>

                                    </button>

                                </form>

                            @elseif($account->is_default)

                                <span class="text-muted small align-self-center">
                                    Default
                                </span>

                            @else

                                <span class="text-muted small align-self-center">
                                    Assigned
                                </span>

                            @endif

                        </div>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>
{{-- ==========================================
    QR PREVIEW MODAL
========================================== --}}

<div class="modal fade"
     id="qrPreviewModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="qrPreviewTitle">
                    QR Code
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body text-center">

                <img id="qrPreviewModalImage"
                     src=""
                     alt="QR Code"
                     style="
                        max-width:100%;
                        max-height:500px;
                        object-fit:contain;
                     ">

            </div>

        </div>

    </div>

</div>
@endsection


@section('scripts')

@push('scripts')

<script>

    // ==========================================
    // CURRENT STATUS
    // ==========================================

    let currentStatus = 'active';


    // ==========================================
    // DATATABLE
    // ==========================================

    $(document).ready(function () {

        let table = $('#payment-upi-table').DataTable({

            pageLength: 50,

            lengthMenu: [5, 10, 25, 50, 100],

            order: [[1, 'asc']],

            scrollX: true

        });


        // ==========================================
        // STATUS TABS
        // ==========================================

        $('.upi-status-tab').on('click', function (e) {

            e.preventDefault();

            currentStatus = $(this).data('status');

            $('.upi-status-tab').removeClass('active');

            $(this).addClass('active');

            $.fn.dataTable.ext.search = [];

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {

                    if (settings.nTable.id !== 'payment-upi-table') {
                        return true;
                    }

                    let row = table.row(dataIndex).node();

                    return $(row).data('status') === currentStatus;
                }
            );

            table.draw();

        });


        // ==========================================
        // SET DEFAULT CONFIRMATION
        // ==========================================

        $(document).on('submit', '.set-default-form', function (e) {

            e.preventDefault();

            let form = this;

            Swal.fire({

                icon: 'question',

                title: 'Set as Default?',

                text: 'This UPI will become the global default for forms without a specific UPI assignment.',

                showCancelButton: true,

                confirmButtonText: 'Yes, Set Default',

                cancelButtonText: 'Cancel'

            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        });


        // ==========================================
        // TOGGLE STATUS CONFIRMATION
        // ==========================================

        $(document).on('submit', '.toggle-status-form', function (e) {

            e.preventDefault();

            let form = this;

            let button = $(form).find('button');

            let isActivating = button.text().trim() === 'Active';

            Swal.fire({

                icon: 'warning',

                title: isActivating
                    ? 'Activate UPI?'
                    : 'Deactivate UPI?',

                text: isActivating
                    ? 'This UPI will become available for form assignments.'
                    : 'This UPI will no longer be available for use.',

                showCancelButton: true,

                confirmButtonText: 'Yes, Continue',

                cancelButtonText: 'Cancel'

            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        });


        // ==========================================
        // DELETE CONFIRMATION
        // ==========================================

        $(document).on('submit', '.delete-upi-form', function (e) {

            e.preventDefault();

            let form = this;

            Swal.fire({

                icon: 'warning',

                title: 'Delete UPI / QR?',

                text: 'This UPI account will be deleted.',

                showCancelButton: true,

                confirmButtonText: 'Yes, Delete',

                cancelButtonText: 'Cancel',

                confirmButtonColor: '#dc3545'

            }).then((result) => {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        });

    });
// ==========================================
// QR PREVIEW
// ==========================================

$(document).on('click', '.qr-clickable', function () {

    let image = $(this).data('image');

    let name = $(this).data('name');

    $('#qrPreviewModalImage').attr('src', image);

    $('#qrPreviewTitle').text(name + ' - QR Code');

    $('#qrPreviewModal').modal('show');

});
</script>

@endpush

@endsection