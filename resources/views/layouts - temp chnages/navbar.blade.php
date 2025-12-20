@php
    // highlight child link only
    function isChildActive($route)
    {
        return request()->routeIs($route) ? 'active' : '';
    }

    // expand submenu
    function showSubmenu($routes)
    {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) return 'mm-show';
        }
        return '';
    }

    // mark parent li active ONLY to open submenu (NOT highlight purple)
    function isParent($routes)
    {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) return 'mm-active';
        }
        return '';
    }
@endphp

<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">

            {{-- ========================================================= --}}
            {{-- ADMIN MENU (role = 1)                                    --}}
            {{-- ========================================================= --}}
            @if(Auth::check() && Auth::user()->role == 1)
                <li class="nav-label first"></li>

                {{-- Dashboard --}}
                <li class="{{ isParent(['dashboard','analytics.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul class="{{ showSubmenu(['dashboard']) }}">
                        <li>
                            <a class="{{ isChildActive('dashboard') }}"
                                href="{{ route('dashboard') }}">
                                Dashboard Overview
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('analytics.index') }}"
                                href="{{ route('analytics.index') }}">
                                Analytics
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Sessions --}}
                <li class="{{ isParent(['sessions.index']) }}">
                    <a class="" href="{{ route('sessions.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Sessions</span>
                    </a>
                     
                </li>

                {{-- Student Main Admin --}}
                <li class="{{ isParent(['students*','certificates*','close_student*','courses*','colleges*','placements*','references.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Student Main Admin</span>
                    </a>
                    <ul class="{{ showSubmenu(['students*','certificates*','close_student*','courses*','colleges*','placements*'.'references.index']) }}">
                        {{-- Colleges --}}
                        <li class="{{ isParent(['colleges.index']) }}">
                            <a href="{{ route('colleges.index') }}">
                                <i class="fas fa-university"></i>
                                <span class="nav-text">Colleges</span>
                            </a>
                             
                        </li>

                         {{-- Courses --}}
                        <li class="{{ isParent(['courses.index']) }}">
                            <a  href="{{ route('courses.index') }}">
                                <i class="fas fa-book"></i>
                                <span class="nav-text">Courses</span>
                            </a>
                             
                        </li>

                        {{-- Students Confirmation --}}
                        <li class="{{ isParent(['students.index']) }}">
                            <a  href="{{ route('students.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Students Confirmation</span>
                            </a>
                            
                        </li>

                        {{-- Certificates --}}
                        <li class="{{ isParent(['certificates.index']) }}">
                            <a href="{{ route('certificates.index') }}">
                                <i class="fas fa-certificate"></i>
                                <span class="nav-text">Students Certification</span>
                            </a>
                             
                        </li>

                        {{-- Certificates --}}
                        <li class="{{ isParent(['close_student.index']) }}">
                            <a href="{{ route('close_student.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Close Student</span>
                            </a>
                             
                        </li>

                         {{-- Placement --}}
                        <li class="{{ isParent(['placements.index']) }}">
                            <a href="{{ route('placements.index') }}">
                                <i class="fa-solid fa-photo-film"></i>
                                <span class="nav-text">Placements</span>
                            </a>
                        </li>


                        {{-- References --}}
                        <li class="{{ isParent(['references.index']) }}">
                            <a href="{{ route('references.index') }}">
                                <i class="fas fa-address-book"></i>
                                <span class="nav-text">References</span>
                            </a>
                            
                        </li>
                       
                        
                    </ul>
                </li>

                

                
                {{-- Trainers --}}
                <li class="{{ isParent(['trainers*','batches*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Trainers</span>
                    </a>
                    <ul class="{{ showSubmenu(['trainers*','batches*']) }}">
                        {{-- Trainers --}}
                        <li class="{{ isParent(['trainers.index']) }}">
                            <a href="{{ route('trainers.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="nav-text">Trainers</span>
                            </a>
                            
                        </li>

                        {{-- Batches --}}
                        <li class="{{ isParent(['batches.index']) }}">
                            <a href="{{ route('batches.index') }}">
                                <i class="fas fa-layer-group"></i>
                                <span class="nav-text">Batches</span>
                            </a>
                             
                        </li>
                    </ul>
                </li>
                {{-- Leads --}}
                <li class="{{ isParent(['enquiries*','admin.enquiries.dashboard','salespersons*','admin.enquiries.performance']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Sales</span>
                    </a>
                    <ul class="{{ showSubmenu(['enquiries*','admin.enquiries.dashboard','salespersons','admin.enquiries.performance']) }}">
                        <li>
                            <a class="{{ isChildActive('enquiries*') }}"
                                href="{{ route('enquiries.index') }}">
                                Uploaded Data
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('salespersons*') }}"
                                href="{{ route('salespersons.list') }}">
                                Salespersons
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('admin.calls') }}"
                                href="{{ route('admin.calls') }}">
                                Call Status
                            </a>
                        </li>

                         <!-- <li>
                            <a class="{{ isChildActive('admin.enquiries.performance') }}"
                                href="{{ route('admin.enquiries.performance') }}">
                                Sale Activity
                            </a>
                        </li> -->
                         <!-- <li>
                            <a class="{{ isChildActive('assignments') }}"
                                href="{{ route('assignments.report') }}">
                                Sales Activity
                            </a>
                        </li> -->
                       <!--  <li>
                            <a class="{{ isChildActive('leads') }}"
                                href="{{ route('leads.import.history') }}">
                                Upload History
                            </a>
                        </li> -->
                    </ul>
                </li>

                 {{-- Attendence --}}
                <li class="{{ isParent(['attendance.employees']) }}">
                    <a href="{{ route('attendance.employees') }}">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Attendence</span>
                    </a>
                </li>

                 {{-- Users --}}

                 <li class="{{ isParent(['users.index']) }}">
                    <a href="{{ route('users.index') }}">
                         <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                    </a>
                </li>
                <!-- <li class="{{ isParent(['users.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                    </a>
                    <ul class="{{ showSubmenu(['users.index']) }}">
                        <li>
                            <a class="{{ isChildActive('users.index') }}"
                                href="{{ route('users.index') }}">
                                Users List
                            </a>
                        </li>
                    </ul>
                </li> -->
                
                

                 {{-- Trainers --}}
                <li class="{{ isParent(['student.events*','college.events*','upcoming-events*','employee.events.*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Website Uses</span>
                    </a>
                    <ul class="{{ showSubmenu(['student.events*','college.events*','upcoming-events*','employee.events.*']) }}">
                        {{-- Trainers --}}

                     {{-- College Events --}}
                        <li>
                            <a class="{{ isChildActive('college.events.*') }}"
                               href="{{ route('college.events.index') }}">
                                Memory College Events
                            </a>
                        </li>

                        {{-- Student Events --}}
                        <li>
                            <a class="{{ isChildActive('student.events.*') }}"
                               href="{{ route('student.events.index') }}">
                               Memory Student Events
                            </a>
                        </li>

                        {{-- Employee Events --}}
                        <li>
                            <a class="{{ isChildActive('employee.events.*') }}"
                               href="{{ route('employee.events.index') }}">
                               Memory Employee Events
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('upcoming-events.*') }}"
                               href="{{ route('upcoming-events.index') }}">
                               Events
                            </a>
                        </li>
                         {{-- Brochures --}}
                <li class="{{ isParent(['brochures.index']) }}">
                    <a href="{{ route('brochures.index') }}">
                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                        <span class="nav-text">Brochures</span>
                    </a>
                </li>
                 {{-- Brochures --}}
                <li class="{{ isParent(['company_profile.index']) }}">
                    <a href="{{ route('company_profile.index') }}">
                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                        <span class="nav-text">Company Profile</span>
                    </a>
                </li>


                         
                    </ul>
                </li>

              
                {{-- Leads --}}
                <li class="{{ isParent(['pgs*','part-time-jobs*','placement-companies*']) }}">
    <a class="has-arrow" href="javascript:void(0)">
        <i class="fas fa-tasks"></i>
        <span class="nav-text">Student Services</span>
    </a>
    <ul class="{{ showSubmenu(['pgs*','part-time-jobs*','placement-companies*']) }}">

        <li>
            <a class="{{ isChildActive('placement-companies*') }}"
               href="{{ route('placement-companies.index') }}">
                <!-- <i class="fas fa-building me-2"></i> -->
                Placement Companies
            </a>
        </li>

        <li>
            <a class="{{ isChildActive('part-time-jobs*') }}"
               href="{{ route('part-time-jobs.index') }}">
                <!-- <i class="fas fa-user-clock me-2"></i> -->
                Part-Time Jobs Companies
            </a>
        </li>

        <li>
            <a class="{{ isChildActive('pgs*') }}"
               href="{{ route('pgs.index') }}">
                <!-- <i class="fas fa-bed me-2"></i> -->
                Paying Guest
            </a>
        </li>

    </ul>
