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
    <!-- Mobile Close Button -->
<div class="sidebar-close d-lg-none">
    <span>&times;</span>
</div>

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
                                <span class="nav-text">Students Confirmations</span>
                            </a>
                            
                        </li>

                        {{-- Certificates --}}
                        <li class="{{ isParent(['certificates.index']) }}">
                            <a href="{{ route('certificates.index') }}">
                                <i class="fas fa-certificate"></i>
                                <span class="nav-text">Students Certifications</span>
                            </a>
                             
                        </li>

                        {{-- Certificates --}}
                        <li class="{{ isParent(['close_student.index']) }}">
                            <a href="{{ route('close_student.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Close Students</span>
                            </a>
                             
                        </li>

                         {{-- Placement --}}
                        <li class="{{ isParent(['placements.index']) }}">
                            <a href="{{ route('placements.index') }}">
                                <i class="fa-solid fa-photo-film"></i>
                                <span class="nav-text">Student Placements</span>
                            </a>
                        </li>


                        {{-- References --}}
                        <li class="{{ isParent(['references.index']) }}">
                            <a href="{{ route('references.index') }}">
                                <i class="fas fa-address-book"></i>
                                <span class="nav-text"> Student References</span>
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
                <li class="{{ isParent(['enquiries*','admin.enquiries.dashboard','salespersons*','admin.enquiries.performance','registrations*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Sales</span>
                    </a>
                    <ul class="{{ showSubmenu(['enquiries*','admin.enquiries.dashboard','salespersons','admin.enquiries.performance','registrations*']) }}">
                        <li>
                            <a class="{{ isChildActive('enquiries*') }}"
                                href="{{ route('enquiries.index') }}">
                               Manage  Sales Data
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('salespersons*') }}"
                                href="{{ route('salespersons.list') }}">
                                Sales Teams
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('admin.calls') }}"
                                href="{{ route('admin.calls') }}">
                                Team Status
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('registrations*') }}"
                                href="{{ route('registrations.index') }}">
                                Registrations
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
                <!-- <li class="{{ isParent(['attendance.employees']) }}">
                    <a href="{{ route('attendance.employees') }}">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Employee Attendence </span>
                    </a>
                </li> -->

                {{-- Attendence --}}
                <li class="{{ isParent(['attendance.employees','employees*','letters*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Employees </span>
                    </a>
                    <ul class="{{ showSubmenu(['attendance.employees']) }}">
                        <li>
                            <a class="{{ isChildActive('attendance.employees') }}"
                                href="{{ route('attendance.employees') }}">
                                Employees Attendence 
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('employees') }}"
                                href="{{ route('employees.index') }}">
                                Employees Lists 
                            </a>
                        </li>
                         <li>
                            <a class="{{ isChildActive('letters*') }}"
                                href="{{ route('letters.index') }}">
                                Joining/Experience Letters
                            </a>
                        </li>
                    </ul>
                </li>


                 {{-- Users --}}

                 <li class="{{ isParent(['users.index']) }}">
                    <a href="{{ route('users.index') }}">
                         <i class="fas fa-users"></i>
                        <span class="nav-text">Manage Users</span>
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
                                 College Memory Events
                            </a>
                        </li>

                        {{-- Student Events --}}
                        <li>
                            <a class="{{ isChildActive('student.events.*') }}"
                               href="{{ route('student.events.index') }}">
                               Student Memory  Events
                            </a>
                        </li>

                        {{-- Employee Events --}}
                        <li>
                            <a class="{{ isChildActive('employee.events.*') }}"
                               href="{{ route('employee.events.index') }}">
                              Employee  Memory  Events
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('upcoming-events.*') }}"
                               href="{{ route('upcoming-events.index') }}">
                              Upcoming Events
                            </a>
                        </li>
                         {{-- Brochures --}}
                <li class="{{ isParent(['brochures.index']) }}">
                    <a href="{{ route('brochures.index') }}">
                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                        <span class="nav-text">Manage Brochures</span>
                    </a>
                </li>
                 {{-- Brochures --}}
                <li class="{{ isParent(['company_profile.index']) }}">
                    <a href="{{ route('company_profile.index') }}">
                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                        <span class="nav-text">Company Profile Manage</span>
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


                         

               
               
                 

                 {{-- Tests --}}
                <li class="{{ isParent(['admin.tests.*','admin.offline-tests*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Student Exams</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.tests*']) }}">
                        <li>
                            <a class="{{ isChildActive('test-categories.index') }}"
                                href="{{ route('test-categories.index') }}">
                                Exam Category
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('admin.tests.index') }}"
                                href="{{ route('admin.tests.index') }}">
                                Online Exams
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('admin.offline-tests.index') }}"
                                href="{{ route('admin.offline-tests.index') }}">
                                Offline Exams
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Joined Students --}}
                <li class="{{ isParent(['joined_students*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Joined Students</span>
                    </a>
                    <ul class="{{ showSubmenu(['joined_students*']) }}">
                        <li>
                            <a class="{{ isChildActive('joined_students.adminUrl') }}"
                                href="{{ route('joined_students.adminUrl') }}">
                                Joined Students Link
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('admin.joined_students.index') }}"
                                href="{{ route('joined_students.index') }}">
                                Joined Students Lists
                            </a>
                        </li>
                    </ul>
                </li>


                 {{-- Leads --}}
                <li class="{{ isParent(['recharges*','projects*','tutorials*','cvs','daily-interviews']) }}">
    <a class="has-arrow" href="javascript:void(0)">
        <i class="fas fa-tasks"></i>
        <span class="nav-text">Manage Records</span>
    </a>
    <ul class="{{ showSubmenu(['recharges*','projects*','tutorials*','cvs','daily-interviews']) }}">

        <li>
            <a class="{{ isChildActive('recharges*') }}"
               href="{{ route('recharges.index') }}">
                <!-- <i class="fas fa-building me-2"></i> -->
                Recharges
            </a>
        </li>

        <li>
            <a class="{{ isChildActive('projects*') }}"
               href="{{ route('projects.index') }}">
                <!-- <i class="fas fa-user-clock me-2"></i> -->
                Projects
            </a>
        </li>

        <li>
            <a class="{{ isChildActive('tutorials*') }}"
               href="{{ route('tutorials.index') }}">
                <!-- <i class="fas fa-bed me-2"></i> -->
                Tutorials
            </a>
        </li>
        <li>
            <a class="{{ isChildActive('cvs*') }}"
               href="{{ route('cvs.index') }}">
                <!-- <i class="fas fa-bed me-2"></i> -->
                CV's
            </a>
        </li>
        <li>
            <a class="{{ isChildActive('daily-interviews*') }}"
               href="{{ route('daily-interviews.index') }}">
                <!-- <i class="fas fa-bed me-2"></i> -->
                Daily Interviews
            </a>
        </li>

    </ul>
