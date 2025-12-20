@extends('layouts.app')

@section('content')
<div class="container">

<h3>Create Placement</h3>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<a href="{{ route('placements.index') }}" class="btn mb-3"
   style="background-color:#343957;color:white;">Back</a>

<form method="POST"
      action="{{ route('placements.store') }}"
      enctype="multipart/form-data">

    @csrf

    {{-- STUDENT NAME --}}
    <div class="mb-3">
        <label>Student Name</label>
        <input type="text" name="student_name"
               class="form-control"
               value="{{ old('student_name') }}" required>
    </div>

    {{-- COLLEGE --}}
    <div class="mb-3">
        <label>College</label>
        <select name="college_name" class="form-select" required>
            <option value="">Select College</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}"
                    {{ old('college_name') == $college->id ? 'selected' : '' }}>
                    {{ $college->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- STATE --}}
    <div class="mb-3">
        <label>State</label>
        <select name="state_id" class="form-select" required>
            <option value="">Select State</option>
            @foreach($states as $state)
                <option value="{{ $state->id }}"
                    {{ old('state_id') == $state->id ? 'selected' : '' }}>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- LOCATION --}}
    <div class="mb-3">
        <label>Location</label>
        <input type="text" name="location"
               class="form-control"
               value="{{ old('location') }}">
    </div>


    {{-- TECHNOLOGY --}}
    <div class="mb-3">
        <label>Course</label>
        <select name="tech" class="form-select">
            <option value="">Select Technology</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}"
                    {{ old('tech') == $course->id ? 'selected' : '' }}>
                    {{ $course->course_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- SESSION --}}
    <div class="mb-3">
        <label>Session</label>
        <select name="session_id" class="form-select" required>
            <option value="">Select Session</option>
            @foreach($sessions as $session)
                <option value="{{ $session->id }}"
                    {{ old('session_id') == $session->id ? 'selected' : '' }}>
                    {{ $session->session_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PLACEMENT DATE --}}
    <div class="mb-3">
        <label>Placement Date</label>
        <input type="date" name="placement_date"
               class="form-control"
               value="{{ old('placement_date') }}" required>
    </div>

    {{-- COMPANY (DROPDOWN) --}}
    <div class="mb-3">
        <label>Company</label>
        <select name="company" class="form-select" required>
            <option value="">Select Company</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}"
                    {{ old('company') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PHONE --}}
    <div class="mb-3">
    <label>Phone No</label>
    <input type="tel"
           name="phone_no"
           class="form-control"
           value="{{ old('phone_no') }}"
           maxlength="10"
           pattern="[0-9]{10}"
           inputmode="numeric"
           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
           placeholder="10 digit number"
           required>
</div>


    {{-- DESCRIPTION --}}
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description"
                  class="form-control">{{ old('description') }}</textarea>
    </div>

    <hr>

    {{-- DRAG DROP UPLOAD --}}
    <div class="mb-3">
        <label>Upload Images / Videos <span class="text-danger">*</span></label>

        <div id="dropArea"
             style="border:2px dashed #6b51df;padding:30px;text-align:center;
                    cursor:pointer;border-radius:8px;">
            <p class="text-muted mb-1">Drag & Drop files here</p>
            <p class="text-muted">or click to browse</p>
        </div>

        <input type="file"
               name="media[]"
               id="mediaInput"
               multiple
               accept="image/*,video/*"
               style="display:none;">
    </div>

    {{-- COVER IMAGE --}}
    <div class="mb-3">
        <label>Choose Cover Image (required)</label>
        <div id="coverImageOptions" class="row"></div>
    </div>

    {{-- PREVIEW --}}
    <div id="previewArea" class="row"></div>

    <button class="btn mt-3"
            style="background-color:#343957;color:white;">
        Save Placement
    </button>

</form>

</div>
@endsection

@push('scripts')
<script>
let dropArea       = document.getElementById('dropArea');
let mediaInput     = document.getElementById('mediaInput');
let previewArea    = document.getElementById('previewArea');
let coverOptions   = document.getElementById('coverImageOptions');

dropArea.addEventListener('click', () => mediaInput.click());
dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.style.background="#f5f0ff"; });
dropArea.addEventListener('dragleave', () => dropArea.style.background="white");

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.style.background="white";
    mediaInput.files = e.dataTransfer.files;
    previewFiles(e.dataTransfer.files);
});

mediaInput.addEventListener('change', function(){
    previewFiles(this.files);
});

function previewFiles(files){
    previewArea.innerHTML = "";
    coverOptions.innerHTML = "";

    Array.from(files).forEach(file => {
        let ext = file.name.split(".").pop().toLowerCase();
        let url = URL.createObjectURL(file);

        let div = document.createElement("div");
        div.classList.add("col-md-3", "mb-3");

        if(["jpg","jpeg","png","webp","gif"].includes(ext)){
            div.innerHTML = `
                <div class="card">
                    <img src="${url}" style="width:100%;height:150px;object-fit:cover;">
                </div>
                <div class="text-center mt-1">
                    <input type="radio" name="cover_image" value="${file.name}" required>
                    <label class="small">Set as cover</label>
                </div>
            `;
        } else {
            div.innerHTML = `
                <div class="card">
                    <video src="${url}" style="width:100%;height:150px;object-fit:cover;" muted></video>
                </div>
                <p class="text-danger small text-center">(Cannot be cover)</p>
            `;
        }

        previewArea.appendChild(div);
    });
}
</script>
@endpush