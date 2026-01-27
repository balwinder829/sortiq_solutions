@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit MOU</h4>

    <form method="POST" action="{{ route('mous.update', $mou) }}">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label>College</label>
                <select name="college_id"
                        class="form-control @error('college_id') is-invalid @enderror" required>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}"
                            {{ old('college_id',$mou->college_id)==$college->id?'selected':'' }}>
                            {{ $college->college_name }}
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>MOU Title</label>
                <input type="text"
                       name="mou_title"
                       value="{{ old('mou_title',$mou->mou_title) }}"
                       class="form-control @error('mou_title') is-invalid @enderror" required>
                @error('mou_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Email(s)</label>
                <input type="text"
                       name="email_to"
                       value="{{ old('email_to',$mou->email_to) }}"
                       class="form-control @error('email_to') is-invalid @enderror" required>
                @error('email_to')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Start Date</label>
                <input type="date"
                       name="start_date"
                       value="{{ old('start_date',$mou->start_date->toDateString()) }}"
                       class="form-control @error('start_date') is-invalid @enderror" required>
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>End Date</label>
                <input type="date"
                       name="end_date"
                       id="endDate"
                       value="{{ old('end_date', $mou->end_date->toDateString()) }}"
                       class="form-control @error('end_date') is-invalid @enderror">
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <div class="form-group col-md-12">
                <label>Description <span class="text-danger">*</span></label>

                <textarea name="description"
                    class="form-control @error('description') is-invalid @enderror"
                    maxlength="350"
                    required>{{ old('description', $mou->description) }}</textarea>

                <small class="text-muted">Maximum 350 characters</small>

                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('mous.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const startDateInput = document.querySelector('input[name="start_date"]');
    const endDateInput   = document.getElementById('endDate');

    let endDateManuallyChanged = false;

    // Detect manual change of end date
    endDateInput.addEventListener('input', function () {
        endDateManuallyChanged = true;
    });

    function autoCalculateEndDate() {
        if (!startDateInput.value) return;

        // If user manually changed end date, do not override
        if (endDateManuallyChanged) return;

        const startDate = new Date(startDateInput.value);
        startDate.setFullYear(startDate.getFullYear() + 3);

        const yyyy = startDate.getFullYear();
        const mm   = String(startDate.getMonth() + 1).padStart(2, '0');
        const dd   = String(startDate.getDate()).padStart(2, '0');

        endDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    startDateInput.addEventListener('change', autoCalculateEndDate);
});
</script>
<!-- <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('description');
</script>  -->

@endpush
