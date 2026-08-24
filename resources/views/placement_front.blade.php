<!DOCTYPE html>
<html>
<head>

    <title>Placed Students</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f5f7fb;
        }

        .hero-section {
            padding: 40px 0;
        }

        .main-card {
            border-radius: 15px;
            overflow: hidden;
            background: rgba(255,255,255,0.95);
        }

        .card-header {
            background-color: #343957;
            color: #fff;
            border-radius: 15px 15px 0 0;
            min-height: 70px;
        }

        /*
        |--------------------------------------------------------------------------
        | Main Content Padding
        |--------------------------------------------------------------------------
        */

        .main-card .card-body {
            padding: 25px;
        }


        /*
        |--------------------------------------------------------------------------
        | Student Card
        |--------------------------------------------------------------------------
        */

        .student-card {
            border: 1px solid #e1e4ea;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            background: #fff;
            transition: 0.2s ease;
        }

        .student-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }


        /*
        |--------------------------------------------------------------------------
        | Student Image
        |--------------------------------------------------------------------------
        */

        .student-image-wrapper {
            padding: 20px 20px 10px;
            text-align: center;
            background: #fff;
        }

        .student-image {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #e1e4ea;
            background: #f1f3f5;
        }


        /*
        |--------------------------------------------------------------------------
        | Card Body
        |--------------------------------------------------------------------------
        */

        .student-card-body {
            padding: 15px 20px 20px;
        }

        .student-name {
            font-size: 21px;
            font-weight: 600;
            color: #343957;
            margin-bottom: 14px;
             text-align: center;
        }

        .student-info {
            font-size: 14px;
            margin-bottom: 7px;
            color: #555;
        }

        .student-info strong {
            color: #343957;
        }


        /*
        |--------------------------------------------------------------------------
        | View Button
        |--------------------------------------------------------------------------
        */

        .view-btn {
            background-color: #343957;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
        }

        .view-btn:hover {
            background-color: #2a2f4a;
            color: #fff;
        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        .pagination-wrapper {
            margin-top: 30px;
        }

        .pagination {
            justify-content: center;
        }

        .pagination .page-link {
            color: #343957;
        }

        .pagination .active .page-link {
            background-color: #343957;
            border-color: #343957;
            color: #fff;
        }


        /*
        |--------------------------------------------------------------------------
        | No Data
        |--------------------------------------------------------------------------
        */

        .no-data {
            padding: 50px;
            text-align: center;
            color: #777;
        }

    </style>

</head>

<body>

<section class="hero-section">

<div class="container">

    <div class="main-card shadow">

        {{-- HEADER --}}
        <div class="card-header d-flex align-items-center position-relative">

            {{-- LEFT LOGO --}}
            <div class="d-flex align-items-center gap-2">

                <img
                    src="{{ asset('images/front_ss-logo.png') }}"
                    style="height:35px;"
                    alt="Logo"
                >

            </div>


            {{-- CENTER TITLE --}}
            <div class="position-absolute w-100 text-center">

                <strong>
                    Our Placed Students
                </strong>

            </div>


            {{-- RIGHT EMPTY --}}
            <div class="ms-auto"></div>

        </div>


        {{-- CONTENT --}}
        <div class="card-body">

            @if($placements->count())

                {{-- 3 CARDS PER ROW --}}
                <div class="row g-4">

                    @foreach($placements as $placement)

                        <div class="col-lg-4 col-md-6">

                            <div class="student-card">

                                {{-- STUDENT IMAGE --}}
                                <div class="student-image-wrapper">

                                    @if($placement->cover_image)

                                        <img
                                            src="{{ asset($placement->cover_image) }}"
                                            class="student-image"
                                            alt="{{ $placement->student_name }}"
                                        >

                                    @elseif($placement->images->count())

                                        <img
                                            src="{{ asset($placement->images->first()->image) }}"
                                            class="student-image"
                                            alt="{{ $placement->student_name }}"
                                        >

                                    @else

                                        <img
                                            src="{{ asset('images/placeholder_avatar.png') }}"
                                            class="student-image"
                                            alt="Student"
                                        >

                                    @endif

                                </div>


                                {{-- CARD BODY --}}
                                <div class="student-card-body">

                                    {{-- NAME --}}
                                    <div class="student-name">

                                        {{ $placement->student_name }}

                                    </div>


                                    {{-- COMPANY --}}
                                    <div class="student-info">

                                        <strong>
                                            Company:
                                        </strong>

                                        {{ $placement->companyRelation?->name ?? '-' }}

                                    </div>


                                    {{-- TECHNOLOGY --}}
                                    <div class="student-info">

                                        <strong>
                                            Technology:
                                        </strong>

                                        {{ $placement->course?->course_name ?? '-' }}

                                    </div>


                                    {{-- COLLEGE --}}
                                    <div class="student-info">

                                        <strong>
                                            College:
                                        </strong>

                                        {{ $placement->college_full_name ?: '-' }}

                                    </div>


                                    {{-- LOCATION --}}
                                    @if($placement->location)

                                        <div class="student-info">

                                            <strong>
                                                Location:
                                            </strong>

                                            {{ $placement->location }}

                                        </div>

                                    @endif


                                    {{-- PLACEMENT DATE --}}
                                    @if($placement->placement_date)

                                        <div class="student-info">

                                            <strong>
                                                Placement Date:
                                            </strong>

                                            {{ \Carbon\Carbon::parse($placement->placement_date)->format('d M Y') }}

                                        </div>

                                    @endif


                                    {{-- VIEW DETAILS --}}
                                    <!-- <div class="text-center mt-3">

                                        <a
                                            href="{{ route('placements.show', $placement->id) }}"
                                            class="btn view-btn"
                                        >
                                            View Details
                                        </a>

                                    </div> -->

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- PAGINATION --}}
                <div class="pagination-wrapper">

                    {{ $placements->onEachSide(1)->links('pagination::bootstrap-5') }}

                </div>


            @else

                <div class="no-data">

                    <h5>
                        No placed students found.
                    </h5>

                </div>

            @endif

        </div>

    </div>

</div>

</section>

</body>
</html>