</li>

                
                        

                        <li class="{{ isParent(['admin.blocked-numbers.index']) }}">
                            <a href="{{ route('admin.blocked-numbers.index') }}">
                                <i class="fas fa-certificate"></i>
                                <span class="nav-text">Blocked Numbers</span>
                            </a>
                             
                        </li>


                        {{-- Certificates --}}
                        
                

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

             @if(Auth::check() && Auth::user()->role == 4)

                 {{-- ================= DASHBOARD ================= --}}
                @cananyperm('dashboard.view','analytics.view')
                <li class="{{ isParent(['dashboard','analytics.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul class="{{ showSubmenu(['dashboard']) }}">
                         @canperm('dashboard.view')
                         <li>
                            <a class="{{ isChildActive('dashboard') }}"
                                href="{{ route('dashboard') }}">
                                Dashboard Overview
                            </a>
                        </li>
                         @endcanperm
                         @canperm('analytics.view')
                        <li>
                            <a class="{{ isChildActive('analytics.index') }}"
                                href="{{ route('analytics.index') }}">
                                Analytics
                            </a>
                        </li>
                        @endcanperm
                    </ul>
                </li>
                @endcanperm
                
                {{-- Sessions --}}
                @cananyperm(
                        'sessions.view',
                    )
                <li class="{{ isParent(['sessions.index']) }}">
                    <a class="" href="{{ route('sessions.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Sessions</span>
                    </a>
                     
                </li>
                @endcanperm

                 {{-- Student Main Admin --}}
                @cananyperm(
                        'students.view',
                        'certificates.view',
                        'placements.view',
                        'courses.view',
                        'colleges.view',
                        'references.view',
                        'close_students.view'
                    )
                <li class="{{ isParent(['students*','certificates*','close_student*','courses*','colleges*','placements*','references.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Student Main Admin</span>
                    </a>
                    <ul class="{{ showSubmenu(['students*','certificates*','close_student*','courses*','colleges*','placements*','references.index']) }}">
                        {{-- Colleges --}}
                        @canperm('colleges.view')
                        <li class="{{ isParent(['colleges.index']) }}">
                            <a href="{{ route('colleges.index') }}">
                                <i class="fas fa-university"></i>
                                <span class="nav-text">Colleges</span>
                            </a>
                             
                        </li>
                            @endcanperm
                         {{-- Courses --}}
                         @canperm('courses.view')
                        <li class="{{ isParent(['courses.index']) }}">
                            <a  href="{{ route('courses.index') }}">
                                <i class="fas fa-book"></i>
                                <span class="nav-text">Courses</span>
                            </a>
                             
                        </li>
                            @endcanperm
                        {{-- Students Confirmation --}}
                        @canperm('students.view')
                        <li class="{{ isParent(['students.index']) }}">
                            <a  href="{{ route('students.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Students Confirmation</span>
                            </a>
                            
                        </li>
                            @endcanperm
                        {{-- Certificates --}}
                        @canperm('certificates.view')
                        <li class="{{ isParent(['certificates.index']) }}">
                            <a href="{{ route('certificates.index') }}">
                                <i class="fas fa-certificate"></i>
                                <span class="nav-text">Students Certification</span>
                            </a>
                             
                        </li>
                            @endcanperm
                        {{-- Certificates --}}
                        @canperm('close_students.view')
                        <li class="{{ isParent(['close_student.index']) }}">
                            <a href="{{ route('close_student.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Close Student</span>
                            </a>
                             
                        </li>
                            @endcanperm
                         {{-- Placement --}}
                         @canperm('placements.view')
                        <li class="{{ isParent(['placements.index']) }}">
                            <a href="{{ route('placements.index') }}">
                                <i class="fa-solid fa-photo-film"></i>
                                <span class="nav-text">Placements</span>
                            </a>
                        </li>
                            @endcanperm

                        {{-- References --}}
                        @canperm('references.view')
                        <li class="{{ isParent(['references.index']) }}">
                            <a href="{{ route('references.index') }}">
                                <i class="fas fa-address-book"></i>
                                <span class="nav-text">References</span>
                            </a>
                            
                        </li>
                    @endcanperm
                        
                    </ul>
                </li>
                     @endcanperm
                

                 {{-- Trainers --}}
                @cananyperm('trainers.view','batches.view')
                <li class="{{ isParent(['trainers*','batches*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Trainers</span>
                    </a>
                    <ul class="{{ showSubmenu(['trainers*','batches*']) }}">
                        {{-- Trainers --}}
                        @canperm('trainers.view')
                        <li class="{{ isParent(['trainers.index']) }}">
                            <a href="{{ route('trainers.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="nav-text">Trainers</span>
                            </a>
                            
                        </li>
                        @endcanperm

                        {{-- Batches --}}
                        @canperm('trainers.view')
                        <li class="{{ isParent(['batches.index']) }}">
                            <a href="{{ route('batches.index') }}">
                                <i class="fas fa-layer-group"></i>
                                <span class="nav-text">Batches</span>
                            </a>
                             
                        </li>
                        @endcanperm

                    </ul>
                </li>

                @endcanperm


                  {{-- Leads --}}
                @cananyperm('enquiries.view','salespersons.view','calls.view')
                <li class="{{ isParent(['enquiries*','admin.enquiries.dashboard','salespersons*','admin.enquiries.performance']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Sales</span>
                    </a>
                    <ul class="{{ showSubmenu(['enquiries*','admin.enquiries.dashboard','salespersons','admin.enquiries.performance']) }}">
                        @canperm('enquiries.view')
                        <li>
                            <a class="{{ isChildActive('enquiries*') }}"
                                href="{{ route('enquiries.index') }}">
                                Uploaded Data
                            </a>
                        </li>
                        @endcanperm
                        @canperm('salespersons.view')
                        <li>
                            <a class="{{ isChildActive('salespersons*') }}"
                                href="{{ route('salespersons.list') }}">
                                Salespersons
                            </a>
                        </li>
                        @endcanperm
                        @canperm('calls.view')
                        <li>
                            <a class="{{ isChildActive('admin.calls') }}"
                                href="{{ route('admin.calls') }}">
                                Call Status
                            </a>
                        </li>
                        @endcanperm
                    </ul>
                </li>
                        @endcanperm


                 {{-- Attendence --}}

                 @canperm('attendance.view')
                <li class="{{ isParent(['attendance.employees']) }}">
                    <a href="{{ route('attendance.employees') }}">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Attendence</span>
                    </a>
                </li>
                @endcanperm




                {{-- Users --}}

                  @canperm('users.view')

                 <li class="{{ isParent(['users.index']) }}">
                    <a href="{{ route('users.index') }}">
                         <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                    </a>
                </li>
                      @endcanperm



                 {{-- Trainers --}}

                 @cananyperm('events.view','brochures.view','company_profile.view','college_event.view','student_event.view','employee_event.view','upcoming-events.view')
                <li class="{{ isParent(['student.events*','college.events*','upcoming-events*','employee.events.*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Website Uses</span>
                    </a>
                    <ul class="{{ showSubmenu(['student.events*','college.events*','upcoming-events*','employee.events.*']) }}">
                        {{-- Trainers --}}

                     {{-- College Events --}}
                     @canperm('college_event.view')
                        <li>
                            <a class="{{ isChildActive('college.events.*') }}"
                               href="{{ route('college.events.index') }}">
                                Memory College Events
                            </a>
                        </li>
                    @endcanperm
                        {{-- Student Events --}}
                        @canperm('student_event.view')
                        <li>
                            <a class="{{ isChildActive('student.events.*') }}"
                               href="{{ route('student.events.index') }}">
                               Memory Student Events
                            </a>
                        </li>
                        @endcanperm

                        {{-- Employee Events --}}
                        @canperm('employee_event.view')
                        <li>
                            <a class="{{ isChildActive('employee.events.*') }}"
                               href="{{ route('employee.events.index') }}">
                               Memory Employee Events
                            </a>
                        </li>

                        @endcanperm
                        @canperm('upcoming-events.view')
                        <li>
                            <a class="{{ isChildActive('upcoming-events.*') }}"
                               href="{{ route('upcoming-events.index') }}">
                               Events
                            </a>
                        </li>
                        @endcanperm
                         {{-- Brochures --}}
                         @canperm('brochures.view')
                <li class="{{ isParent(['brochures.index']) }}">
                    <a href="{{ route('brochures.index') }}">
                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                        <span class="nav-text">Brochures</span>
                    </a>
                </li>
                @endcanperm
                 {{-- Brochures --}}
                 @canperm('company_profile.view')
                <li class="{{ isParent(['company_profile.index']) }}">
                    <a href="{{ route('company_profile.index') }}">
                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                        <span class="nav-text">Company Profile</span>
                    </a>
                </li>
                @endcanperm

                         
                    </ul>
                </li>
                @endcanperm

                 {{-- student services --}}
                 @cananyperm('events.view','brochures.view','company_profiles.view','placement-companies.view','part-time-jobs.view','pgs.view')
                <li class="{{ isParent(['pgs*','part-time-jobs*','placement-companies*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tasks"></i>
                        <span class="nav-text">Student Services</span>
                    </a>
                    <ul class="{{ showSubmenu(['pgs*','part-time-jobs*','placement-companies*']) }}">
                        @canperm('placement-companies.view')
                        <li>
                            <a class="{{ isChildActive('placement-companies*') }}"
                               href="{{ route('placement-companies.index') }}">
                                <!-- <i class="fas fa-building me-2"></i> -->
                                Placement Companies
                            </a>
                        </li>
                        @endcanperm
                        @canperm('part-time-jobs.view')
                        <li>
                            <a class="{{ isChildActive('part-time-jobs*') }}"
                               href="{{ route('part-time-jobs.index') }}">
                                <!-- <i class="fas fa-user-clock me-2"></i> -->
                                Part-Time Jobs Companies
                            </a>
                        </li>
                        @endcanperm
                        @canperm('pgs.view')
                        <li>
                            <a class="{{ isChildActive('pgs*') }}"
                               href="{{ route('pgs.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                                Paying Guest
                            </a>
                        </li>
                        @endcanperm
                    </ul>
                </li>

                @endcanperm



                 {{-- Finance --}}
                 @cananyperm('office-expenses.view','pantry-expenses.view','event-expenses.view','travel-expenses.view','office-assets.view')
                <li class="{{ isParent(['office-expenses*','pantry-expenses*','event-expenses*','travel-expenses*','office-assets*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-coins"></i>
                        <span class="nav-text">Records and Finance</span>
                    </a>
                    <ul class="{{ showSubmenu(['office-expenses*','pantry-expenses*','event-expenses*','travel-expenses*','office-assets*']) }}">
                       
                       @canperm('office-expenses.view') <li>
                            <a class="{{ isChildActive('office-expenses*') }}"
                               href="{{ route('office-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Electricty Bill
                            </a>
                        </li>
                        @endcanperm
                         @canperm('pantry-expenses.view')
                         <li>
                            <a class="{{ isChildActive('pantry-expenses*') }}"
                               href="{{ route('pantry-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Pantry Expenses
                            </a>
                        </li>
