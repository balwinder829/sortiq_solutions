@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }
</style>

<div class="container">

    <a href="{{ route('office-assets.create') }}"
       class="btn mb-3"
       style="background:#6b51df;color:#fff;">
        Add Office Asset
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="GET" action="{{ route('office-assets.index') }}" class="mb-4">
        <div class="row align-items-end">

            <div class="col-md-3">
                <label class="fw-bold">Date Range</label>
                <select name="quick" class="form-control">
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
                       class="form-control">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">To Date</label>
                <input type="date" name="to_date"
                       value="{{ request('to_date') }}"
                       max="{{ now()->format('Y-m-d') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Title</label>
                <input type="text" name="title"
                       value="{{ request('title') }}"
                       class="form-control"
                       placeholder="Asset name">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <button class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>
                <a href="{{ route('office-assets.index') }}"
                   class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="expenseTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Amount</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($assets as $asset)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($asset->expense_date)->format('d M Y') }}</td>
                    <td>{{ $asset->title }}</td>
                    <td>{{ number_format($asset->amount, 2) }}</td>
                    <td class="text-center">

                        <a href="{{ route('office-assets.show', $asset->id) }}" class="btn btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('office-assets.edit', $asset->id) }}" class="btn btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('office-assets.destroy', $asset->id) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm"
                                    onclick="return confirm('Are you sure?')">
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
        order: [[1,'desc']]
    });
});
</script>
@endpush
