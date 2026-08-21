<!DOCTYPE html>
<html>
<head>

    <title>Schedule Interview</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        .hero-section {
            padding: 40px 0;
            background: #f5f7fb;
        }

        .card {
            border-radius: 15px;
            overflow: hidden;
            background: rgba(255,255,255,0.95);
        }

        .card-header {
            background-color: #343957;
            color: #fff;
            border-radius: 15px 15px 0 0;
        }

        .primary-btn {
            background-color: #343957;
            color: #fff;
            border: none;
            padding: 10px 25px;
        }

        .primary-btn:hover {
            background-color: #2a2f4a;
            color: #fff;
        }

        .invalid-feedback {
            display: block;
        }

    </style>

</head>

<body>

<section class="hero-section">

<div class="container">

<div class="card shadow">

    {{-- HEADER --}}
    <div class="card-header d-flex align-items-center position-relative">

        {{-- LEFT LOGO --}}
        <div class="d-flex align-items-center gap-2">

            <img
                src="{{ asset('images/front_ss-logo.png') }}"
                style="height:35px;"
            >

        </div>

        {{-- CENTER TITLE --}}
        <div class="position-absolute w-100 text-center">

            <strong>
                Schedule Interview
            </strong>

        </div>

        <div class="ms-auto"></div>

    </div>


    <div class="card-body">

        {{-- SUCCESS --}}
        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('interview.store') }}"
            enctype="multipart/form-data"
        >

            @csrf


            {{-- Candidate Name / Mobile --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        name="candidate_name"
                        class="form-control @error('candidate_name') is-invalid @enderror"
                        value="{{ old('candidate_name') }}"
                        placeholder="Full Name"
                        required
                    >

                    @error('candidate_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-6">

                    <input
                        type="text"
                        name="mobile"
                        class="form-control @error('mobile') is-invalid @enderror"
                        value="{{ old('mobile') }}"
                        placeholder="Mobile Number"
                        minlength="10"
                        maxlength="10"
                        pattern="[0-9]{10}"
                        title="Enter a valid 10-digit mobile number"
                        required
                    >

                    @error('mobile')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Email / Location --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="Email Address"
                    >

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-6">

                    <input
                        type="text"
                        name="current_location"
                        class="form-control @error('current_location') is-invalid @enderror"
                        value="{{ old('current_location') }}"
                        placeholder="Current Location"
                    >

                    @error('current_location')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Current Company / Position --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        name="current_company"
                        class="form-control @error('current_company') is-invalid @enderror"
                        value="{{ old('current_company') }}"
                        placeholder="Current Company"
                    >

                    @error('current_company')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-6">

                    <input
                        type="text"
                        name="position_applied"
                        class="form-control @error('position_applied') is-invalid @enderror"
                        value="{{ old('position_applied') }}"
                        placeholder="Position Applied For"
                        required
                    >

                    @error('position_applied')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Qualification / Experience --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        name="qualification"
                        class="form-control @error('qualification') is-invalid @enderror"
                        value="{{ old('qualification') }}"
                        placeholder="Highest Qualification"
                    >

                    @error('qualification')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="col-md-6">

                    <input
                        type="text"
                        name="experience"
                        class="form-control @error('experience') is-invalid @enderror"
                        value="{{ old('experience') }}"
                        placeholder="Experience (e.g. Fresher / 2 Years)"
                    >

                    @error('experience')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Technology --}}
            <div class="row mb-3">

                <div class="col-md-12">

                    <input
                        type="text"
                        name="technology_known"
                        class="form-control @error('technology_known') is-invalid @enderror"
                        value="{{ old('technology_known') }}"
                        placeholder="Technology Known (e.g. PHP, Laravel, MySQL, React)"
                    >

                    @error('technology_known')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Preferred Date / Time --}}
            <div class="row mb-3">

                <div class="col-md-6">

                    <small class="text-muted">
                        Preferred Interview Date
                    </small>

                    <input
                        type="date"
                        id="preferred_date"
                        name="preferred_date"
                        class="form-control @error('preferred_date') is-invalid @enderror"
                        value="{{ old('preferred_date') }}"
                        min="{{ date('Y-m-d') }}"
                        required
                    >

                    @error('preferred_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- JavaScript validation error --}}
                    <div
                        id="preferred_date_js_error"
                        class="invalid-feedback"
                    ></div>

                    <small class="text-muted">
                        Sunday is not available.
                    </small>

                </div>


                <div class="col-md-6">

                    <small class="text-muted">
                        Preferred Interview Time
                    </small>

                    <input
                        type="time"
                        name="preferred_time"
                        class="form-control @error('preferred_time') is-invalid @enderror"
                        value="{{ old('preferred_time') }}"
                    >

                    @error('preferred_time')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Resume --}}
            <div class="row mb-3">

                <div class="col-md-12">

                    <small class="text-muted">
                        Resume (PDF, DOC, DOCX - Max 5 MB)
                    </small>

                    <input
                        type="file"
                        name="resume"
                        class="form-control @error('resume') is-invalid @enderror"
                        accept=".pdf,.doc,.docx"
                    >

                    @error('resume')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Message --}}
            <div class="row mb-3">

                <div class="col-md-12">

                    <textarea
                        name="message"
                        rows="4"
                        class="form-control @error('message') is-invalid @enderror"
                        placeholder="Additional Message"
                    >{{ old('message') }}</textarea>

                    @error('message')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- Submit --}}
            <div class="text-center">

                <button
                    type="submit"
                    class="btn primary-btn px-5"
                >
                    Submit Interview Request
                </button>

            </div>

        </form>

    </div>

</div>

</div>

</section>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const dateInput = document.getElementById('preferred_date');
    const dateError = document.getElementById('preferred_date_js_error');

    dateInput.addEventListener('change', function () {

        // Clear previous JS error
        dateError.textContent = '';
        dateInput.classList.remove('is-invalid');

        if (!this.value) {
            return;
        }

        const selectedDate = new Date(this.value + 'T00:00:00');

        // Sunday = 0
        if (selectedDate.getDay() === 0) {

            dateError.textContent =
                'Sunday is not available for interviews.';

            dateInput.classList.add('is-invalid');

            this.value = '';
        }

    });

});

</script>

</body>
</html>