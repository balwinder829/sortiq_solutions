@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Student Session</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sessions.update', $session->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Session Name</label>
            <input type="text" name="session_name" value="{{ old('session_name', $session->session_name) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Session Start</label>
            <input type="date" name="session_start" value="{{ old('session_start', $session->session_start->format('Y-m-d')) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Session End</label>
            <input type="date" name="session_end" value="{{ old('session_end', $session->session_end->format('Y-m-d')) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="active" {{ old('status', $session->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $session->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Session</button>
    </form>
</div>
@endsection
