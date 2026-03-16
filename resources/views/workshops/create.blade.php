@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Workshop</h3>
<!-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif -->

    <form method="POST" action="{{ route('workshops.store') }}">
        <div class="row">
        @csrf

        {{-- Title --}}
        <div class="form-group col-md-6">
            <label>Title</label>
            <input type="text" 
                   name="title" 
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}"
                   placeholder="Title" 
                   required>
            @error('title')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    
        {{-- COLLEGE --}}
        <div class="form-group col-md-6">
            <label>College</label>

            <select name="college_id"
                    class="form-select @error('college_id') is-invalid @enderror">

                <option value="" disabled>Select College</option>

                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ old('college_id') == $college->id ? 'selected' : '' }}>
                        {{ $college->FullName }}
                    </option>
                @endforeach

            </select>

            @error('college_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- <div class="form-group col-md-6">
            <label>College Type</label>
            <select name="college_type" class="form-control">
                <option value="0">Degree</option>
                <option value="1">Diploma</option>
            </select>
        </div> -->


        {{-- Name --}}
        <div class="form-group col-md-6">
            <label>Session</label>
            <input type="text" 
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ ucwords($activeSession->session_name) }}"
                   placeholder="Name" readonly 
                >
           
        </div>


         {{-- Name --}}
        <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text" 
                   name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Name" 
                   required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="form-group col-md-6">
            <label>Phone No</label>
            <input type="text" 
                   name="tp_hod_no" 
                   class="form-control @error('tp_hod_no') is-invalid @enderror"
                   value="{{ old('tp_hod_no') }}"
                   placeholder="Phone" 
                   required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
            @error('tp_hod_no')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Duration --}}
        <div class="form-group col-md-6">
            <label>Duration</label>
            <input type="text" 
                   name="duration"
                   class="form-control @error('duration') is-invalid @enderror"
                   value="{{ old('duration') }}"
                   placeholder="Duration" 
                   required>
            @error('duration')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- DATE --}}
        <div class="form-group col-md-6">
            <label>Date</label>
            <input type="date" name="date"
                   class="form-control"
                   value="{{ old('date') }}" required>
        </div>
        
 
         {{-- Status --}}

        @php $statuses = ['done','decided','meeting','hold','cancel']; @endphp

        <div class="form-group col-md-6">
            <label>Status</label>

            <select name="status"
                    class="form-control @error('status') is-invalid @enderror"
                    required>

                <option value="" disabled {{ old('status') ? '' : 'selected' }}>--Select--</option>

                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ old('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach

            </select>

            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        {{-- Workshop Type --}}

        @php $types = ['campus','office']; @endphp

        <div class="form-group col-md-6">
            <label>Workshop Type</label>

            <select name="type"
                    class="form-control @error('type') is-invalid @enderror"
                    required>

                <option value="" disabled {{ old('type') ? '' : 'selected' }}>--Select--</option>

                @foreach($types as $type)
                    <option value="{{ $type }}"
                        {{ old('type') == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach

            </select>

            @error('type')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        {{-- Event Type --}}

        @php $event_types = ['seminar','placement_drive','both']; @endphp

        <div class="form-group col-md-6">
            <label>Event Type</label>

            <select name="event_type"
                    class="form-control @error('event_type') is-invalid @enderror"
                    required>

                <option value="" disabled {{ old('event_type') ? '' : 'selected' }}>-- Select --</option>

                @foreach($event_types as $event_type)
                    <option value="{{ $event_type }}"
                        {{ old('event_type') == $event_type ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $event_type)) }}
                    </option>
                @endforeach

            </select>

            @error('event_type')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group col-md-12">
            <label>Description</label>

            <textarea name="description"
                      rows="4"
                      class="form-control @error('description') is-invalid @enderror"
                      placeholder="Enter workshop details...">{{ old('description') }}</textarea>

            @error('description')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

         <div class="row">
             <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary">Save</button>
         <a href="{{ route('workshops.index') }}" class="btn btn-secondary ml-2">Back</a>
    </div>
         </div>
</div>
    </form>
</div>
@endsection
