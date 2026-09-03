<!DOCTYPE html>
<html>
<head>
    <title>Student Feedback</title>

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

            <img
                src="{{ asset('images/front_ss-logo.png') }}"
                style="height:35px;"
            >

        </div>

        <!-- CENTER TITLE -->
        <div class="position-absolute w-100 text-center">

            <strong>Student Feedback</strong>

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
        <form
            method="POST"
            action="{{ route('student-feedback.store') }}"
        >

            @csrf


            <!-- NAME / MOBILE -->
            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Full Name"
                        required
                    >

                    @error('name')

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


            <!-- EMAIL / COURSE -->
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
                        class="form-control @error('course') is-invalid @enderror"
                        name="course"
                        value="{{ old('course') }}"
                        placeholder="Course / Technology"
                    >

                    @error('course')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>


            <!-- BATCH -->
            <div class="row mb-3">

                <div class="col-md-6">

                    <input
                        type="text"
                        class="form-control @error('batch') is-invalid @enderror"
                        name="batch"
                        value="{{ old('batch') }}"
                        placeholder="Batch"
                    >

                    @error('batch')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>


            <!-- MESSAGE -->
            <div class="row mb-3">

                <div class="col-12">

                    <textarea
                        class="form-control @error('message') is-invalid @enderror"
                        name="message"
                        rows="5"
                        placeholder="Write your feedback here..."
                        required
                    >{{ old('message') }}</textarea>

                    @error('message')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>


            <!-- SUBMIT -->
            <div class="text-center">

                <button
                    type="submit"
                    class="btn primary-btn px-5"
                >
                    Submit Feedback
                </button>

            </div>

        </form>

    </div>

</div>

</div>

</section>

</body>
</html>