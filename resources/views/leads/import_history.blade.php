@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container">
    <h3>Excel Import History</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Batch ID</th>
                <th>Uploaded By</th>
                <th>File Name</th>
                <th>Total Imported</th>
                <th>View</th>
            </tr>
        </thead>

        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ date('d M Y h:i A', strtotime($log->created_at)) }}</td>
                <td>{{ $log->batch_id }}</td>
                <td>{{ $log->username }}</td>
                <td>{{ $log->file_name }}</td>
                <td>{{ $log->total_imported }}</td>
                <td>
                    <a href="{{ route('leads.import.batch', $log->batch_id) }}" class="btn btn-sm btn-info">
                        View Leads
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>
@endsection
