@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    #rechargesTable {
        width: 100% !important;
    }
</style>

<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Recharges</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2 flex-wrap">

                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn mb-3"
                        style="background:#dc3545;color:#fff;">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>

                <a href="{{ route('recharges.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                    Add Recharge
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Bulk-delete configuration --}}
    <input type="hidden"
           id="bulkDeleteUrl"
           value="{{ route('recharges.bulkDelete') }}">

    <input type="hidden"
           id="bulkDeleteToken"
           value="{{ csrf_token() }}">

    <div class="table-responsive">
        <table id="rechargesTable"
               class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th>#</th>
                    <th>Mobile Number</th>
                    <th>Employee Name</th>
                    <th>Operator</th>
                    <th>Amount(Rs.)</th>
                    <th>Rechagre Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($recharges as $recharge)
                <tr>
                    <td class="text-center">
                        <input type="checkbox"
                               class="row-checkbox"
                               value="{{ $recharge->id }}">
                    </td>

                    <td></td>

                    <td>{{ $recharge->mobile_number }}</td>

                    <td>{{ $recharge->employee_name }}</td>

                    <td>{{ $recharge->operator }}</td>

                    <td>{{ $recharge->amount }}</td>

                    <td>
                        {{ $recharge->recharged_at
                            ? \Carbon\Carbon::parse($recharge->recharged_at)->format('d M Y')
                            : '' }}
                    </td>

                    <td>
                        <a href="{{ route('recharges.edit', $recharge) }}"
                           class="btn btn-sm"
                           data-bs-toggle="tooltip"
                           title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('recharges.destroy', $recharge) }}"
                              method="POST"
                              class="d-inline recharge-delete-form">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm"
                                    data-bs-toggle="tooltip"
                                    title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

    var table = $('#rechargesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        autoWidth: true,
        scrollX: true,
        columnDefs: [
            {
                targets: [0, 1],
                searchable: false,
                orderable: false
            },
            {
                targets: 7,
                searchable: false,
                orderable: false
            }
        ]
    });

    // Preserve selected IDs across pagination, searching and redraws.
    var selectedRechargeIds = new Set();

    function updateRechargeCheckboxes() {
        $('#rechargesTable tbody .row-checkbox').each(function () {
            this.checked = selectedRechargeIds.has(String(this.value));
        });

        var checkboxes = $('#rechargesTable tbody .row-checkbox');
        var checkedCount = checkboxes.filter(':checked').length;

        $('input[type="checkbox"][id="checkAll"]').prop(
            'checked',
            checkboxes.length > 0 && checkedCount === checkboxes.length
        );
    }

    // Individual checkbox selection.
    $(document).on('change', '#rechargesTable tbody .row-checkbox', function () {
        var id = String(this.value);

        if (this.checked) {
            selectedRechargeIds.add(id);
        } else {
            selectedRechargeIds.delete(id);
        }

        updateRechargeCheckboxes();
    });

    // Select/deselect current DataTable page only.
    $(document).on('change', 'input[type="checkbox"][id="checkAll"]', function () {
        var checked = this.checked;

        $('#rechargesTable tbody .row-checkbox').each(function () {
            var id = String(this.value);

            if (checked) {
                selectedRechargeIds.add(id);
            } else {
                selectedRechargeIds.delete(id);
            }
        });

        updateRechargeCheckboxes();
    });

    table.on('draw.dt', function () {
        var pageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = pageInfo.start + i + 1;
        });

        updateRechargeCheckboxes();
    });

    table.draw();

    // Bulk delete selected recharges.
    $('#bulkDeleteBtn').on('click', function () {

        if (selectedRechargeIds.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Record Selected',
                text: 'Please select at least 1 record.'
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Delete Selected Recharges?',
            text: 'Are you sure you want to delete the selected records?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: $('#bulkDeleteUrl').val(),
                type: 'POST',
                data: {
                    _token: $('#bulkDeleteToken').val(),
                    ids: Array.from(selectedRechargeIds).join(',')
                },
                success: function (response) {

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted Successfully',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Unable to delete selected records.'
                        });
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to delete selected records.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message
                    });
                }
            });
        });
    });

    // Existing Bootstrap tooltip behavior.
    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });

    // Single-record delete confirmation.
    $(document).on('submit', '.recharge-delete-form', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var form = this;

        Swal.fire({
            icon: 'warning',
            title: 'Delete Recharge?',
            text: 'Are you sure you want to delete this recharge?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {
            if (result.isConfirmed) {
                HTMLFormElement.prototype.submit.call(form);
            }
        });

        return false;
    });

});
</script>
@endpush