@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Send Email</h3>

    <form method="POST" action="{{ route('admin.college-emails.store') }}">
        @csrf

        <div class="row">

        {{-- SELECT COLLEGES --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">College</label>
            <select name="college_ids[]" id="college-select" class="form-control select2" multiple required>
                <option value="">Select College</option>
                @foreach($colleges as $col)
                    <option value="{{ $col->id }}">{{ $col->FullName }}</option>
                @endforeach
            </select>
 
        </div>

        {{-- TYPE --}}
        <div class="form-group col-md-6 mt-3">
            <label>Recipient Type</label>

            <select name="type"
                class="form-control @error('type') is-invalid @enderror" required>

                <option value="hod">HOD</option>
                <option value="tpo">TPO</option>
                <option value="both">Both</option>

            </select>

            @error('type')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- PURPOSE --}}
        <div class="form-group col-md-6 mt-3">
            <label>Purpose</label>

            <select name="purpose_id"
                class="form-control @error('purpose_id') is-invalid @enderror" required>

                <option value="">Select Purpose</option>

                @foreach($purposes as $purpose)
                    <option value="{{ $purpose->id }}">
                        {{ $purpose->name }}
                    </option>
                @endforeach

            </select>

            @error('purpose_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- SENDER --}}
        <div class="form-group col-md-6 mt-3">
            <label>Sender Email</label>

            <select name="sender_id"
                class="form-control @error('sender_id') is-invalid @enderror" required>

                <option value="">Select Sender</option>

                @foreach($senders as $sender)
                    <option value="{{ $sender->id }}">
                        {{ $sender->email }}
                    </option>
                @endforeach

            </select>

            @error('sender_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- SUBJECT --}}
        <div class="form-group col-md-12 mt-3">
            <label>Subject</label>

            <input type="text"
                   name="subject"
                   class="form-control @error('subject') is-invalid @enderror"
                   value="{{ old('subject') }}"
                   placeholder="Enter subject"
                   required>

            @error('subject')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- BODY --}}
        <div class="form-group col-md-12 mt-3">
            <label>Email Body</label>

            <textarea name="body"
                      rows="6"
                      class="form-control @error('body') is-invalid @enderror"
                      placeholder="Enter email content..."
                      >{{ old('body') }}</textarea>

            @error('body')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- BUTTONS --}}
        <div class="row mt-4">
            <div class="form-group col-md-6">
                <button type="submit" class="btn btn-primary">Send Email</button>

                <a href="{{ route('admin.college-emails.index') }}"
                   class="btn btn-secondary ml-2">
                    Back
                </a>
            </div>
        </div>

        </div>
    </form>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Search college name",
            allowClear: true
        });
    });
</script>
<script>
let colleges = @json($colleges);

$('form').on('submit', function (e) {

    let selected = $('#college-select').val();

    if (!selected || selected.length === 0) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'No College Selected',
            text: 'Please select at least one college'
        });

        return;
    }

    let invalid = [];

    selected.forEach(function(id){

        let c = colleges.find(x => x.id == id);

        if (!c || !c.hod || !c.hod.emails || c.hod.emails.length === 0) {
            invalid.push(c?.full_name ?? c?.college_name ?? 'Unknown College');
        }

    });

    if (invalid.length > 0) {

        e.preventDefault();

        let html = `<div style="text-align:left;">
                        <p>Please add email for the following colleges:</p>
                        <ul style="padding-left:20px;">`;

        invalid.forEach(function(name){
            html += `<li>${name}</li>`;
        });

        html += `</ul></div>`;

        Swal.fire({
            icon: 'error',
            title: 'Missing Emails',
            html: html,
            confirmButtonText: 'OK'
        });
    }

});
</script>
@endpush