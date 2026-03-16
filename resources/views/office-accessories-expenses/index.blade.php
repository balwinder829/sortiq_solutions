@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }
</style>

<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Accessories Expenses</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('office-accessories-expenses.create') }}"
                   class="btn mb-3"
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
    <form method="GET"  id="filterForm" action="{{ route('office-accessories-expenses.index') }}" class="mb-4">
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
                <input type="date" name="from_date"
                       value="{{ request('from_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filter-change">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">To Date</label>
                <input type="date" name="to_date"
                       value="{{ request('to_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control filter-change">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Title</label>
                <input type="text" name="title"
                       value="{{ request('title') }}"
                       class="form-control filter-change"
                       placeholder="Item / Purpose">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
               <!--  <button class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button> -->
                <a href="{{ route('office-accessories-expenses.index') }}"
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
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->quantity }}</td>
                    <td>{{ number_format($expense->total_amount, 2) }}</td>
                    <td>{{ number_format($expense->other_charges, 2) }}</td>
                    
                    <td class="text-center">

                        <a href="{{ route('office-accessories-expenses.show', $expense->id) }}"
                           class="btn btn-sm" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('office-accessories-expenses.edit', $expense->id) }}"
                           class="btn btn-sm" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('office-accessories-expenses.destroy', $expense->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm"
                                    data-swal-confirm="Are you sure?"
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
    $('#expenseTable').DataTable({
        pageLength: 25,
        order: []
    });

    $('.filter-change').on('change', function(){
        $('#filterForm').submit();
    });

    let typingTimer;
    $('.filter-change').on('keyup', function(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function(){
            $('#filterForm').submit();
        }, 600);
    });
});
</script>
@endpush
