@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Sales Employee Performance</h3>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>Employee</th>
                <th>Total Follow-ups</th>
                <th>Total Calls</th>
                <th>Conversions (Joined)</th>
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $emp)
            <tr>
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->followups }}</td>
                <td>{{ $emp->calls }}</td>
                <td>{{ $emp->conversions }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>

</div>
@endsection
