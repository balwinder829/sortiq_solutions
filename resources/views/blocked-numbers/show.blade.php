@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Blocked Number Details</h2>

    <p><strong>Number:</strong> {{ $blocked->number }}</p>
    <p><strong>Blocked At:</strong> {{ $blocked->blocked_at }}</p>
    <p><strong>Total Occurrences:</strong> {{ $blocked->occurrence_count }}</p>

    <h4>Audit Log</h4>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Table</th>
            <th>Count</th>
        </tr>
        </thead>
        <tbody>
        @foreach($blocked->logs as $log)
            <tr>
                <td>{{ $log->table_name }}</td>
                <td>{{ $log->count }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.blocked-numbers.index') }}"
       class="btn btn-secondary">Back</a>
</div>
@endsection
