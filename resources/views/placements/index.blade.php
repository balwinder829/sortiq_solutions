@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }
</style>

<div class="container">

    {{-- ADD BUTTON --}}
    <a href="{{ route('placements.create') }}"
       class="btn mb-3"
       style="background-color:#343957;color:white;">
        Add Placement
    </a>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTERS (DROPDOWNS + INPUTS) --}}
    <form method="GET" action="{{ route('placements.index') }}" class="mb-3">
        <div class="row g-2">

            {{-- COLLEGE --}}
            <div class="col-md-2">
                <select name="college_id" class="form-select">
                    <option value="">All Colleges</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}"
                            {{ request('college_id') == $college->id ? 'selected' : '' }}>
                            {{ $college->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STATE --}}
            <div class="col-md-2">
                <select name="state_id" class="form-select">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}"
                            {{ request('state_id') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- LOCATION --}}
            <div class="col-md-2">
                <input type="text"
                       name="location"
                       value="{{ request('location') }}"
                       class="form-control"
                       placeholder="Location">
            </div>

            {{-- TECH --}}
            <div class="col-md-2">
                <select name="tech" class="form-select">
                    <option value="">All Technology</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ request('tech') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- SESSION --}}
            <div class="col-md-2">
                <select name="session_id" class="form-select">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}"
                            {{ request('session_id') == $session->id ? 'selected' : '' }}>
                            {{ $session->session_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BUTTONS --}}
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('placements.index') }}"
                   class="btn btn-secondary w-100">
                    Reset
                </a>
            </div>

        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="placementTable">
            <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>College</th>
                <th>State</th>
                <th>Location</th>
                <th>Tech</th>
                <th>Session</th>
                <th>Media</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($placements as $placement)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $placement->student_name }}</td>

                    <td>{{ $placement->college_full_name ?? '-' }}</td>

                    <td>{{ $placement->state->name ?? '-' }}</td>

                    <td>{{ $placement->location ?? '-' }}</td>

                    <td>{{ $placement->course->course_name ?? '-' }}</td>

                    <td>{{ $placement->session->session_name ?? '-' }}</td>

                    {{-- MEDIA --}}
                    <td>
                        <span class="badge bg-primary">
                            {{ $placement->images->count() }} Images
                        </span>
                        <span class="badge bg-warning text-dark">
                            {{ $placement->videos->count() }} Videos
                        </span>
                    </td>

                    {{-- ACTIONS --}}
                    <td class="text-center" style="width: 120px;">
                        <a href="{{ route('placements.show', $placement->id) }}"
                           class="btn btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('placements.edit', $placement->id) }}"
                           class="btn btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('placements.destroy', $placement->id) }}"
                              method="POST"
                              style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm"
                                    onclick="return confirm('Delete this placement?')">
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
        $('#placementTable').DataTable({
            pageLength: 25,
            lengthMenu: [5, 10, 25, 50, 100],
        });
    });
</script>

<script>
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush
