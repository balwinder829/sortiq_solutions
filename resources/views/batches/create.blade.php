@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Add Batch</h4>
        </div>
        <div class="card-body">

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('batches.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    
                    {{-- Batch Name --}}
                    <div class="form-group col-md-6">
                        <label>Batch Name</label>
                        <input type="text" 
                               name="batch_name" 
                               class="form-control @error('batch_name') is-invalid @enderror"
                               value="{{ old('batch_name') }}" required>
                        @error('batch_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Session --}}
                    <div class="form-group col-md-6">
                        <label>Session</label>
                        <select name="session_name" 
                                class="form-control @error('session_name') is-invalid @enderror" required>
                            <option value="" disabled selected>Choose Session</option>
                            @foreach($sessionsData as $session_n)
                                <option value="{{ $session_n->id }}"
                                    {{ old('session_name') == $session_n->id ? 'selected' : '' }}>
                                    {{ $session_n->session_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('session_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Start Time --}}
                    <div class="form-group col-md-6">
                        <label>Start Time</label>
                        <input type="time" 
                               name="start_time" 
                               class="form-control @error('start_time') is-invalid @enderror"
                               value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- End Time --}}
                    <div class="form-group col-md-6">
                        <label>End Time</label>
                        <input type="time" 
                               name="end_time" 
                               class="form-control @error('end_time') is-invalid @enderror"
                               value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Technology --}}
                    <div class="form-group col-md-6">
                        <label>Technology</label>
                        <select name="class_assign" 
                                class="form-control @error('class_assign') is-invalid @enderror" required>
                            <option value="" disabled selected>Choose Technology</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}"
                                    {{ old('class_assign') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_assign')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Trainer --}}
                    <div class="form-group col-md-6">
                        <label>Batch Assigned To</label>
                        <select name="batch_assign" 
                                class="form-control @error('batch_assign') is-invalid @enderror" required>
                            <option value="" disabled selected>Choose Trainer</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}"
                                    {{ old('batch_assign') == $trainer->id ? 'selected' : '' }}>
                                    {{ $trainer->activeUser->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('batch_assign')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Classroom --}}
                 

                    {{-- Duration --}}
                    <div class="form-group col-md-6">
                        <label>Duration</label>
                        <select name="duration" 
                                class="form-control @error('duration') is-invalid @enderror" required>
                            <option value="" disabled selected>Choose Duration</option>
                            @foreach($course_duration as $d)
                                <option value="{{ $d->duration }}" 
                                    {{ old('duration') == $d->duration ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('duration')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Batch Mode --}}
                    <div class="form-group col-md-6">
                        <label>Batch Mode</label>
                        <select name="batch_mode" 
                                class="form-control @error('batch_mode') is-invalid @enderror" required>

                            <option value="offline" {{ old('batch_mode') == 'offline' ? 'selected' : '' }}>
                                Offline
                            </option>

                            <option value="online" {{ old('batch_mode') == 'online' ? 'selected' : '' }}>
                                Online
                            </option>

                        </select>

                        @error('batch_mode')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                     {{-- Batch Status --}}
                    <div class="form-group col-md-6">
                        <label>Batch Status</label>
                        <select name="status" 
                                class="form-control @error('status') is-invalid @enderror" required>

                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>

                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>



                </div>

                <button type="submit" class="btn btn-primary">Save</button>

            </form>
        </div>
    </div>
</div>
@endsection
