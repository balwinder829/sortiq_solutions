@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    #expenseTable {
        width: 100% !important;
    }
</style>

<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Travel Expenses</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2 flex-wrap">
                <button type="button" id="bulkDeleteBtn" class="btn mb-3"
                        style="background:#dc3545;color:#fff;">
                    <i class="fa fa-trash"></i> Delete Selected
                </button>

                <a href="{{ route('travel-expenses.create') }}"
                   class="btn mb-3" style="background:#6b51df;color:#fff;">
                    Add Travel Expense
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

    <form method="GET" id="filterForm" class="mb-4">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="fw-bold">Date Range</label>
                <select name="quick" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="today" {{ request('quick') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('quick') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="7days" {{ request('quick') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="1month" {{ request('quick') == '1month' ? 'selected' : '' }}>Last 1 Month</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">From Date</label>
                <input type="date" name="from_date"
                       value="{{ request('from_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filterchange">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">To Date</label>
                <input type="date" name="to_date"
                       value="{{ request('to_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filterchange">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Title</label>
                <input type="text" name="title"
                       value="{{ request('title') }}"
                       class="form-control filterchangetext"
                       placeholder="Travel purpose">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('travel-expenses.index') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Bulk-delete data without wrapping the table in a form --}}
    <input type="hidden" id="bulkDeleteUrl"
           value="{{ route('travel-expenses.bulkDelete') }}">
    <input type="hidden" id="bulkDeleteToken" value="{{ csrf_token() }}">

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="expenseTable">
            <thead class="table-light">
                <tr>
                    <th class="text-center">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th>#</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Amount(Rs.)</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="row-checkbox"
                                   value="{{ $expense->id }}">
                        </td>

                        <td></td>

                        <td>
                            {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                        </td>

                        <td>{{ $expense->title }}</td>

                        <td>{{ number_format($expense->amount, 2) }}</td>

                        <td class="text-center">
                            <a href="{{ route('travel-expenses.show', $expense->id) }}"
                               class="btn btn-sm" title="View">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('travel-expenses.edit', $expense->id) }}"
                               class="btn btn-sm" title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('travel-expenses.destroy', $expense->id) }}"
                                  method="POST"
                                  class="d-inline travel-expense-delete-form">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm" title="Delete">
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
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    var table = $('#expenseTable').DataTable({
        pageLength: 25,
        order: [[2, 'desc']],
        autoWidth: true,
        scrollX: true,
        columnDefs: [
            {
                targets: [0, 1],
                searchable: false,
                orderable: false
            },
            {
                targets: 5,
                searchable: false,
                orderable: false
            }
        ]
    });

    // Keep selections across pagination, search and redraws.
    var selectedTravelExpenseIds = new Set();

    function updateExpenseCheckboxes() {
        $('#expenseTable tbody .row-checkbox').each(function () {
            this.checked = selectedTravelExpenseIds.has(String(this.value));
        });

        var checkboxes = $('#expenseTable tbody .row-checkbox');
        var checkedCount = checkboxes.filter(':checked').length;

        $('#checkAll').prop(
            'checked',
            checkboxes.length > 0 && checkedCount === checkboxes.length
        );
    }

    $(document).on('change', '#expenseTable tbody .row-checkbox', function () {
        var id = String(this.value);

        if (this.checked) {
            selectedTravelExpenseIds.add(id);
        } else {
            selectedTravelExpenseIds.delete(id);
        }

        updateExpenseCheckboxes();
    });

    // Select/deselect current page only.
    $(document).on('change', '#checkAll', function () {
        var checked = this.checked;

        $('#expenseTable tbody .row-checkbox').each(function () {
            var id = String(this.value);

            if (checked) {
                selectedTravelExpenseIds.add(id);
            } else {
                selectedTravelExpenseIds.delete(id);
            }
        });

        updateExpenseCheckboxes();
    });

    table.on('draw.dt', function () {
        var pageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = pageInfo.start + i + 1;
        });

        updateExpenseCheckboxes();
    });

    table.draw();

    // Bulk delete selected travel expenses.
    $('#bulkDeleteBtn').on('click', function () {

        if (selectedTravelExpenseIds.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Record Selected',
                text: 'Please select at least 1 record.'
            });
            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Delete Selected Travel Expenses?',
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
                    ids: Array.from(selectedTravelExpenseIds).join(',')
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

    // Existing filter behavior.
    var timer;

    $('.filterchange').on('change', function () {
        $('#filterForm').submit();
    });

    $('.filterchangetext').on('input', function () {
        clearTimeout(timer);

        timer = setTimeout(function () {
            $('#filterForm').submit();
        }, 500);
    });

    // Single-record delete confirmation.
    $(document).on('submit', '.travel-expense-delete-form', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var form = this;

        Swal.fire({
            icon: 'warning',
            title: 'Delete Travel Expense?',
            text: 'Are you sure you want to delete this travel expense?',
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