@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Interview Question</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST"
      action="{{ route('interview-questions.update', $interview_question) }}?{{ http_build_query(request()->query()) }}">
@csrf
@method('PUT')

<div class="row">

    <div class="form-group col-md-6">
        <label>Round Type</label>
        <select name="round_type" class="form-control" required>
            <option value="hr" {{ $interview_question->round_type=='hr'?'selected':'' }}>HR</option>
            <option value="technical" {{ $interview_question->round_type=='technical'?'selected':'' }}>Technical</option>
            <option value="machine" {{ $interview_question->round_type=='machine'?'selected':'' }}>Machine</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Experience Level</label>
        <select name="experience_level" class="form-control" required>
            @foreach(['fresher','1-3','3-5','5+'] as $e)
                <option value="{{ $e }}"
                    {{ $interview_question->experience_level==$e?'selected':'' }}>
                    {{ $e }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Technology</label>
        <select name="technology_id" class="form-control">
            <option value="">-- Optional --</option>
            @foreach($technologies as $tech)
                <option value="{{ $tech->id }}"
                    {{ $interview_question->technology_id==$tech->id?'selected':'' }}>
                    {{ $tech->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-12">
        <label>Question</label>
        <textarea name="question"
                  class="form-control" required>{{ old('question',$interview_question->question) }}</textarea>
    </div>

    <div class="form-group col-md-12">
        <label>Answer</label>
        <textarea name="answer" id="answer"
                  class="form-control" required>{{ old('answer',$interview_question->answer) }}</textarea>
    </div>

</div>

<button class="btn btn-primary mt-3">Update</button>
<!-- <a href="{{ route('interview-questions.index') }}"
   class="btn btn-secondary mt-3">Back</a>
 -->
<a href="{{ route('interview-questions.index', request()->query()) }}" class="btn btn-secondary mt-3">
    Back
</a>

</form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('answer');
</script>
@endpush
