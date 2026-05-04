@extends('layouts.app')

@section('content')

<style>
.nav-tabs .nav-link {
    background:#f4f6f9;
    border:1px solid #dee2e6;
    margin-right:5px;
    border-radius:6px 6px 0 0;
}
.nav-tabs .nav-link.active{
    background:#ffffff;
    border-bottom:2px solid #0d6efd;
    font-weight:600;
}
</style>

<div class="container mt-4">

    <h4 class="mb-4">📊 Analytics Dashboard</h4>

    {{-- ================= TAB HEADERS ================= --}}
    <ul class="nav nav-tabs mb-3">

        <li class="nav-item">
            <button class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-tests"
                    data-tab="tests">
                Tests
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-workshops"
                    data-tab="workshops">
                Workshops
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-leads"
                    data-tab="leads">
                Sales
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-passouts"
                    data-tab="passouts">
                Passouts
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-staff"
                    data-tab="staff">
                Staff
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-trainers"
                    data-tab="trainers">
                Mentors
            </button>
        </li>

        {{-- 3 DUMMY TABS --}}
       <!--  <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-users" data-tab="users">
                Users
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-courses" data-tab="courses">
                Courses
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reports" data-tab="reports">
                Reports
            </button>
        </li> -->

    </ul>

    {{-- ================= TAB CONTENT ================= --}}
    <div class="tab-content">

        {{-- ================= TEST TAB ================= --}}
        <div class="tab-pane fade show active" id="tab-tests">

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

        {{-- ================= WORKSHOP TAB ================= --}}
        <div class="tab-pane fade" id="tab-workshops">

            <div class="row g-3">

                <!-- Done -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Done</h6>
                            <h3>{{ $done }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Meeting -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Meeting</h6>
                            <h3>{{ $meeting }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Decided -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Decided</h6>
                            <h3>{{ $decided }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Past -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Past</h6>
                            <h3>{{ $past }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Today -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Today</h6>
                            <h3>{{ $todayCount }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Upcoming</h6>
                            <h3>{{ $future }}</h3>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= LEADS TAB ================= --}}
        <div class="tab-pane fade" id="tab-leads">

            <div class="row g-3">

                <!-- Total Leads -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Leads</h6>
                            <h3>{{ $totalLeads }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Assigned Leads -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Assigned Leads</h6>
                            <h3>{{ $assignedLeads }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Unassigned Leads -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Unassigned Leads</h6>
                            <h3>{{ $unassignedLeads }}</h3>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= PASSOUT LEADS TAB ================= --}}
        <div class="tab-pane fade" id="tab-passouts">

            <div class="row g-3">

                <!-- Total -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Passout Leads</h6>
                            <h3>{{ $totalPassoutLeads }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Assigned -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Assigned</h6>
                            <h3>{{ $assignedPassoutLeads }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Unassigned -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Unassigned</h6>
                            <h3>{{ $unassignedPassoutLeads }}</h3>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        {{-- ================= SALES STAFF TAB ================= --}}
        <div class="tab-pane fade" id="tab-staff">

            <div class="row g-3">

                <!-- Total Staff -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Staff</h6>
                            <h3>{{ $totalStaff }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Active Staff -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Active Staff</h6>
                            <h3>{{ $activeStaff }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Inactive Staff -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Inactive Staff</h6>
                            <h3>{{ $inactiveStaff }}</h3>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        {{-- ================= TRAINER TAB ================= --}}
        <div class="tab-pane fade" id="tab-trainers">

            <div class="row g-3">

                <!-- Total Trainers -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Trainers</h6>
                            <h3>{{ $totalTrainers }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Active Trainers -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Active Trainers</h6>
                            <h3>{{ $activeTrainers }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Inactive Trainers -->
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Inactive Trainers</h6>
                            <h3>{{ $inactiveTrainers }}</h3>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        {{-- ================= DUMMY TABS ================= --}}
        @foreach(['users','courses','reports'] as $dummy)
        <div class="tab-pane fade" id="tab-{{ $dummy }}">
            <div class="row g-3">

                @for($i=1;$i<=5;$i++)
                <div class="col-md-4">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h6>{{ ucfirst($dummy) }} Metric {{ $i }}</h6>
                            <h3>{{ rand(10,100) }}</h3>
                        </div>
                    </div>
                </div>
                @endfor

            </div>
        </div>
        @endforeach

    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    let params = new URLSearchParams(window.location.search);
    let activeTab = params.get('tab');

    if (activeTab) {
        let triggerEl = document.querySelector(`[data-tab="${activeTab}"]`);
        if (triggerEl) {
            new bootstrap.Tab(triggerEl).show();
        }
    }

    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            let tabName = e.target.getAttribute('data-tab');
            let url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        });
    });

});
</script>
@endpush