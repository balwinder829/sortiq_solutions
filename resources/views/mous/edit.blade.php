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
                        class="form-control @error('college_id') is-invalid @enderror select2" required>
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

            {{-- Mobile No --}}
            <div class="form-group col-md-6 mb-3">
                <label for="mou_number">Mobile Number</label>
                <input type="text"
                       name="mou_number"
                       id="mou_number"
                       class="form-control @error('mou_number') is-invalid @enderror"
                       value="{{ old('mou_number', $mou->mou_number) }}"
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    onpaste="handlePaste(event)"
                    oninput="sanitizeContact(this)"
                    title="Enter a valid 10-digit mobile number">
                @error('mou_number')
                    <small class="text-danger">{{ $message }}</small>
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

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input
                    type="date"
                    name="issue_date"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', $mou->issue_date ? \Carbon\Carbon::parse($mou->issue_date)->toDateString() : now()->toDateString()) }}"
                    class="form-control @error('issue_date') is-invalid @enderror"
                    required
                >
                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- MOU Status --}}
            <div class="form-group col-md-6">
                <label>MOU Status</label>
                <select
                    name="mou_status"
                    id="mou_status"
                    class="form-control @error('mou_status') is-invalid @enderror"
                    required
                >
                    <option value="">Select MOU Status</option>
                    <option value="generated" {{ old('mou_status', $mou->mou_status) == 'generated' ? 'selected' : '' }}>Generated</option>
                    <option value="done" {{ old('mou_status', $mou->mou_status) == 'done' ? 'selected' : '' }}>Done</option>
                    <option value="pending" {{ old('mou_status', $mou->mou_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="email_sent" {{ old('mou_status', $mou->mou_status) == 'email_sent' ? 'selected' : '' }}>Email Sent</option>
                </select>
                @error('mou_status')
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
