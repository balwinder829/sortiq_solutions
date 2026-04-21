@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h4 class="mb-4">📊 Ads Analytics Dashboard</h4>

    <div class="row g-3">

        <!-- Total Pages -->
        <div class="col-md-4">
            <a href="{{ route('pages.index') }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Total Pages</h6>
                        <h3>{{ $totalPages }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Internship Pages -->
        <div class="col-md-4">
            <a href="{{ route('pages.index') }}?type=internship" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Internship Pages</h6>
                        <h3>{{ $internshipPages }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Service Pages -->
        <div class="col-md-4">
            <a href="{{ route('pages.index') }}?type=service" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Service Pages</h6>
                        <h3>{{ $servicePages }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Internship Leads -->
        <div class="col-md-4">
            <a href="{{ route('internship-registrations.index') }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Internship Leads</h6>
                        <h3>{{ $totalInternshipLeads }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Service Leads -->
        <div class="col-md-4">
            <a href="{{ route('services-registrations.index') }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Service Leads</h6>
                        <h3>{{ $totalServiceLeads }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Internship Page -->
        <div class="col-md-4">
            <a href="{{ route('internship-registrations.index') }}?slug={{ $topInternshipPage->slug }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Internship Page</h6>
                        <h5>{{ $topInternshipPage->slug ?? '-' }}</h5>
                        <small>{{ $topInternshipPage->total ?? 0 }} leads</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Service Page -->
        <div class="col-md-4">
            <a href="{{ route('services-registrations.index') }}?slug={{ $topServicePage->slug }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Service Page</h6>
                        <h5>{{ $topServicePage->slug ?? '-' }}</h5>
                        <small>{{ $topServicePage->total ?? 0 }} leads</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Least Page -->
        <div class="col-md-4">
            <a href="{{ route('internship-registrations.index') }}?slug={{ $leastPage->slug }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Least Performing Page</h6>
                        <h5>{{ $leastPage->slug ?? '-' }}</h5>
                        <small>{{ $leastPage->total ?? 0 }} leads</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top College -->
        <div class="col-md-4">
            <a href="{{ route('internship-registrations.index') }}?college={{ $topCollege->college }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top College</h6>
                        <h5>{{ $topCollege->collegeData->college_name ?? '-' }}</h5>
                        <small>{{ $topCollege->total ?? 0 }} students</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Location -->
        <div class="col-md-4">
            <a href="{{ route('services-registrations.index') }}?location={{ $topLocation->location }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Location</h6>
                        <h5>{{ $topLocation->location ?? '-' }}</h5>
                        <small>{{ $topLocation->total ?? 0 }} leads</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Course -->
        <div class="col-md-4">
            <a href="{{ route('internship-registrations.index') }}?technology={{ $topCourse->technology }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Course</h6>
                        <h5>{{ $topCourse->courseData->course_name ?? '-' }}</h5>
                        <small>{{ $topCourse->total ?? 0 }} students</small>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection