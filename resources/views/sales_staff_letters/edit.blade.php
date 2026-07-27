@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Edit Sales Staff Letter</h4>

    <form method="POST"
          action="{{ route('sales-staff-letters.update', $letter) }}">

        @csrf
        @method('PUT')

        <div class="row">

            {{-- Sales Staff --}}
            <div class="form-group col-md-6">
                <label>Sales Staff</label>

                <select name="sales_staff_id"
                        class="form-control"
                        required>

                    @foreach($salesStaff as $staff)

                        <option value="{{ $staff->id }}"
                            {{ $letter->sales_staff_id == $staff->id ? 'selected' : '' }}>

                            {{ $staff->name }}

                        </option>

                    @endforeach

                </select>
            </div>

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select name="letter_type"
                        class="form-control"
                        required>

                    <option value="trainer_consent"
                        {{ $letter->letter_type == 'trainer_consent' ? 'selected' : '' }}>

                        Sales Staff Consent Letter

                    </option>

                </select>
            </div>

            {{-- Employee ID --}}
            <div class="form-group col-md-6 mt-3">
                <label>Employee ID</label>

                <input
                    type="text"
                    name="emp_id"
                    class="form-control"
                    value="{{ old('emp_id', $letter->emp_id) }}"
                    required
                >
            </div>

            {{-- Month Of Deduction --}}
            <div class="form-group col-md-6 mt-3">
                <label>Month Of Deduction</label>

                <input
                    type="text"
                    name="month_of_deduction"
                    class="form-control"
                    value="{{ old('month_of_deduction', $letter->month_of_deduction) }}"
                    required
                >
            </div>

            {{-- Year Of Deduction --}}
            <div class="form-group col-md-6 mt-3">
                <label>Year Of Deduction</label>

                <input
                    type="text"
                    name="year_of_deduction"
                    class="form-control"
                    value="{{ old('year_of_deduction', $letter->year_of_deduction) }}"
                    required
                >
            </div>

            {{-- Sale Target --}}
            <div class="form-group col-md-6 mt-3">
                <label>Sale Target</label>

                <input
                    type="text"
                    name="sale_target"
                    class="form-control"
                    value="{{ old('sale_target', $letter->sale_target) }}"
                    required
                >
            </div>

            {{-- Amount Of Deduction --}}
            <div class="form-group col-md-6 mt-3">
                <label>Amount Of Deduction</label>

                <input
                    type="number"
                    step="0.01"
                    name="amount_of_deduction"
                    class="form-control"
                    value="{{ old('amount_of_deduction', $letter->amount_of_deduction) }}"
                    required
                >
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6 mt-3">
                <label>Issue Date</label>

                <input
                    type="date"
                    name="issue_date"
                    class="form-control"
                    value="{{ old('issue_date', $letter->issue_date) }}"
                    required
                >
            </div>


              {{-- Letter Content --}}
            <div class="form-group col-md-12" id="letter_content_field">
                <label>Letter Content</label>
                <textarea
                    name="letter_content"
                    id="editor"
                    class="form-control @error('letter_content') is-invalid @enderror"
                    rows="15">{!! old('letter_content', $letter->letter_content ?: ($template->content ?? '')) !!}</textarea>

                @error('letter_content')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">
            Update
        </button>

        <a href="{{ route('sales-staff-letters.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>

</div>

@endsection


@push('scripts')
<!-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> -->
 <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('editor');
</script>

@endpush