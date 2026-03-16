@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Generate Letter</h4>

     @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <form method="POST" action="{{ route('managements_letters.store') }}">
        @csrf

        <div class="row">

             {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <select
                    name="letter_type"
                    id="letterType"
                    class="form-control @error('letter_type') is-invalid @enderror"
                    required
                >
                    <option value="">Select Letter Type</option>
                    
                    <option value="custom" {{ old('letter_type')=='custom'?'selected':'' }}>Custom Office Letter</option>
                </select>
                @error('letter_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6" >
                <label>Letter Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    class="form-control @error('bond_period') is-invalid @enderror"
                    value="{{ old('title') }}"
                    placeholder="Title"
                    required
                >
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
 
             

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input
                    type="date"
                    name="issue_date"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', now()->toDateString()) }}"
                    class="form-control @error('issue_date') is-invalid @enderror"
                    required
                >
                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
 
              
            <div class="form-group col-md-12" id="bondTerms">
                <label>Content</label>
                <textarea
                    name="content"
                    id="content"
                    class="form-control @error('content') is-invalid @enderror" required
                >{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('managements_letters.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection


@push('scripts')
 
 <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('content');
</script> 

@endpush
