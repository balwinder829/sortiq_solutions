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

    #expenseTable_wrapper {
        width: 100%;
    }
</style>

<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Papers Expenses</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end align-items-center gap-2 mb-3 flex-wrap">
                <button type="button"
                        id="bulkDeleteExpenses"
                        class="btn btn-danger">
                    <i class="fa fa-trash"></i> Delete Selected
                </button>

                <a href="{{ route('office-paper-expenses.create') }}"
                   class="btn"
                   style="background:#6b51df;color:#fff;">
                    Add Expense
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

    {{-- FILTERS --}}
    <form method="GET"
          id="filterForm"
          action="{{ route('office-paper-expenses.index') }}"
          class="mb-4">

        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="fw-bold">Date Range</label>
                <select name="quick" class="form-control filter-change">
                    <option value="">All</option>
                    <option value="today" {{ request('quick')=='today'?'selected':'' }}>Today</option>
                    <option value="yesterday" {{ request('quick')=='yesterday'?'selected':'' }}>Yesterday</option>
                    <option value="7days" {{ request('quick')=='7days'?'selected':'' }}>Last 7 Days</option>
                    <option value="1month" {{ request('quick')=='1month'?'selected':'' }}>Last 1 Month</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">From Date</label>
                <input type="date"
                       name="from_date"
                       value="{{ request('from_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filter-change">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">To Date</label>
                <input type="date"
                       name="to_date"
                       value="{{ request('to_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filter-change">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Title</label>
                <input type="text"
                       name="title"
                       value="{{ request('title') }}"
                       class="form-control filter-change"
                       placeholder="Item / Purpose">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('office-paper-expenses.index') }}"
                   class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="expenseTable">
            <thead class="table-light">
                <tr>
                    <th class="text-center">
                        <input type="checkbox"
                               id="checkAll"
                               title="Select current page">
                    </th>
                    <th>#</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Quantity</th>
                    <th>Amount(Rs.)</th>
                    <th>Other Charges(Rs.)</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($expenses as $expense)
                <tr>
                    <td class="text-center">
                        <input type="checkbox"
                               class="expense-checkbox"
                               value="{{ $expense->id }}">
                    </td>

                    <td></td>

                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>

                    <td>{{ $expense->title }}</td>

                    <td>{{ $expense->quantity }}</td>

                    <td>{{ number_format($expense->total_amount, 2) }}</td>

                    <td>{{ number_format($expense->other_charges, 2) }}</td>

                    <td class="text-center">
                        <a href="{{ route('office-paper-expenses.show', $expense->id) }}"
                           class="btn btn-sm"
                           title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('office-paper-expenses.edit', $expense->id) }}"
                           class="btn btn-sm"
                           title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('office-paper-expenses.destroy', $expense->id) }}"
                              method="POST"
                              class="d-inline paper-expense-delete-form">
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
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var table = $('#expenseTable').DataTable({
        pageLength: 25,
        order: [],
        scrollX: true,
        autoWidth: true,
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
// table.page.len(2).draw();
    // Keep selections when changing pages or searching.
    var selectedExpenses = {};

    function updateExpenseCheckboxes() {
        var $currentCheckboxes = table.rows({ page: 'current' })
            .nodes().to$()
            .find('.expense-checkbox');

        $currentCheckboxes.each(function () {
            this.checked = !!selectedExpenses[this.value];
        });

        var total = $currentCheckboxes.length;
        var checked = $currentCheckboxes.filter(':checked').length;

        $('input[type="checkbox"][id="checkAll"]')
            .prop('checked', total > 0 && checked === total)
            .prop('indeterminate', checked > 0 && checked < total);
    }

    // Serial numbers.
    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });

        updateExpenseCheckboxes();
    });

    // Select or deselect all records on the current page only.
    $(document).on('change', '#checkAll', function () {
        var checked = this.checked;

        table.rows({ page: 'current' }).nodes().to$()
            .find('.expense-checkbox')
            .each(function () {
                this.checked = checked;

                if (checked) {
                    selectedExpenses[this.value] = true;
                } else {
                    delete selectedExpenses[this.value];
                }
            });

        updateExpenseCheckboxes();
    });

    // Individual checkbox selection.
    $('#expenseTable').on('change', '.expense-checkbox', function () {
        if (this.checked) {
            selectedExpenses[this.value] = true;
        } else {
            delete selectedExpenses[this.value];
        }

        updateExpenseCheckboxes();
    });

    // Bulk delete.
    $('#bulkDeleteExpenses').on('click', function () {
        var ids = Object.keys(selectedExpenses);

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Record Selected',
                text: 'Please select at least 1 record.',
                confirmButtonText: 'OK'
            });

            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Delete Selected Paper Expenses?',
            text: 'Are you sure you want to delete the selected paper expenses?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: @json(route('office-paper-expenses.bulkDelete')),
                type: 'POST',
                data: {
                    _token: @json(csrf_token()),
                    ids: ids.join(',')
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
                            text: response.message || 'Unable to delete selected expenses.'
                        });
                    }
                },
                error: function (xhr) {
                    var message = 'Unable to delete selected expenses. Please try again.';

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

    // Single-row delete confirmation.
    $(document).on('submit', '.paper-expense-delete-form', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var form = this;

        Swal.fire({
            icon: 'warning',
            title: 'Delete Paper Expense?',
            text: 'Are you sure you want to delete this expense?',
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

    updateExpenseCheckboxes();

    // Existing filters.
    $('.filter-change').on('change', function () {
        $('#filterForm').submit();
    });

    let typingTimer;

    $('.filter-change').on('keyup', function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            $('#filterForm').submit();
        }, 600);
    });
});
</script>
@endpush