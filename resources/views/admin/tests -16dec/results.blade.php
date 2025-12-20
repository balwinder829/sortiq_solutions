@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('content')
<div class="container my-5">
    <h2 class="mb-4 text-primary">Student Test Results</h2>

    <!-- Filters Form -->
    <form method="GET" action="{{ route('admin.tests.results', $test->id) }}" class="row g-3 mb-4 align-items-center">
        <div class="col-md-2">
            <input type="text" name="sno" value="{{ request('sno') }}" class="form-control" placeholder="Serial No">
        </div>
        <div class="col-md-2">
            <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Student Name">
        </div>
        <div class="col-md-2">
            <input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="Email">
        </div>
        <div class="col-md-2">
            <input type="text" name="test" value="{{ $test->title }}" class="form-control" placeholder="Test Name">
        </div>
        <div class="col-md-2">
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="top_scorer" value="1" id="topScorer" {{ request('top_scorer') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="topScorer">Top Scorer</label>
            </div>
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <div class="col-md-1 d-grid">
            <a href="{{ route('admin.tests.results', $test->id) }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>


    <!-- Results Table -->
    <table class="table table-bordered table-striped" id="resultsTable">
        <thead class="table-light">
            <tr>
                <th>Serial No</th>
                <th>Student Name</th>
                <th>Student Email</th>
                <th>Test Name</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentTests as $result)
                <tr>
                    <td>{{ $result->sno }}</td>
                    <td>{{ $result->student_name }}</td>
                    <td>{{ $result->student_email }}</td>
                    <td>{{ $result->test->title }}</td>
                    <td>{{ (int)$result->score }}/{{ $result->test->questions->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


