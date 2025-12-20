@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Student Session</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('sessions.store') }}">
        @csrf

        <div class="form-group">
            <label>Session Name</label>
            <input type="text" name="session_name" value="{{ old('session_name') }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Session Start</label>
            <input type="date" name="session_start" value="{{ old('session_start') }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Session End</label>
            <input type="date" name="session_end" value="{{ old('session_end') }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="">Select</option>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
