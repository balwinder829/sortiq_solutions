@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')

<div class="container mt-4">

    <h3>Attendance Panel</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-4 shadow-sm">

        @if(!$attendanceToday)
            <form action="{{ route('attendance.checkIn') }}" method="POST">
                @csrf
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <button class="btn btn-success btn-lg w-100">Check In</button>
            </form>

        @elseif(!$attendanceToday->logout_time)
            <form action="{{ route('attendance.checkOut') }}" method="POST">
                @csrf
                <button class="btn btn-danger btn-lg w-100">Check Out</button>
            </form>

        @else
            <div class="alert alert-info text-center">
                Attendance Completed for Today.
            </div>
        @endif

    </div>

</div>

@endsection
@push('scripts')
<script>
navigator.geolocation?.getCurrentPosition(
    pos => {
        latitude.value = pos.coords.latitude;
        longitude.value = pos.coords.longitude;
    },
    () => {},
    { enableHighAccuracy: true }
);
</script>
@endpush