@endcanperm
@canperm('event-expenses.view')
                         <li>
                            <a class="{{ isChildActive('event-expenses*') }}"
                               href="{{ route('event-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Events Expenses
                            </a>
                        </li>
@endcanperm
@canperm('travel-expenses.view')
                         <li>
                            <a class="{{ isChildActive('travel-expenses*') }}"
                               href="{{ route('travel-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Travel Expenses
                            </a>
                        </li>
@endcanperm
@canperm('office-assets.view')
                         <li>
                            <a class="{{ isChildActive('office-assets*') }}"
                               href="{{ route('office-assets.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Asset Expenses
                            </a>
                        </li>
@endcanperm
                         
                    </ul>
                </li>
@endcanperm

 {{-- Tests --}}

 @cananyperm('test-categories.view','tests.view','offline-tests.view')
                <li class="{{ isParent(['admin.tests.*','admin.offline-tests*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Student Exam Test</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.tests*']) }}">
                        @canperm('test-categories.view')
                        <li>
                            <a class="{{ isChildActive('test-categories.index') }}"
                                href="{{ route('test-categories.index') }}">
                                Test Category
                            </a>
                        </li>
                        @endcanperm
                        @canperm('tests.view')
                        <li>
                            <a class="{{ isChildActive('admin.tests.index') }}"
                                href="{{ route('admin.tests.index') }}">
                                Online Tests
                            </a>
                        </li>
@endcanperm
@canperm('offline-tests.view')
                         <li>
                            <a class="{{ isChildActive('admin.offline-tests.index') }}"
                                href="{{ route('admin.offline-tests.index') }}">
                                Offline Tests
                            </a>
                        </li>
                        @endcanperm
                    </ul>
                </li>


                @endcanperm
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
