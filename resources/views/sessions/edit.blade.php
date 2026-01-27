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
            <label>Session Display Name</label>
            <input type="text" name="session_display_name" value="{{ old('session_display_name', $session->session_display_name) }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Session Start</label>
            <input type="date" name="session_start" value="{{ old('session_start', $session->session_start->format('Y-m-d')) }}" class="form-control" id="start_date"  required>
            <small class="text-danger d-none" id="start_error">
                    Sunday is not allowed
                </small>
        </div>

        <div class="form-group">
            <label>Session End</label>
            <input type="date" name="session_end" id="end_date" value="{{ old('session_end', $session->session_end->format('Y-m-d')) }}" class="form-control" required>
             <small class="text-danger d-none" id="end_error">
                    Sunday is not allowed
                </small>
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
<script>
    function blockSunday(input, errorId) {
    input.addEventListener('change', function () {
        if (!this.value) return;

        const day = new Date(this.value).getDay(); // 0 = Sunday

        if (day === 0) {
            this.value = '';
            document.getElementById(errorId).classList.remove('d-none');
        } else {
            document.getElementById(errorId).classList.add('d-none');
        }
    });
}

blockSunday(document.getElementById('start_date'), 'start_error');
blockSunday(document.getElementById('end_date'), 'end_error');
</script>
@endsection
