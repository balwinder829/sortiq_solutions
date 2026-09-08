@extends('layouts.app')

@section('content')
<div class="container">

<h3>Edit Test</h3>


@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif



<form method="POST" action="{{ route('admin.tests.update', $test->id) }}">
@csrf @method('PUT')

<div class="row">

    {{-- Title --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Title</label>
        <input type="text" name="title" class="form-control" 
               value="{{ $test->title }}" required>
    </div>

    {{-- Category --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Category</label>
        <select name="test_category_id" class="form-control">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" 
                        {{ $test->test_category_id == $cat->id ? 'selected':'' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- College --}}
    @php
        $selectedColleges = $test->links->pluck('college_id')->toArray();
    @endphp
    @php
    $selectedColleges = $test->links()
        ->orderBy('id') // or created_at
        ->pluck('college_id')
        ->toArray();

    // Step 1: Selected in correct order
    $selected = collect($selectedColleges)->map(function ($id) use ($colleges) {
        return $colleges->firstWhere('id', $id);
    })->filter();

    // Step 2: Remaining colleges
    $remaining = $colleges->whereNotIn('id', $selectedColleges);

    // Step 3: Merge
    $orderedColleges = $selected->concat($remaining);
@endphp
    <div class="col-md-6 mb-3">
        <label class="fw-bold">College</label>
        <!-- <select name="college_ids[]" class="form-control select2 select2-ordered"  multiple required>
            @foreach($colleges as $col)
                <option value="{{ $col->id }}" 
                        {{ in_array($col->id,$selectedColleges) ? 'selected' : '' }}>
                    {{ $col->FullName }}
                </option>
            @endforeach
        </select> -->
        <select name="college_ids[]" class="form-control select2 select2-ordered" multiple required>
    @foreach($orderedColleges as $col)
        <option value="{{ $col->id }}"
            {{ in_array($col->id, $selectedColleges) ? 'selected' : '' }}>
            {{ $col->FullName }}
        </option>
    @endforeach
</select>
    </div>

      
    
    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Status</label>
        <select name="status" class="form-control">
            <option value="draft"       {{ $test->status=='draft'?'selected':'' }}>Draft</option>
            <option value="published"   {{ $test->status=='published'?'selected':'' }}>Published</option>
            <option value="unpublished" {{ $test->status=='unpublished'?'selected':'' }}>Unpublished</option>
        </select>
    </div>

    {{-- Active --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Active</label>
        <select name="is_active" class="form-control" required>
            <option value="1" {{ old('is_active', $test->is_active) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $test->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

     

    {{-- Exam Start Time --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Exam Start Time</label>
        <input type="date"
               name="exam_start_at"
               class="form-control"
               value="{{ optional($test->exam_start_at)->format('Y-m-d') }}"
               required>
    </div>

    {{-- Exam End Time --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Exam End Time</label>
        <input type="date"
               name="exam_end_at"
               class="form-control"
               value="{{ optional($test->exam_end_at)->format('Y-m-d') }}"
               required>
    </div>

     

</div>
<div class="form-group col-md-6">
    <button class="btn btn-primary">Update Test</button>
    <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary ml-2">Back</a>
</div>
</form>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- <script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Search college name",
            allowClear: true
        });
    });
</script> -->

<script>
$(document).ready(function () {

    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: "Search college name",
        allowClear: true,
        closeOnSelect: false
    });

    // 🔥 KEY FIX: reorder DOM on selection
    $('.select2-ordered').on('select2:select', function (e) {
        let element = $(this);
        let selectedId = e.params.data.id;

        let option = element.find('option[value="' + selectedId + '"]');

        // move selected option to the end
        option.detach();
        element.append(option);

        element.trigger('change.select2');
    });

    $('.select2-ordered').on('select2:unselect', function (e) {
        let element = $(this);
        let unselectedId = e.params.data.id;

        let option = element.find('option[value="' + unselectedId + '"]');

        // move unselected option back to top (optional)
        option.detach();
        element.prepend(option);

        element.trigger('change.select2');
    });

});
</script>

@endpush
