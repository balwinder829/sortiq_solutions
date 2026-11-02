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
            <h1 class="page_heading">Electricity Bills</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">

                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                <a href="{{ route('office-expenses.create') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Add Electricity Bill
                </a>

            </div>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= FILTERS ================= --}}
    <form method="GET" id="filterForm" class="mb-4">
        <div class="row align-items-end">

            <div class="col-md-3">
                <label class="fw-bold">Date Range</label>
                <select name="quick" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="today" {{ request('quick')=='today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('quick')=='yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="7days" {{ request('quick')=='7days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="1month" {{ request('quick')=='1month' ? 'selected' : '' }}>Last 1 Month</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">From Date</label>
                <input type="date"
                       name="from_date"
                       value="{{ request('from_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filterchange">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">To Date</label>
                <input type="date"
                       name="to_date"
                       value="{{ request('to_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filterchange">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Title</label>
                <input type="text"
                       name="title"
                       value="{{ request('title') }}"
                       class="form-control filterchangetext"
                       placeholder="Expense title">
            </div>

        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('office-expenses.index') }}"
                   class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
    {{-- ================= END FILTERS ================= --}}

    {{-- TABLE --}}
    <table class="table table-bordered table-striped"
           id="expenseTable"
           style="width:100%;">

        <thead class="table-light">
            <tr>
                <th>
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
                    <td>
                        <input type="checkbox"
                               class="expense-checkbox"
                               value="{{ $expense->id }}">
                    </td>

                    <td></td>

                    <td>
                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                    </td>

                    <td>{{ $expense->title }}</td>

                    <td>{{ number_format($expense->amount, 2) }}</td>

                    {{-- VIEW / EDIT / DELETE --}}
                    <td class="text-center">

                        <a href="{{ route('office-expenses.show', $expense->id) }}"
                           class="btn btn-sm"
                           title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('office-expenses.edit', $expense->id) }}"
                           class="btn btn-sm"
                           title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('office-expenses.destroy', $expense->id) }}"
                              method="POST"
                              class="d-inline expense-delete-form">

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

{{-- IMAGE MODAL --}}
<div class="modal fade" id="expenseImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Expense Image</h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img src=""
                     id="expenseModalImage"
                     class="img-fluid rounded">
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    let selectedExpenses = {};

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
        ],

        drawCallback: function () {

            $('#expenseTable tbody .expense-checkbox').each(function () {
                var id = $(this).val();

                $(this).prop(
                    'checked',
                    !!selectedExpenses[id]
                );
            });

            updateCheckAll();
        }
    });
// table.page.len(1).draw();
    // Serial number on current page.
    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    });

    table.draw();

    // Individual checkbox selection.
    $(document).on(
        'change',
        '#expenseTable tbody .expense-checkbox',
        function () {

            var id = $(this).val();

            if ($(this).is(':checked')) {
                selectedExpenses[id] = true;
            } else {
                delete selectedExpenses[id];
            }

            updateCheckAll();
        }
    );

    // Select All: current page only.
    $(document).on('change', '#checkAll', function () {

        var checked = $(this).is(':checked');

        $('#expenseTable tbody .expense-checkbox').each(function () {

            var id = $(this).val();

            $(this).prop('checked', checked);

            if (checked) {
                selectedExpenses[id] = true;
            } else {
                delete selectedExpenses[id];
            }
        });

        updateCheckAll();
    });

    function updateCheckAll() {

        var checkboxes = $('#expenseTable tbody .expense-checkbox');
        var total = checkboxes.length;
        var checked = checkboxes.filter(':checked').length;

        $('input[type="checkbox"][id="checkAll"]')
            .prop('checked', total > 0 && checked === total)
            .prop('indeterminate', checked > 0 && checked < total);
    }

    // Bulk Delete.
    $('#bulkDeleteBtn').on('click', function () {

        var ids = Object.keys(selectedExpenses);

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Record Selected',
                text: 'Please select at least 1 record.'
            });

            return;
        }

        Swal.fire({
            icon: 'warning',
            title: 'Delete Selected Electricity Bills?',
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
                url: "{{ route('office-expenses.bulkDelete') }}",
                type: 'POST',

                data: {
                    _token: "{{ csrf_token() }}",
                    ids: ids.join(',')
                },

                success: function (response) {

                    selectedExpenses = {};

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.reload();
                    });
                },

                error: function (xhr) {

                    var message = 'Something went wrong.';

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

    // Single Delete SweetAlert.
    $(document).on(
        'submit',
        '.expense-delete-form',
        function (e) {

            e.preventDefault();
            e.stopImmediatePropagation();

            const form = this;

            Swal.fire({
                icon: 'warning',
                title: 'Delete Electricity Bill?',
                text: 'Are you sure you want to delete this electricity bill?',
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
        }
    );

    // Existing image modal functionality.
    $(document).on('click', '.expense-image', function () {
        let imageSrc = $(this).data('image');

        $('#expenseModalImage').attr('src', imageSrc);
        $('#expenseImageModal').modal('show');
    });

});
</script>

<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        $('#filterForm').submit();
    });

    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 300);
    });

});
</script>
@endpush