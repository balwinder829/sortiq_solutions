@extends('layouts.app')

@section('content')

<style>
/*.nav-tabs .nav-link {
    background:#f4f6f9;
    border:1px solid #dee2e6;
    margin-right:5px;
    border-radius:6px 6px 0 0;
}
.nav-tabs .nav-link.active{
    background:#ffffff;
    border-bottom:2px solid #0d6efd;
    font-weight:600;
}*/
.nav-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    border-bottom:none;
}

.nav-tabs .nav-item{
    margin:0;
}

.nav-tabs .nav-link{
    background:#f4f6f9;
    border:1px solid #dee2e6;
    border-radius:6px 6px 0 0;
    white-space:nowrap;
    padding:10px 18px;
}

.nav-tabs .nav-link.active{
    background:#ffffff;
    border-bottom:2px solid #0d6efd;
    font-weight:600;
}
</style>
@php
        $fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
        $fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
    @endphp
<div class="container mt-4">

    <h4 class="mb-4">📊 Analytics Dashboard</h4>

    {{-- ================= TAB HEADERS ================= --}}
    <ul class="nav nav-tabs mb-3">

        <li class="nav-item">
            <button class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-leads"
                    data-tab="leads">
                Sales
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
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-students"
                    data-tab="students">
                Students
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-company"
                    data-tab="company">
                Company General
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-ads"
                    data-tab="ads">
                Ads Management
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-hr"
                    data-tab="hr">
                HR Management
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-security"
                    data-tab="security">
                Security
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link"
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
                    data-bs-target="#student-finance"
                    data-tab="student-finance">
                Student Finance
            </button>
        </li>

        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-dropout"
                    data-tab="dropout-students">
                Dropout Students
            </button>
        </li>

    </ul>

    {{-- ================= TAB CONTENT ================= --}}
    <div class="tab-content">

         {{-- ================= LEADS TAB ================= --}}
        <div class="tab-pane fade show active" id="tab-leads">

            <div class="row gx-3 gy-2">

                {{-- Total Leads --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Leads</h6>
                            <h3>{{ $totalLeads }}</h3>
                        </div>
                    </div>
                </div>

                 {{-- Assigned --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Assigned Leads</h6>
                            <h3>{{ $assignedLeads }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Unassigned --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Unassigned Leads</h6>
                            <h3>{{ $unassignedLeads }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Manual Data --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Manual Data</h6>
                            <h3>{{ $manualDataCount }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Hard Data --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Hard Data</h6>
                            <h3>{{ $hardDataCount }}</h3>
                        </div>
                    </div>
                </div>

                 

                {{-- Passout Leads --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Passout Leads</h6>
                            <h3>{{ $passoutLeads }}</h3>
                        </div>
                    </div>
                </div>

               

                {{-- Sales Team --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Sales Team</h6>
                            <h3>{{ $totalStaff }}</h3>
                        </div>
                    </div>
                </div>
                 {{-- Sales Team --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Active Member</h6>
                            <h3>{{ $activeStaff }}</h3>
                        </div>
                    </div>
                </div>
                 {{-- Sales Team --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Inactive Member</h6>
                            <h3>{{ $inactiveStaff }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Total Registrations --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Registrations</h6>
                            <h3>{{ $totalRegistrations }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Pending Registrations --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Pending Registrations</h6>
                            <h3>{{ $pendingRegistrations }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Top Performer --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Top Performer</h6>
                            <h5>{{ $topPerformer->salesStaff->name ?? '-' }}</h5>
                            <small>{{ $topPerformer->total ?? 0 }} registrations</small>
                        </div>
                    </div>
                </div>

                {{-- Lowest Performer --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Lowest Performer</h6>
                            <h5>{{ $lowestPerformer->salesStaff->name ?? '-' }}</h5>
                            <small>{{ $lowestPerformer->total ?? 0 }} registrations</small>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= TRAINER TAB ================= --}}
        <div class="tab-pane fade" id="tab-trainers">

            <div class="row gx-3 gy-2">
                <!-- Total Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Mentors</h6>
                            <h3>{{ $totalTrainers }}</h3>
                        </div>
                    </div>
                </div>
                 <!-- Active Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                             <h6>Active Mentors</h6>
                            <h3>{{ $activeTrainers }}</h3>
                        </div>
                    </div>
                </div>
                <!-- Inactive Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                             <h6>Inactive Mentors</h6>
                            <h3>{{ $inactiveTrainers }}</h3>
                        </div>
                    </div>
                </div>
                 {{-- Total Batches --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Batches</h6>
                            <h3>{{ $totalBatches }}</h3>
                        </div>
                    </div>
                </div>
                

                {{-- Online --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Online Batches</h6>
                            <h3>{{ $onlineBatches }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Offline --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Offline Batches</h6>
                            <h3>{{ $offlineBatches }}</h3>
                        </div>
                    </div>
                </div>
                 {{-- Top Mentor --}}
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Top Mentor</h6>
                            <h5>{{ $topMentor->mentor->name ?? '-' }}</h5>
                            <small>{{ $topMentor->total ?? 0 }} batches</small>
                        </div>
                    </div>
                </div>

                {{-- Lowest Mentor --}}
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Lowest Mentor</h6>
                            <h5>{{ $lowestMentor->mentor->name ?? '-' }}</h5>
                            <small>{{ $lowestMentor->total ?? 0 }} batches</small>
                        </div>
                    </div>
                    </div>


            </div>

        </div>
        {{-- ================= STUDENT TAB ================= --}}
        <div class="tab-pane fade" id="tab-students">

            <div class="row gx-3 gy-2">
                <!-- Total Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Students</h6>
                            <h3>{{ $allstudents->total_students }}</h3>
                        </div>
                    </div>
                </div>
                 <!-- Active Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                             <h6>Online Students</h6>
                            <h3>{{ $allstudents->online_students ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <!-- Inactive Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                             <h6>Offline Students</h6>
                            <h3>{{ $allstudents->offline_students ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                 {{-- Total Confirmed Students --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Total Confirmed Students</h6>
                            <h3 class="fw-bold">{{ $totalConfirmed }}</h3>
                        </div>
                    </div>
                </div>
                

                {{-- Total Certificate --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Total Certificate</h6>
                    <h3 class="fw-bold">{{ $totalCertificate }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Total Closed --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Total Closed</h6>
                    <h3 class="fw-bold">{{ $totalClosed }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Placed Student --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Placed Student</h6>
                    <h3 class="fw-bold">{{ $placedStudents }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Placed Student --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Pending Registration Student</h6>
                    <h3 class="fw-bold">{{ $totalPendingRegistrations }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Placed Student --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Joined Student</h6>
                    <h3 class="fw-bold">{{ $totalJoinedStudents }}</h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>

         {{-- ================= COMPANY TAB ================= --}}
        <div class="tab-pane fade" id="tab-company">

            <div class="row gx-3 gy-2">
                <!-- Total Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Events</h6>
                             <h3 class="fw-bold">{{ $totalEvents }}</h3>
                        </div>
                    </div>
                </div>
                 <!-- Active Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Employee Events</h6>
                            <h3 class="fw-bold">{{ $employeeEvents }}</h3>
                        </div>
                    </div>
                </div>
                <!-- Inactive Trainers -->
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">College Events</h6>
                            <h3 class="fw-bold">{{ $collegeEvents }}</h3>
                        </div>
                    </div>
                </div>
                 
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Student Memory Events</h6>
                            <h3 class="fw-bold">{{ $studentEvents }}</h3>
                        </div>
                    </div>
                </div>

                 
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Upcoming Events</h6>
                            <h3 class="fw-bold">{{ $upcomingEventsCount }}</h3>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ================= ADS TAB ================= --}}
        <div class="tab-pane fade" id="tab-ads">

            <div class="row gx-3 gy-2">

                {{-- Pages --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Total Pages</h6>
                            <h3>{{ $totalPages }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Service Entries</h6>
                            <h3>{{ $serviceEntries }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Internship --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Internship Entries</h6>
                            <h3>{{ $internshipEntries }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Product --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Product Entries</h6>
                            <h3>{{ $productEntries }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Single Product --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Single Product Entries</h6>
                            <h3>{{ $singleProductEntries }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services Today --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Service (Today)</h6>
                            <h3>{{ $todayService }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services Yesterday --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Service (Yesterday)</h6>
                            <h3>{{ $yesterdayService }}</h3>
                        </div>
                    </div>
                </div>
                {{-- Services Today --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Internship (Today)</h6>
                            <h3>{{ $todayInternship }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services Yesterday --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Internship (Yesterday)</h6>
                            <h3>{{ $yesterdayInternship }}</h3>
                        </div>
                    </div>
                </div>
                {{-- Services Today --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Products (Today)</h6>
                            <h3>{{ $todayProduct }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services Yesterday --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Products (Yesterday)</h6>
                            <h3>{{ $yesterdayProduct }}</h3>
                        </div>
                    </div>
                </div>
                {{-- Services Today --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Single Product (Today)</h6>
                            <h3>{{ $todaySingleProduct }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services Yesterday --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Single Product (Yesterday)</h6>
                            <h3>{{ $yesterdaySingleProduct }}</h3>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= HR TAB ================= --}}
        <div class="tab-pane fade" id="tab-hr">

            <div class="row gx-3 gy-2">

                {{-- Pages --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Total Employees</h6>
                            <h3>{{ $totalEmployees }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Active</h6>
                            <h3>{{ $activeEmployees }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Internship --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                             <h6>Inactive</h6>
                            <h3>{{ $inactiveEmployees }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Internship --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Resigned</h6>
                            <h3>{{ $resignedEmployees }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Terminated</h6>
                            <h3>{{ $terminatedEmployees }}</h3>
                        </div>
                    </div>
                </div>

                
            </div>

        </div>
        {{-- ================= SECURITY TAB ================= --}}
        <div class="tab-pane fade" id="tab-security">

            <div class="row gx-3 gy-2">

                {{-- Pages --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Blocked IPs</h6>
                            <h3>{{ $blockedIps }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Allowed IPs</h6>
                            <h3>{{ $allowedIps }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Internship --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                               <h6>Blocked Numbers</h6>
                                <h3>{{ $blockedNumbers }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Internship --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Total Users</h6>
                            <h3>{{ $totalUsers }}</h3>
                        </div>
                    </div>
                </div>


                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                            <div class="card-body">
                            <h6>Terminated</h6>
                            <h3>{{ $terminatedEmployees }}</h3>
                        </div>
                    </div>
                </div>

                
            </div>

        </div>
        {{-- ================= TEST TAB ================= --}}
        <div class="tab-pane fade" id="tab-tests">

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

         {{-- ================= STUDENT FINANCE TAB ================= --}}
        <div class="tab-pane fade" id="student-finance">

            <div class="row gx-3 gy-2">

                {{-- Total Leads --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Amount</h6>
                            <h3>Rs. {{ $fmt->format(optional($feeSums)->total_fees ?? 0) }}</h3>
                        </div>
                    </div>
                </div>

                 {{-- Assigned --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Pending Amount</h6>
                            <h3>Rs. {{ $fmt->format(optional($feeSums)->pending_fees ?? 0) }}</h3>
                        </div>
                    </div>
                </div>

                {{-- Unassigned --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Highest Revenue College Wise</h6>
                            <h4>Rs. {{ $fmt->format(optional($topCollegeData)->total_collected ?? 0) }} - {{ $topCollegeData->college_name_text }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Manual Data --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Highest Revenue State</h6>
                            <h4>Rs. {{ $fmt->format($topState->total ?? 0) }} - {{ $topState->state }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Hard Data --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Highest Revenue District</h6>
                            <h4>Rs. {{ $fmt->format($topDistrict->total ?? 0) }} - {{ $topDistrict->district }}</h4>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- ================= DROPOUT TAB ================= --}}
        <div class="tab-pane fade" id="tab-dropout">

            <div class="row gx-3 gy-2">

                <div class="col-md-3">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Dropout</h6>
                            <h3>{{ $dropoutAnalytics->total_dropouts }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Fees</h6>
                            <h3>Rs. {{ $fmt->format($dropoutAnalytics->total_fees) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Total Collected</h6>
                            <h3>Rs. {{ $fmt->format($dropoutAnalytics->collected_fees) }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card dashboard-card text-center shadow-sm">
                        <div class="card-body">
                            <h6>Pending Fees</h6>
                            <h3>Rs. {{ $fmt->format($dropoutAnalytics->pending_fees) }}</h3>
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