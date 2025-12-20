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
                <li class="{{ isParent(['dashboard']) }}">
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
                    </ul>
                </li>

                {{-- Sessions --}}
                <li class="{{ isParent(['sessions.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Sessions</span>
                    </a>
                    <ul class="{{ showSubmenu(['sessions.index']) }}">
                        <li>
                            <a class="{{ isChildActive('sessions.index') }}"
                                href="{{ route('sessions.index') }}">
                                Session List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Courses --}}
                <li class="{{ isParent(['courses.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-book"></i>
                        <span class="nav-text">Courses</span>
                    </a>
                    <ul class="{{ showSubmenu(['courses.index']) }}">
                        <li>
                            <a class="{{ isChildActive('courses.index') }}"
                                href="{{ route('courses.index') }}">
                                Courses List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Colleges --}}
                <li class="{{ isParent(['colleges.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-university"></i>
                        <span class="nav-text">Colleges</span>
                    </a>
                    <ul class="{{ showSubmenu(['colleges.index']) }}">
                        <li>
                            <a class="{{ isChildActive('colleges.index') }}"
                                href="{{ route('colleges.index') }}">
                                College List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Trainers --}}
                <li class="{{ isParent(['trainers.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Trainers</span>
                    </a>
                    <ul class="{{ showSubmenu(['trainers.index']) }}">
                        <li>
                            <a class="{{ isChildActive('trainers.index') }}"
                                href="{{ route('trainers.index') }}">
                                Trainers List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Batches --}}
                <li class="{{ isParent(['batches.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Batches</span>
                    </a>
                    <ul class="{{ showSubmenu(['batches.index']) }}">
                        <li>
                            <a class="{{ isChildActive('batches.index') }}"
                                href="{{ route('batches.index') }}">
                                Batch List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Departments --}}
               <!--  <li class="{{ isParent(['departments.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span class="nav-text">Departments</span>
                    </a>
                    <ul class="{{ showSubmenu(['departments.index']) }}">
                        <li>
                            <a class="{{ isChildActive('departments.index') }}"
                                href="{{ route('departments.index') }}">
                                Department List
                            </a>
                        </li>
                    </ul>
                </li> -->

                {{-- References --}}
                <li class="{{ isParent(['references.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-address-book"></i>
                        <span class="nav-text">References</span>
                    </a>
                    <ul class="{{ showSubmenu(['references.index']) }}">
                        <li>
                            <a class="{{ isChildActive('references.index') }}"
                                href="{{ route('references.index') }}">
                                Reference List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Students Confirmation --}}
                <li class="{{ isParent(['students.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Students Confirmation</span>
                    </a>
                    <ul class="{{ showSubmenu(['students.index']) }}">
                        <li>
                            <a class="{{ isChildActive('students.index') }}"
                                href="{{ route('students.index') }}">
                                Students List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Certificates --}}
                <li class="{{ isParent(['certificates.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-certificate"></i>
                        <span class="nav-text">Students Certification</span>
                    </a>
                    <ul class="{{ showSubmenu(['certificates.index']) }}">
                        <li>
                            <a class="{{ isChildActive('certificates.index') }}"
                                href="{{ route('certificates.index') }}">
                                Students
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Student Verification --}}
              <!--   <li class="{{ isParent(['student_certificates.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-graduation-cap"></i>
                        <span class="nav-text">Student Verification</span>
                    </a>
                    <ul class="{{ showSubmenu(['student_certificates.index']) }}">
                        <li>
                            <a class="{{ isChildActive('student_certificates.index') }}"
                                href="{{ route('student_certificates.index') }}">
                                Student Certificates List
                            </a>
                        </li>
                    </ul>
                </li> -->

                 {{-- Tests --}}
                <li class="{{ isParent(['admin.tests.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Tests</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.tests.index']) }}">
                        <li>
                            <a class="{{ isChildActive('admin.tests.index') }}"
                                href="{{ route('admin.tests.index') }}">
                                Tests List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Leads --}}
                <li class="{{ isParent(['leads']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Leads</span>
                    </a>
                    <ul class="{{ showSubmenu(['leads']) }}">
                        <li>
                            <a class="{{ isChildActive('leads') }}"
                                href="{{ route('leads.index') }}">
                                Leads
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('leads') }}"
                                href="{{ route('leads.import.history') }}">
                                Leads Import History
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Sales --}}
                <li class="{{ isParent(['leads']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Sales</span>
                    </a>
                    <ul class="{{ showSubmenu(['leads']) }}">
                        <li>
                            <a class="{{ isChildActive('sales') }}"
                                href="{{ route('sales.dashboard') }}">
                                Sales
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('admin.activity') }}"
                                href="{{ route('admin.activity') }}">
                                Sale Activity
                            </a>
                        </li>
                    </ul>
                </li>

                 {{-- Events --}}
                <li class="{{ isParent(['college.events.*', 'student.events.*', 'employee.events.*']) }}">
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
                </li>

                
                {{-- Users --}}
                <li class="{{ isParent(['users.index']) }}">
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
                </li>
            @endif


            {{-- ========================================================= --}}
            {{-- TRAINER MENU (role = 2)                                  --}}
            {{-- ========================================================= --}}
            @if(Auth::check() && Auth::user()->role == 2)

                {{-- Dashboard --}}
                <li class="{{ isParent(['dashboard']) }}">
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
                    </ul>
                </li>

                {{-- Students --}}
               <li class="{{ isParent(['sales.students.index','students.create']) }}">
                <a class="has-arrow" href="javascript:void(0)">
                    <i class="fas fa-user-check"></i>
                    <span class="nav-text">Students Confirmation</span>
                </a>
                <ul class="{{ showSubmenu(['sales.students.index','students.create']) }}">
                    <li>
                        <a class="{{ isChildActive('sales.students.index') }}"
                            href="{{ route('sales.students.index') }}">
                            Students List
                        </a>
                    </li>
                </ul>
            </li>
                {{-- Trainers --}}
                <li class="{{ isParent(['trainers.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Trainers</span>
                    </a>
                    <ul class="{{ showSubmenu(['trainers.index']) }}">
                        <li>
                            <a class="{{ isChildActive('trainers.index') }}"
                                href="{{ route('trainers.index') }}">
                                Trainers List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Batches --}}
                <li class="{{ isParent(['batches.index']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Batches</span>
                    </a>
                    <ul class="{{ showSubmenu(['batches.index']) }}">
                        <li>
                            <a class="{{ isChildActive('batches.index') }}"
                                href="{{ route('batches.index') }}">
                                Batch List
                            </a>
                        </li>
                    </ul>
                </li>

            @endif


            {{-- ========================================================= --}}
            {{-- SALES MENU (role = 3)                                    --}}
            {{-- ========================================================= --}}
            @if(Auth::check() && Auth::user()->role == 3)

                
                {{-- Dashboard --}}
                <li class="{{ isParent(['dashboard']) }}">
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
                    </ul>
                </li>

                {{-- Students --}}
                <li class="{{ isParent(['sales.students.index','students.create']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Students Confirmation</span>
                    </a>
                    <ul class="{{ showSubmenu(['sales.students.index','students.create']) }}">
                        <li>
                            <a class="{{ isChildActive('sales.students.index') }}"
                                href="{{ route('sales.students.index') }}">
                                Students List
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Leads --}}
                <li class="{{ isParent(['leads']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Leads</span>
                    </a>
                    <ul class="{{ showSubmenu(['leads']) }}">
                        <li>
                            <a class="{{ isChildActive('leads') }}"
                                href="{{ route('leads.index') }}">
                                Leads
                            </a>
                        </li>
                    </ul>
                </li>

            @endif

        </ul>
    </div>
</div>
