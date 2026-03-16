@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Interview Question</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('interview-questions.store') }}">
@csrf

<div class="row">

    <div class="form-group col-md-6">
        <label>Round Type</label>
        <select name="round_type" class="form-control" required>
            <option value="">Select Round</option>
            <option value="hr">HR</option>
            <option value="technical">Technical</option>
            <option value="machine">Machine</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Experience Level</label>
        <select name="experience_level" class="form-control" required>
            <option value="">Select Experience</option>
            <option value="fresher">Fresher</option>
            <option value="1-3">1–3 Years</option>
            <option value="3-5">3–5 Years</option>
            <option value="5+">5+ Years</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Technology</label>
        <select name="technology_id" class="form-control">
            <option value="">-- Optional --</option>
            @foreach($technologies as $tech)
                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-12">
        <label>Question</label>
        <textarea name="question" class="form-control" required>{{ old('question') }}</textarea>
    </div>

    <div class="form-group col-md-12">
        <label>Answer</label>
        <textarea name="answer" id="answer"
                  class="form-control" required>{{ old('answer') }}</textarea>
    </div>

</div>

<button class="btn btn-primary mt-3">Save</button>
<a href="{{ route('interview-questions.index') }}"
   class="btn btn-secondary mt-3">Back</a>

</form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('answer');
</script>
@endpush
