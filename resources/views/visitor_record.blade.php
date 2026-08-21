<!DOCTYPE html>
<html>
<head>
    <title>Office Visit Request</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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

    <!-- HEADER -->
    <div class="card-header d-flex align-items-center position-relative">

        <!-- LEFT LOGO -->
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/front_ss-logo.png') }}" style="height:35px;">
        </div>

        <!-- CENTER TITLE -->
        <div class="position-absolute w-100 text-center">
            <strong>Office Visit Request</strong>
        </div>

        <!-- RIGHT EMPTY -->
        <div class="ms-auto"></div>

    </div>

    <div class="card-body">

        <!-- SUCCESS MESSAGE -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('visitor.store') }}">
            @csrf

            <!-- Visitor Name / Mobile -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control @error('visitor_name') is-invalid @enderror"
                        name="visitor_name"
                        value="{{ old('visitor_name') }}"
                        placeholder="Full Name"
                        required
                    >

                    @error('visitor_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control @error('mobile') is-invalid @enderror"
                        name="mobile"
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


            <!-- Email / Organization -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email"
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
                        class="form-control @error('organization') is-invalid @enderror"
                        name="organization"
                        value="{{ old('organization') }}"
                        placeholder="Company / College / Organization"
                    >

                    @error('organization')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>


            <!-- Purpose / Person to Meet -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control @error('purpose') is-invalid @enderror"
                        name="purpose"
                        value="{{ old('purpose') }}"
                        placeholder="Purpose of Visit"
                        required
                    >

                    @error('purpose')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <input
                        type="text"
                        class="form-control @error('person_to_meet') is-invalid @enderror"
                        name="person_to_meet"
                        value="{{ old('person_to_meet') }}"
                        placeholder="Person You Want to Meet"
                    >

                    @error('person_to_meet')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>


            <!-- Visit Date / Visit Time -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <small class="text-muted">Preferred Visit Date</small>

                    <input
                        type="date"
                        class="form-control @error('visit_date') is-invalid @enderror"
                        name="visit_date"
                        value="{{ old('visit_date') }}"
                        min="{{ date('Y-m-d') }}"
                        required
                    >

                    @error('visit_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Preferred Visit Time</small>

                    <input
                        type="time"
                        class="form-control @error('visit_time') is-invalid @enderror"
                        name="visit_time"
                        value="{{ old('visit_time') }}"
                    >

                    @error('visit_time')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>


            <!-- Message -->
            <div class="row mb-3">

                <div class="col-12">

                    <textarea
                        class="form-control @error('message') is-invalid @enderror"
                        name="message"
                        rows="4"
                        placeholder="Additional Message">{{ old('message') }}</textarea>

                    @error('message')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            <!-- Submit -->
            <div class="text-center">

                <button type="submit" class="btn primary-btn px-5">
                    Submit Request
                </button>

            </div>

        </form>

    </div>
</div>

</div>
</section>

</body>
</html>