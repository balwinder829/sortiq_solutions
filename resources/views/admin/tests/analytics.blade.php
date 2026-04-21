@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h4 class="mb-4">🧪 Test Analytics Dashboard</h4>

    <div class="row g-3">

        <!-- Total Tests -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Total Tests</h6>
                        <h3>{{ $totalTests }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Active Tests -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?status=active" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Active Tests</h6>
                        <h3>{{ $activeTests }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Inactive Tests -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?status=inactive" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Inactive Tests</h6>
                        <h3>{{ $inactiveTests }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Online Tests -->
        <!-- <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?mode=online" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Online Tests</h6>
                        <h3>{{ $onlineTests }}</h3>
                    </div>
                </div>
            </a>
        </div> -->

        <!-- Offline Tests -->
       <!--  <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?mode=offline" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Offline Tests</h6>
                        <h3>{{ $offlineTests }}</h3>
                    </div>
                </div>
            </a>
        </div> -->

        <!-- Total Attempts -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Total Attempts</h6>
                        <h3>{{ $totalAttempts }}</h3>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Test -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?test_id={{ $topTest->test_id ?? '' }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Test</h6>
                        <h5>{{ $topTest->test->title ?? '-' }}</h5>
                        <small>{{ $topTest->total ?? 0 }} attempts</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Least Test -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?test_id={{ $leastTest->test_id ?? '' }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Least Test</h6>
                        <h5>{{ $leastTest->test->title ?? '-' }}</h5>
                        <small>{{ $leastTest->total ?? 0 }} attempts</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top College -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?college_id={{ $topCollege->college_id ?? '' }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top College</h6>
                        <h5>{{ $topCollege->college->college_name ?? '-' }}</h5>
                        <small>{{ $topCollege->total ?? 0 }} students</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Course -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?student_course_id={{ $topCourse->student_course_id ?? '' }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Course</h6>
                        <h5>{{ $topCourse->course->course_name ?? '-' }}</h5>
                        <small>{{ $topCourse->total ?? 0 }} tests</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Top Category -->
        <div class="col-md-4">
            <a href="{{ route('admin.tests.index') }}?test_category_id={{ $topCategory->test_category_id ?? '' }}" class="text-decoration-none text-dark">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6>Top Category</h6>
                        <h5>{{ $topCategory->category->name ?? '-' }}</h5>
                        <small>{{ $topCategory->total ?? 0 }} tests</small>
                    </div>
                </div>
            </a>
        </div>

    </div>

</div>
@endsection