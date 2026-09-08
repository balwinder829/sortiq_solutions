@extends('layouts.app')

@section('content')
<div class="container">


<div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Create Test</h1>
        </div>
        <!-- <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('admin.tests.index') }}" class="btn  btn-primary mb-3">Back</a>
            </div>
        </div> -->
    </div>


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




<form method="POST" action="{{ route('admin.tests.store') }}">
@csrf

<div class="row">

    {{-- Title --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    {{-- Category --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Category</label>
        <select name="test_category_id" class="form-control" required>
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- College --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">College</label>
        <select name="college_ids[]" class="form-control select2 select2-ordered" multiple required>
            <option value="">Select College</option>
            @foreach($colleges as $col)
                <option value="{{ $col->id }}">{{ $col->FullName }}</option>
            @endforeach
        </select>
    </div> 

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Status</label>
        <select name="status" class="form-control" required>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="unpublished">Unpublished</option>
        </select>
    </div>

     <div class="col-md-6 mb-3">
        <label class="fw-bold">Active</label>
        <select name="is_active" class="form-control" required>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
            
        </select>
    </div>

     
    {{-- Exam Start Time --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Exam Start Time</label>
        <input type="date"
               name="exam_start_at"
               class="form-control"
               required>
    </div>

    {{-- Exam End Time --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Exam End Time</label>
        <input type="date"
               name="exam_end_at"
               class="form-control"
               required>
    </div>

</div>

<div class="form-group col-md-6">
    <button class="btn btn-primary">Save Test</button>
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