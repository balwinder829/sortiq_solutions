@extends('layouts.public')

@section('content')

<div class="container my-5">
    <h2 class="mb-4 text-center">Enter Your Details to Access Test</h2>


<div class="row justify-content-center">
    <div class="col-md-6">

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('student.office-online.access') }}">
            @csrf
            <input type="hidden" name="slug" value="{{ request('slug') }}">

            {{-- Name --}}
            <div class="mb-3">
                <label class="fw-bold">SNo</label>
                <input type="text" name="student_sno" class="form-control"
                    value="{{ old('student_sno') }}" required>
            </div>

            {{-- Name --}}
            <div class="mb-3">
                <label class="fw-bold">Full Name</label>
                <input type="text" name="student_name" class="form-control"
                    value="{{ old('student_name') }}" required>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="fw-bold">Email</label>
                <input type="email" name="student_email" class="form-control"
                    value="{{ old('student_email') }}" required>
            </div>

            {{-- Mobile --}}
            <div class="mb-3">
                <label class="fw-bold">Mobile</label>
                <input type="text" name="student_mobile" class="form-control"
                    value="{{ old('student_mobile') }}"
                    required pattern="[0-9]{10}" maxlength="10">
            </div>

            {{-- Gender --}}
            <div class="mb-3">
                <label class="fw-bold">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="">Select</option>
                    <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Start Test
            </button>
        </form>

    </div>
</div>

 
   
</script>

@endsection
