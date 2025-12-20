<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">

            {{-- Admin Menu --}}
            @if(Auth::check() && Auth::user()->role === 'admin')
                <li class="nav-label first"></li>

                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('dashboard') }}">Dashboard Overview</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Sessions</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('sessions.index') }}">Session List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-book"></i>
                        <span class="nav-text">Courses</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('courses.index') }}">Courses List</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-university"></i>
                        <span class="nav-text">Colleges</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('colleges.index') }}">College List</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Trainers</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('trainers.index') }}">Trainers List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Batches</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('batches.index') }}">Batch List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span class="nav-text">Departments</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('departments.index') }}">Department List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-address-book"></i> {{-- You can change the icon --}}
                        <span class="nav-text">References</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('references.index') }}">Reference List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Students Confirmation</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('students.index') }}">Students List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-pen-to-square"></i> {{-- Icon for Tests --}}
                        <span class="nav-text">Tests</span>
                    </a>
                    <ul aria-expanded="false">  
                        <li><a href="{{ route('admin.tests.index') }}">Tests List</a></li>
                    </ul>
                </li>

                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-certificate"></i>
                        <span class="nav-text">Students Certification</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('certificates.index') }}">Students</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-graduation-cap"></i> {{-- Or choose another icon --}}
                        <span class="nav-text">Student Verification</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('student_certificates.index') }}">Student Certificates List</a></li>
                    </ul>
                </li>
                 <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                         <i class="fas fa-users"></i>  
                        <span class="nav-text">Users</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('users.index') }}">Users List</a></li>
                    </ul>
                </li>
            @endif
            {{-- Manager --}}
                        @if(Auth::check() && Auth::user()->role === 'manager')
                <li class="nav-label first"></li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('dashboard') }}">Dashboard Overview</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Sessions</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('sessions.index') }}">Session List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-book"></i>
                        <span class="nav-text">Courses</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('courses.index') }}">Courses List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-university"></i>
                        <span class="nav-text">Colleges</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('colleges.index') }}">College List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Trainers</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('trainers.index') }}">Trainers List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Batches</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('batches.index') }}">Batch List</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span class="nav-text">Departments</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('departments.index') }}">Department List</a></li>
                    </ul>
                </li>
                <li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Students Confirmation</span>
                    </a>
                    <ul aria-expanded="false">
                         <li><a href="{{ route('manager.students.index') }}">Students List</a></li>
                    </ul>
                </li>
            @endif
            {{-- Sales Menu --}}
            @if(Auth::check() && Auth::user()->role === 'sales')
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('dashboard') }}">Dashboard Overview</a></li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Students</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('sales.students.index') }}">Students List</a></li>
                        <li><a href="{{ route('students.create') }}">Register Students</a></li>
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</div>
