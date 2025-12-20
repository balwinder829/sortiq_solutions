@extends('layouts.app')

@section('content')
<div class="container">

<h3>Assignment Report</h3>

{{-- SUMMARY CARDS --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card p-3 bg-primary text-white">
            <h5>Today's Assignments</h5>
            <h2>{{ $summary['today'] }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-success text-white">
            <h5>Yesterday's Assignments</h5>
            <h2>{{ $summary['yesterday'] }}</h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-info text-white">
            <h5>Last 7 Days</h5>
            <h2>{{ $summary['last7'] }}</h2>
        </div>
    </div>
</div>

{{-- FILTERS --}}
<form method="GET" class="row mb-4">

    <div class="col-md-3">
        <label>Quick Filter</label>
        <select name="filter" class="form-control">
            <option value="">-- None --</option>
            <option value="today" {{ request('filter')=='today'?'selected':'' }}>Today</option>
            <option value="yesterday" {{ request('filter')=='yesterday'?'selected':'' }}>Yesterday</option>
            <option value="last7" {{ request('filter')=='last7'?'selected':'' }}>Last 7 Days</option>
        </select>
    </div>

    <div class="col-md-3">
        <label>From Date</label>
        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
    </div>

    <div class="col-md-3">
        <label>To Date</label>
        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
    </div>

    <div class="col-md-3">
        <label>Salesperson</label>
        <select name="salesperson_id" class="form-control">
            <option value="">All</option>
            @foreach($salespersons as $sp)
                <option value="{{ $sp->id }}" {{ request('salesperson_id')==$sp->id?'selected':'' }}>
                    {{ $sp->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mt-3">
        <label>College</label>
        <select name="college" class="form-control">
            <option value="">All Colleges</option>
            @foreach($colleges as $clg)
                <option value="{{ $clg->college_name }}" {{ request('college')==$clg->college_name?'selected':'' }}>
                    {{ $clg->college_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3 mt-4">
        <button class="btn btn-primary mt-2">Apply Filter</button>
    </div>
</form>

{{-- RECORDS TABLE --}}
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Enquiry Name</th>
            <th>Mobile</th>
            <th>College</th>
            <th>Assigned To</th>
            <th>Assigned At</th>
        </tr>
    </thead>

    <tbody>
        @foreach($records as $row)
            <tr>
                <td>{{ $row->enquiry_name }}</td>
                <td>{{ $row->mobile }}</td>
                <td>{{ $row->college_name }}</td>
                <td>
    <a href="{{ url('admin/salespersons/' . $row->salesperson_id) }}">
        {{ $row->salesperson }}
    </a>
</td>
                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y, h:i A') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $records->links() }}

</div>
@endsection