</li>


               {{-- Finance --}}
                <li class="{{ isParent(['office-expenses*','pantry-expenses*','event-expenses*','travel-expenses*','office-assets*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-coins"></i>
                        <span class="nav-text">Records and Finance</span>
                    </a>
                    <ul class="{{ showSubmenu(['office-expenses*','pantry-expenses*','event-expenses*','travel-expenses*','office-assets*']) }}">
                        <li>
                            <a class="{{ isChildActive('office-expenses*') }}"
                               href="{{ route('office-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Electricty Bill
                            </a>
                        </li>
                         <li>
                            <a class="{{ isChildActive('pantry-expenses*') }}"
                               href="{{ route('pantry-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Pantry Expenses
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('event-expenses*') }}"
                               href="{{ route('event-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Events Expenses
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('travel-expenses*') }}"
                               href="{{ route('travel-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Travel Expenses
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('office-assets*') }}"
                               href="{{ route('office-assets.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Asset Expenses
                            </a>
                        </li>

                         
                    </ul>
                </li>


                         

               
               
                 {{-- Events --}}
                <!-- <li class="{{ isParent(['college.events.*', 'student.events.*', 'employee.events.*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-calendar"></i>
                        <span class="nav-text">Events</span>
                    </a>

                    <ul class="{{ showSubmenu(['college.events.*', 'student.events.*', 'employee.events.*']) }}">

                        {{-- College Events --}}
                        <li>
                            <a class="{{ isChildActive('college.events.*') }}"
                               href="{{ route('college.events.index') }}">
                                College Events
                            </a>
                        </li>

                        {{-- Student Events --}}
                        <li>
                            <a class="{{ isChildActive('student.events.*') }}"
                               href="{{ route('student.events.index') }}">
                                Student Events
                            </a>
                        </li>

                        {{-- Employee Events --}}
                        <li>
                            <a class="{{ isChildActive('employee.events.*') }}"
                               href="{{ route('employee.events.index') }}">
                                Employee Events
                            </a>
                        </li>

                    </ul>
                </li> -->

                 {{-- Tests --}}
                <li class="{{ isParent(['admin.tests.*','admin.offline-tests*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Student Exam Test</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.tests*']) }}">
                        <li>
                            <a class="{{ isChildActive('test-categories.index') }}"
                                href="{{ route('test-categories.index') }}">
                                Test Category
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('admin.tests.index') }}"
                                href="{{ route('admin.tests.index') }}">
                                Online Tests
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('admin.offline-tests.index') }}"
                                href="{{ route('admin.offline-tests.index') }}">
                                Offline Tests
                            </a>
                        </li>
                    </ul>
                </li>
                

               {{-- Logout --}}
                <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="nav-text">Logout</span>
                    </a>

                    <form id="sidebar-logout-form"
                          action="{{ route('logout') }}"
                          method="POST"
                          class="d-none">
                        @csrf
                    </form>
                </li>



                

                
               
            @endif


            {{-- ========================================================= --}}
            {{-- TRAINER MENU (role = 2)                                  --}}
            {{-- ========================================================= --}}
            @if(Auth::check() && Auth::user()->role == 2)

            
                {{-- Batches --}}
                <li class="{{ isParent(['batches.mybatches']) }}">
                    <a  href="{{ route('batches.mybatches') }}">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Batches</span>
                    </a>
                    
                </li>

                {{-- Attendence --}}
                <li class="{{ isParent(['attendance.employee','attendance.myDetail']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Attendence</span>
                    </a>
                    <ul class="{{ showSubmenu(['attendance.employee','attendance.myDetail']) }}">
                        <li>
                            <a class="{{ isChildActive('attendance.employee') }}"
                                href="{{ route('attendance.employee') }}">
                                Add Attendence
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('attendance.myDetail') }}"
                                href="{{ route('attendance.myDetail') }}">
                                Attendence History
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Logout --}}
                <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="nav-text">Logout</span>
                    </a>

                    <form id="sidebar-logout-form"
                          action="{{ route('logout') }}"
                          method="POST"
                          class="d-none">
                        @csrf
                    </form>
                </li>


            @endif


            {{-- ========================================================= --}}
            {{-- SALES MENU (role = 3)                                    --}}
            {{-- ========================================================= --}}
            @if(Auth::check() && Auth::user()->role == 3)

                
                {{-- Dashboard --}}
                
               

                {{-- Leads --}}
                <li class="{{ isParent(['sales.enquiries']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul class="{{ showSubmenu(['sales.enquiries']) }}">
                        <li>
                            <a class="{{ isChildActive('sales.enquiries') }}"
                                href="{{ route('sales.enquiries.index') }}">
                                Assigned Data
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('sales.dashboard') }}"
                                href="{{ route('sales.dashboard') }}">
                                My Dashboard
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Attendence --}}
                <li class="{{ isParent(['attendance.employee','attendance.myDetail']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Attendence</span>
                    </a>
                    <ul class="{{ showSubmenu(['attendance.employee','attendance.myDetail']) }}">
                        <li>
                            <a class="{{ isChildActive('attendance.employee') }}"
                                href="{{ route('attendance.employee') }}">
                                Add Attendence
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('attendance.myDetail') }}"
                                href="{{ route('attendance.myDetail') }}">
                                Attendence History
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Logout --}}
                <li>
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="nav-text">Logout</span>
                    </a>

                    <form id="sidebar-logout-form"
                          action="{{ route('logout') }}"
                          method="POST"
                          class="d-none">
                        @csrf
                    </form>
                </li>


            @endif

        </ul>
    </div>
</div>
