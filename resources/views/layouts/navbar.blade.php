@php
    // highlight child link only
    function isChildActive1($route)
    {
        return request()->routeIs($route) ? 'active' : '';
    }

    function isChildActive($route)
    {
        return request()->routeIs($route) ? 'mm-active' : '';
    }

    // expand submenu
    function showSubmenu($routes)
{
    $routeName = request()->route()->getName();
    $segments  = explode('.', $routeName);

    foreach ((array) $routes as $route) {
        $key = trim(str_replace('*', '', $route));

        if (in_array($key, $segments, true)) {
            return 'mm-show';
        }
    }

    return '';
}

    function showSubmenu2($routes)
{
    $routeName = request()->route()->getName();

    foreach ((array) $routes as $route) {

        if (str_contains($route, '*')) {
            $key = str_replace(['*', '.'], '', $route);

            if (str_contains(str_replace('.', '', $routeName), $key)) {
                return 'mm-show';
            }
        }

        if (request()->routeIs($route)) {
            return 'mm-show';
        }
    }

    return '';
}

    function showSubmenu1($routes)
    {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) return 'mm-show';
        }
        return '';
    }

    // mark parent li active ONLY to open submenu (NOT highlight purple)
    function isParent1($routes)
    {
        foreach ($routes as $route) {
            if (request()->routeIs($route)) return 'mm-active';
        }
        return '';
    }
function isParent($routes)
{
    $routeName = request()->route()->getName(); // e.g. verify-students.index
    $segments  = explode('.', $routeName);     // ['verify-students','index']

    foreach ((array) $routes as $route) {
        $key = trim(str_replace('*', '', $route));

        if (in_array($key, $segments, true)) {
            return 'mm-active';
        }
    }

    return '';
}

    function isParent2($routes)
{
    $routeName = request()->route()->getName();

    foreach ((array) $routes as $route) {

        // If wildcard is used → strip it and do contains
        if (str_contains($route, '*')) {
            $key = str_replace(['*', '.'], '', $route);

            if (str_contains(str_replace('.', '', $routeName), $key)) {
                return 'mm-active';
            }
        }

        // Normal Laravel routeIs
        if (request()->routeIs($route)) {
            return 'mm-active';
        }
    }

    return '';
}
 

@endphp
<style>
    /* Highlight active menu item at ANY depth */
.metismenu li.mm-active > a {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.1);
}

@media (min-width: 991px){
/* DESKTOP SIDEBAR COLLAPSE */

body.menu-toggle .quixnav {
    width: 80px !important;
}

body.menu-toggle .quixnav .nav-text,
body.menu-toggle .quixnav .has-arrow:after,
body.menu-toggle .quixnav ul ul {
    display: none !important;
}

body.menu-toggle .content-body {
    margin-left: 80px !important;
}

body.menu-toggle .header {
    /*padding-left: 80px !important;*/
}/*
.nav-control{
    cursor:pointer;
    font-size:20px;
    color:#6c757d;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:0.3s;
}

.nav-control:hover{
    color:#593bdb;
}*/
/*.nav-control{
    cursor:pointer;
    font-size:20px;
    color:#6c757d;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:all .3s ease;
}

.nav-control:hover{
    color:#593bdb;
}*/
.nav-control{
    position: relative !important;
    top: auto !important;
    right: auto !important;
    left: auto !important;

    width: auto !important;
    height: auto !important;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-right: 15px;
    cursor: pointer;
}

.nav-control i{
    font-size: 22px;
    color: #6c757d;
    transition: .3s;
}

.nav-control:hover i{
    color: #593bdb;
}
 
.sidebar-collapse-btn{
    position: absolute;
    top: 90px;
    right: -14px;
    width: 28px;
    height: 60px;
    background: #593bdb;
    color: #fff;
    border-radius: 0 10px 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 999;
    transition: all .3s ease;
    opacity: 0;
}

/* SHOW ON SIDEBAR HOVER */
.quixnav:hover .sidebar-collapse-btn{
    opacity: 1;
}

/* ICON ANIMATION */
.sidebar-collapse-btn i{
    transition: all .3s ease;
    font-size: 14px;
}

/* ROTATE ICON WHEN COLLAPSED */
body.menu-toggle .sidebar-collapse-btn i{
    transform: rotate(180deg);
}

/* MOVE BUTTON WHEN COLLAPSED */
body.menu-toggle .sidebar-collapse-btn{
    right: -14px;
}

}
</style>

<div class="quixnav">
    <div class="sidebar-collapse-btn" id="sidebarToggle">
    <i class="fas fa-chevron-left"></i>
</div>
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
                {{-- Analytics --}}
                <li class="{{ isParent(['admin.analytics']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-text">Analytics</span>
                    </a>

                    <ul class="{{ showSubmenu(['admin.analytics']) }}">

                        {{-- Overview --}}
                        <li>
                            <a href="{{ route('admin.analytics') }}"
                               class="{{ request()->routeIs('admin.analytics') && !request('tab') ? 'mm-active' : '' }}">
                                Overview
                            </a>
                        </li>

                         {{-- Sales --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'leads']) }}"
                               class="{{ request('tab') == 'leads' ? 'mm-active' : '' }}">
                                Sales
                            </a>
                        </li>

                        {{-- Mentors --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'trainers']) }}"
                               class="{{ request('tab') == 'trainers' ? 'mm-active' : '' }}">
                                Mentors
                            </a>
                        </li>

                        {{-- Students --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'students']) }}"
                               class="{{ request('tab') == 'students' ? 'mm-active' : '' }}">
                                Students
                            </a>
                        </li>

                        {{-- Company General --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'company']) }}"
                               class="{{ request('tab') == 'company' ? 'mm-active' : '' }}">
                                Company General
                            </a>
                        </li>


                        {{-- Ads Management --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'ads']) }}"
                               class="{{ request('tab') == 'ads' ? 'mm-active' : '' }}">
                                Ads Management
                            </a>
                        </li>

                        {{-- HR Management --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'hr']) }}"
                               class="{{ request('tab') == 'hr' ? 'mm-active' : '' }}">
                                HR Management
                            </a>
                        </li>

                         {{-- Security --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'security']) }}"
                               class="{{ request('tab') == 'security' ? 'mm-active' : '' }}">
                                Security
                            </a>
                        </li>

                        {{-- Tests --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'tests']) }}"
                               class="{{ request('tab') == 'tests' ? 'mm-active' : '' }}">
                                Tests
                            </a>
                        </li>

                        {{-- Workshops --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'workshops']) }}"
                               class="{{ request('tab') == 'workshops' ? 'mm-active' : '' }}">
                                Workshops
                            </a>
                        </li>
                        {{-- Student Finance --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'student-finance']) }}"
                               class="{{ request('tab') == 'student-finance' ? 'mm-active' : '' }}">
                                Student Finance
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Sessions --}}
                <li class="{{ isParent(['sessions*']) }}">
                    <a class="" href="{{ route('sessions.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Sessions</span>
                    </a>
                     
                </li>

                 {{-- Courses --}}
                <li class="{{ isParent(['courses*']) }}">
                    <a  href="{{ route('courses.index') }}">
                        <i class="fas fa-book"></i>
                        <span class="nav-text">Technologies</span>
                    </a>
                     
                </li>

                 {{-- Colleges Locations --}}
                <li class="{{ isParent(['states*','districts*']) }}">
                    <a href="{{ route('states.index') }}">
                        <i class="fas fa-university"></i>
                        <span class="nav-text">Colleges Locations</span>
                    </a>
                </li>

                {{-- Colleges --}}
                <li class="{{ isParent(['colleges*','mous*', 'hods*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-university"></i>
                        <span class="nav-text">Colleges</span>
                    </a>
                    <ul class="{{ showSubmenu(['colleges*','mous*', 'hods*']) }}">
                       {{-- Colleges --}}
                        <li class="{{ isParent(['colleges*']) }}">
                            <a href="{{ route('colleges.index') }}">
                               
                                <span class="nav-text">Colleges</span>
                            </a>
                             
                        </li>

                        <li>
                            <a class="{{ isChildActive('mous*') }}"
                                href="{{ route('mous.index') }}">
                                College MoU
                            </a>
                        </li> 

                        <li>
                            <a class="{{ isChildActive('hods*') }}"
                               href="{{ route('hods.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                               College Authority
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('college-mistakes*') }}"
                               href="{{ route('college-mistakes.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                               College Mistakes
                            </a>
                        </li> 

                        {{-- College Exams --}}
                            <li class="{{ 
                                (isParent(['tests.*','test-categories.*'])|| request()->is('admin/questions*')  || request()->is('admin/tests*')) 
                                ? 'mm-active' : '' 
                            }}">
                                <a class="has-arrow" href="javascript:void(0)">
                                    <i class="fas fa-pen-to-square"></i>
                                    <span class="nav-text">College Exams</span>
                                </a>
                                <ul class="{{ showSubmenu(['tests*','test-categories.*']) ||  request()->is('admin/tests*')  }}">
                                    <li>
                                        <a class="{{ isChildActive('test-categories.*') }}"
                                            href="{{ route('test-categories.index') }}">
                                            Exam Category
                                        </a>
                                    </li>
                                    <li>
                                          <a class="{{ request()->is('admin/tests*') ? 'mm-active' : '' }}"
                                            href="{{ route('admin.tests.index') }}">
                                            Online Exams
                                        </a>
                                    </li>
                                   
                                     <li class="{{ request()->routeIs('admin.external-attendance.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('admin.external-attendance.index') }}">
                                            Attendance Form
                                        </a>
                                    </li>

                                    
                                    <li class="{{ request()->routeIs('admin.test.analytics') ? 'mm-active' : '' }}">
                                        <a href="{{ route('admin.test.analytics') }}">
                                            Test Analytics
                                        </a>
                                    </li>
                                    
                                     <!-- <li>
                                        <a class="{{ isChildActive('admin.offline-tests.index') }}"
                                            href="{{ route('admin.offline-tests.index') }}">
                                            Offline Exams
                                        </a>
                                    </li> -->
                                </ul>
                            </li>                             
                    </ul>
                </li>

                 {{-- Workshop --}}
                <li class="{{ isParent(['workshops*','admin.college-emails*', 'admin.college-calls*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-tools"></i>
                        <span class="nav-text">Workshop</span>
                    </a>
                    <ul class="{{ showSubmenu(['workshops*','admin.college-emails*', 'admin.college-calls*']) }}">
                         
                         <li class="{{ isParent(['workshops*']) }}">
                            <a href="{{ route('workshops.index') }}">
                                <!-- <i class="fa-regular fa-file-lines"></i> -->
                                <span class="nav-text">Workshops</span>
                            </a>
                        </li>

                        <li class="{{ isParent(['workshop-expenses*']) }}">
                            <a href="{{ route('workshop-expenses.index') }}">
                                <!-- <i class="fa-regular fa-file-lines"></i> -->
                                <span class="nav-text">Workshop Expenses</span>
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('admin.college-emails*') }}"
                               href="{{ route('admin.college-emails.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                              Colleges Emails Records
                            </a>
                        </li> 

                        <li>
                            <a class="{{ isChildActive('admin.college-calls*') }}"
                               href="{{ route('admin.college-calls.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                              Colleges Calls Records
                            </a>
                        </li> 

                        <li class="{{ isParent(['notifications.byType*']) }}">
                            <a href="{{ route('notifications.byType', 'workshop.reminder.week') }}">
                                <!-- <i class="fa-regular fa-file-lines"></i> -->
                                <span class="nav-text">Workshop Notification</span>
                            </a>
                        </li>
                            
                    </ul>
                </li>

               
                 

                 


                 {{-- Leads --}}
                <li class="{{ isParent(['enquiries*','admin.enquiries.dashboard','salespersons*','admin.enquiries.performance','registrations*','admin.calls','sales_staff*','sales-staff-letters*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Sales Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['enquiries*','admin.enquiries.dashboard','salespersons','admin.enquiries.performance','registrations*','admin.calls','sales_staff*','sales-staff-letters*']) }}">
                        <li>
                            <a class="{{ isChildActive('enquiries*') }}"
                                href="{{ route('enquiries.index') }}">
                               Manage  Sales Data
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('sales_staff*') }}"
                                href="{{ route('sales_staff.index') }}">
                                Manage Sales Teams
                            </a>
                        </li>
                        <li class="{{ isParent(['sales-staff-letters*']) }}">
                            <a href="{{ route('sales-staff-letters.index') }}">
                                <!-- <i class="fas fa-chalkboard-teacher"></i> -->
                                <span class="nav-text">Sale Staff Letters</span>
                            </a>
                            
                        </li>
                        <li>
                            <a class="{{ isChildActive('salespersons*') }}"
                                href="{{ route('salespersons.list') }}">
                                Sales Teams Assigned Data
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
                                Pending Registrations
                            </a>
                        </li>

                        <li class="{{ request()->route('type') == 'student.registered.summary' ? 'mm-active' : '' }}">
                            <a href="{{ route('notifications.byType', 'student.registered.summary') }}">
                                Pending Registrations Notification
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('notifications.byType', 'sales.leads.low.percent.admin') }}"
                               class="{{ request()->route('type') == 'sales.leads.low.percent.admin' ? 'mm-active' : '' }}">
                                Low Leads Alerts
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.manual_data.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.manual_data.index') }}">
                                Manual Upload Data
                            </a>
                        </li>
                         <li class="{{ request()->routeIs('admin.hard_data.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.hard_data.index') }}">
                                Hard Data
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
                {{-- Leads --}}
                <li class="{{ isParent(['passouts*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Passout Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['passouts*']) }}">
                        <li>
                            <a class="{{ isChildActive('passouts*') }}"
                                href="{{ route('passouts.index') }}">
                               Manage Passout Data
                            </a>
                        </li>
                                                
                    </ul>
                </li>

                 {{-- Trainers --}}
                <li class="{{ isParent(['trainers*','batches*','trainer-letters*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Mentor Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['trainers*','batches*','trainer-letters*']) }}">
                        {{-- Trainers --}}
                        <li class="{{ isParent(['trainers*']) }}">
                            <a href="{{ route('trainers.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="nav-text">Mentors</span>
                            </a>
                            
                        </li>


                        {{-- Batches --}}
                        <li class="{{ isParent(['batches*']) }}">
                            <a href="{{ route('batches.index') }}">
                                <i class="fas fa-layer-group"></i>
                                <span class="nav-text">Batches</span>
                            </a>
                             
                        </li>
                        <li class="{{ isParent(['trainer-letters*']) }}">
                            <a href="{{ route('trainer-letters.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="nav-text">Mentors Letters</span>
                            </a>
                            
                        </li>
                    </ul>
                </li>

                {{-- Student Main Admin --}}
                <li class="{{ isParent(['students*','certificates*','close_student*','student-evaluations*','fee.status','student-additional-letters*','student-accepted-letters*','admin.office-tests*','admin.office-online-tests*','student-custom-letters*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Student Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['students*','certificates*','close_student*','student-evaluations*','fee.status','student-additional-letters*','student-accepted-letters*','admin.office-tests*','admin.office-online-tests*','student-custom-letters*']) }}">
                        

                        
                        {{-- Students Confirmation --}}
                        <li class="{{ isParent(['students*']) }}">
                            <a  href="{{ route('students.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Students Confirmations</span>
                            </a>
                            
                        </li>

                        {{-- Certificates --}}
                        <li class="{{ isParent(['certificates*']) }}">
                            <a href="{{ route('certificates.index') }}">
                                <i class="fas fa-certificate"></i>
                                <span class="nav-text">Students Certifications</span>
                            </a>
                             
                        </li>

                        {{-- Certificates --}}
                        <li class="{{ isParent(['close_student*']) }}">
                            <a href="{{ route('close_student.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Close Students</span>
                            </a>
                             
                        </li>   
                         <li class="{{ isParent(['student-evaluations*']) }}">
                            <a href="{{ route('student-evaluations.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Students Evaluations</span>
                            </a>
                             
                        </li>    
                        <li class="{{ isParent(['fee.status']) }}">
                            <a href="{{ route('fee.status') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Payments (Fee) Status</span>
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('student-additional-letters*') }}"
                                href="{{ route('student-additional-letters.index') }}">
                                <i class="fas fa-file-signature"></i>
                                <span class="nav-text">Student Letters</span>
                            </a>
                        </li> 

                        <li>
                            <a class="{{ isChildActive('student-custom-letters*') }}"
                                href="{{ route('student-custom-letters.index') }}">
                                <i class="fas fa-file-signature"></i>
                                <span class="nav-text">Student Custom Letters</span>
                            </a>
                        </li>  

                        <li>
                            <a class="{{ isChildActive('student-accepted-letters*') }}"
                                href="{{ route('student-accepted-letters.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Accepted Letters
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('admin.office-tests.*') ? 'mm-active' : '' }}">
                            <a 
                                href="{{ route('admin.office-tests.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Student Office Exams
                            </a>
                        </li>

                        <!-- <li class="{{ request()->routeIs('admin.office-online-tests.*') ? 'mm-active' : '' }}"> -->
                        <li class="{{ request()->is('admin/office-online-tests*') ? 'mm-active' : '' }}">
                            <a 
                                href="{{ route('admin.office-online-tests.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Student Online Exams
                            </a>
                        </li> 

                        <li>
                            <a class="{{ isChildActive('admin.pending_request.index*') }}"
                                href="{{ route('admin.pending_request.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Registration Request
                            </a>
                        </li> 

                        <li>
                            <a class="{{ isChildActive('admin.student.leave*') }}"
                                href="{{ route('admin.student.leave.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Students Leaves
                            </a>
                        </li>   

                        <li class="{{ request()->route('type') == 'fee.pending.summary' ? 'mm-active' : '' }}">
                            <a href="{{ route('notifications.byType', 'fee.pending.summary') }}">
                                <i class="fas fa-file-signature"></i>
                                Fee Notification
                            </a>
                        </li> 
                        <li class="{{ request()->route('type') == 'bin.ready.summary' ? 'mm-active' : '' }}">
                            <a href="{{ route('notifications.byType', 'bin.ready.summary') }}">
                                <i class="fas fa-file-signature"></i>
                                BIN Ready Notification
                            </a>
                        </li> 
                        {{-- Student help desk start--}}               
                        {{-- Help Desk --}}
                            <li class="{{ request()->routeIs('admin.helpdesk.*') || isParent(['projects*','tutorials*']) ? 'mm-active' : '' }}">
                                <a class="has-arrow" href="javascript:void(0)">
                                    <i class="fas fa-pen-to-square"></i>
                                    <span class="nav-text">Student Help Desk</span>
                                </a>
                                <ul class="{{ request()->routeIs('admin.helpdesk.*') || showSubmenu(['projects*','tutorials*']) ? 'mm-show' : '' }}">
                                     <li>
                                        <a class="{{ isChildActive('projects*') }}"
                                           href="{{ route('projects.index') }}">
                                            <!-- <i class="fas fa-user-clock me-2"></i> -->
                                            Student Projects
                                        </a>
                                    </li>

                                    <li>
                                        <a class="{{ isChildActive('tutorials*') }}"
                                           href="{{ route('tutorials.index') }}">
                                            <!-- <i class="fas fa-bed me-2"></i> -->
                                           Student Tutorials
                                        </a>
                                    </li>

                                    @foreach($helpdeskCategories as $cat)

                                    <li class="{{ request()->get('category') == $cat->id ? 'mm-active' : '' }}">

                                        <a href="{{ route('admin.helpdesk.articles.index',['category'=>$cat->id]) }}">

                                            {{ $cat->name }}

                                        </a>

                                    </li>

                                    @endforeach
                                    <li class="{{ request()->routeIs('admin.helpdesk.categories.index.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('admin.helpdesk.categories.index') }}">
                                            
                                            <span class="nav-text">Helpdesk Categories</span>
                                        </a>
                                    </li>

                                    <li class="{{ request()->routeIs('student-projects.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('student-projects.index') }}">
                                            <span class="nav-text">Projects</span>
                                        </a>
                                    </li>

                                    <li class="{{ request()->routeIs('student-project-assignments.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('student-project-assignments.index') }}">
                                            <span class="nav-text">Projects Assignments</span>
                                        </a>
                                    </li>

                                     <li class="{{ request()->routeIs('student-project-submissions.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('student-project-submissions.index') }}">
                                            <span class="nav-text">Projects Submissions</span>
                                        </a>
                                    </li>

                                    <li class="{{ request()->routeIs('student-project-reviews.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('student-project-reviews.index') }}">
                                            <span class="nav-text">Projects Reviews</span>
                                        </a>
                                    </li>

                                     <li class="{{ request()->routeIs('admin.student.cv-templates.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('admin.student.cv-templates.index') }}">
                                            <span class="nav-text">CVs Templates</span>
                                        </a>
                                    </li>

                                    <li class="{{ request()->routeIs('admin.student.cv.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('admin.student.cv.index') }}">
                                            <span class="nav-text">Generated CVs</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('student_ppt.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('student_ppt.index') }}">
                                            <span class="nav-text">Students PPT</span>
                                        </a>
                                    </li> 

                                   <!--  <li class="{{ request()->routeIs('admin.helpdesk.categories.*') ? 'mm-active' : '' }}">
                                        <a href="{{ route('admin.helpdesk.articles.index') }}">
                                            
                                            <span class="nav-text">Articles</span>
                                        </a>
                                    </li> -->

                                   
                                </ul>
                            </li>
                        {{-- Student help desk end--}}   

                         {{-- Student Services --}}
                <li class="{{ isParent(['pgs*','part-time-jobs*','placement-companies*','placements*','references*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tasks"></i>
                        <span class="nav-text">Student Services</span>
                    </a>
                    <ul class="{{ showSubmenu(['pgs*','part-time-jobs*','placement-companies*','placements*','references*']) }}">

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

                         {{-- Placement --}}
                        <li class="{{ isParent(['placements.*']) }}">
                            <a href="{{ route('placements.index') }}">
                                <!-- <i class="fa-solid fa-photo-film"></i> -->
                                <span class="nav-text">Placed Students</span>
                            </a>
                        </li>


                        {{-- References --}}
                        <li class="{{ isParent(['references.*']) }}">
                            <a href="{{ route('references.index') }}">
                                <!-- <i class="fas fa-address-book"></i> -->
                                <span class="nav-text"> Student References</span>
                            </a>
                            
                        </li>
                    </ul>
                </li>              
                    </ul>
                </li>


                 

               
               <!--  <li class="{{ isParent(['admin.helpdesk.*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Helpdesk</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.helpdesk.*']) }}">
                        
                    </ul>
                </li> -->

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
                    
                    {{-- Verify Students --}}
                <li class="{{ isParent(['verify-students*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Verify Students</span>
                    </a>
                    <ul class="{{ showSubmenu(['verify-students*']) }}">
                        <li>
                            <a class="{{ isChildActive('verify-students-index.index') }}"
                                href="{{ route('verify-students-index.index') }}">
                                Verify Students Link
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('verify-students.index') }}"
                                href="{{ route('verify-students.index') }}">
                                Verify Students Lists
                            </a>
                        </li>
                    </ul>
                </li>

                  
           

                 {{-- Attendence --}}
                <!-- <li class="{{ isParent(['attendance.employees']) }}">
                    <a href="{{ route('attendance.employees') }}">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Employee Attendence </span>
                    </a>
                </li> -->

                {{-- HR Management --}}
                <li class="{{ isParent(['attendance*','employees*','letters*','salary-slips*','accepted-letters*','managements_letters*','jd*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">HR Management </span>
                    </a>
                    <ul class="{{ showSubmenu(['attendance*','employees*','letters*','salary-slips*','accepted-letters*','managements_letters*','jd*']) }}">
                        <li>
                            <a class="{{ isChildActive('employees*') }}"
                                href="{{ route('employees.index') }}">
                                Employees Lists 
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('attendance*') }}"
                                href="{{ route('attendance.employees') }}">
                                Employees Attendence 
                            </a>
                        </li>
                        
                         <li>
                            <a class="{{ isChildActive('letters*') }}"
                                href="{{ route('letters.index') }}">
                                Emp Official Letters
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('managements_letters*') }}"
                                href="{{ route('managements_letters.index') }}">
                                Management Official Letters
                            </a>
                        </li>
                        

                         <li>
                            <a class="{{ isChildActive('accepted-letters*') }}"
                                href="{{ route('accepted-letters.index') }}">
                                Emp Signed Letters
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('salary-slips*') }}"
                                href="{{ route('salary-slips.index') }}">
                                Emp Salary Slips
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('jd.*') }}"
                                href="{{ route('jd.index') }}">
                                Job Desciptions
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('admin.employee.leave*') }}"
                                href="{{ route('admin.employee.leave.index') }}">
                                Employees Leaves
                            </a>
                        </li>
                        
                         {{-- Interviews --}}
                <li class="{{ isParent(['technologies.*','interview-questions.practice','interview-questions*','interviews*','cvs*','daily-interviews*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Interviews</span>
                    </a>
                    <ul class="{{ showSubmenu(['technologies.*','interview-questions.practice','interview-questions*','interviews*','cvs*','daily-interviews*']) }}">
                        <li>
                            <a class="{{ isChildActive('technologies*') }}"
                                href="{{ route('technologies.index') }}">
                                Technologies
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('interview-questions*') }}"
                                href="{{ route('interview-questions.index') }}">
                                Interview Questions
                            </a>
                        </li>

                         <li>
                            <a class="{{ isChildActive('interview-questions.practice') }}"
                                href="{{ route('interview-questions.practice') }}">
                                Interview Q&As
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('interviews*') }}"
                                href="{{ route('interviews.index') }}">
                                Candidates
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('cvs*') }}"
                               href="{{ route('cvs.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                                CVs or Resumes
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('daily-interviews*') }}"
                               href="{{ route('daily-interviews.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                                Daily Interview Schedules
                            </a>
                        </li>
                        <li class="{{ request()->route('type') == 'admin.interviews.today' ? 'mm-active' : '' }}">
                            <a href="{{ route('notifications.byType', 'admin.interviews.today') }}">
                                Today Interviews Notification
                            </a>
                        </li>
                    </ul>
                </li>

                         
                    </ul>
                </li>

                 

                 
                <li class="{{ isParent(['student.events*','college.events*','upcoming-events*','employee.events.*','company_ppt*','brochures*','company_profile*','scanners*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Company Uses</span>
                    </a>
                    <ul class="{{ showSubmenu(['student.events*','college.events*','upcoming-events*','employee.events.*','company_ppt*','brochures*','company_profile*','scanners*']) }}">
                        
                        <li>
                            <a class="{{ isChildActive('upcoming-events.*') }}"
                               href="{{ route('upcoming-events.index') }}">
                              Upcoming Events
                            </a>
                        </li>
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

                        
                         {{-- Brochures --}}

                    <li class="{{ isParent(['brochures.*']) }}">
                        <a href="{{ route('brochures.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Manage Brochures</span>
                        </a>
                    </li>
                     {{-- Brochures --}}
                    <li class="{{ isParent(['company_profile*']) }}">
                        <a href="{{ route('company_profile.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Company Profile Manage</span>
                        </a>
                    </li>

                    <li class="{{ isParent(['company_ppt.*']) }}">
                        <a href="{{ route('company_ppt.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Company PPT Manage</span>
                        </a>
                    </li>
                    <li class="{{ isParent(['scanners*']) }}">
                        <a href="{{ route('scanners.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Social Share Scanners</span>
                        </a>
                    </li>  
                    <li class="{{ request()->route('type') == 'upcoming.event' ? 'mm-active' : '' }}">
                        <a href="{{ route('notifications.byType', 'upcoming.event') }}">
                            Upcoming Events Notification
                        </a>
                    </li>                      
                    </ul>
                </li>


                 {{-- Ads Management --}}
                <li class="{{ isParent(['internship-registrations*','pages*','services-registrations*','products-registrations*','single-product-registrations*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Ads Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['internship-registrations*','pages*','services-registrations*','products-registrations*','single-product-registrations*']) }}">
                        <li>
                            <a class="{{ isChildActive('pages.*') }}"
                                href="{{ route('pages.index') }}">
                                Ads Pages
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('internship-registrations*') }}"
                                href="{{ route('internship-registrations.index') }}">
                                Internship Entries
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('services-registrations*') }}"
                                href="{{ route('services-registrations.index') }}">
                                Services Entries
                            </a>
                        </li>
                        
                        <li>
                            <a class="{{ isChildActive('products-registrations*') }}"
                                href="{{ route('products-registrations.index') }}">
                                Products Entries
                            </a>
                        </li>
                        
                        <li>
                            <a class="{{ isChildActive('single-product-registrations*') }}"
                                href="{{ route('single-product-registrations.index') }}">
                                Single Products Entries
                            </a>
                        </li>
                        
                         <li class="{{ request()->routeIs('testimonials.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('testimonials.index') }}">
                                Testimonials
                            </a>
                        </li>
                         <li class="{{ request()->routeIs('admin.ads.analytics') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.ads.analytics') }}">
                                Ads Analytics
                            </a>
                        </li>
                    </ul>
                </li>

                 <li class="{{ isParent(['admin.form-entries.*','admin.gmail*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Gmail</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.form-entries.*','admin.gmail*']) }}">
                        <li class="{{ request()->routeIs('admin.form-entries.*') ? 'mm-active' : '' }}">
                          <a href="{{ route('admin.form-entries.index') }}">
                              <i class="fas fa-file-alt"></i>
                              <span class="nav-text">Gmail Form Entries</span>
                          </a>
                        </li>
                        <li class="{{ isParent(['admin.gmail*']) }}">

                            <a href="{{ route('admin.gmail.index') }}">

                                <i class="fas fa-envelope"></i>

                                <span class="nav-text">Gmail (Queries & HR)</span>

                            </a>

                        </li>
                    </ul>
                </li>

                 {{-- Gmail Form Entries --}}
                
                       

                {{-- Blocked Numbers --}}
                <li class="{{ isParent(['blocked-numbers.*']) }}">
                    <a href="{{ route('admin.blocked-numbers.index') }}">
                        <i class="fas fa-certificate"></i>
                        <span class="nav-text">Blocked Numbers</span>
                    </a>
                     
                </li>

                {{-- Security --}}
                <li class="{{ isParent(['admin.blocked-ips.*','admin.system-activity*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Security</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.blocked-ips.*','admin.system-activity*']) }}">
                        <li class="{{ request()->routeIs('admin.blocked-ips.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.blocked-ips.index') }}">
                                <i class="fas fa-ban"></i>
                                <span class="nav-text">Blocked IPs</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.allowed-ips.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.allowed-ips.index') }}">
                                <i class="fas fa-check-circle"></i>
                                <span class="nav-text">Allowed IPs (Script Access)</span>
                            </a>
                        </li>

                        {{-- System Activity (logins, page views, IP) --}}
                        <li class="{{ request()->routeIs('admin.system-activity') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.system-activity') }}">
                                <i class="fas fa-history"></i>
                                <span class="nav-text">Activity</span>
                            </a>
                        </li>
                    </ul>
                </li>

                
                 {{-- Users --}}

                 <li class="{{ isParent(['users*']) }}">
                    <a href="{{ route('users.index') }}">
                         <i class="fas fa-users"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                </li>
                <li class="{{ isParent(['finance*']) }}">
                    <a href="{{ route('finance.index') }}">
                         <i class="fas fa-users"></i>
                        <span class="nav-text">Finanace Tabs</span>
                    </a>
                </li>

                 {{-- Finance --}}
                <li class="{{ isParent(['office-expenses*','pantry-expenses*','event-expenses*','travel-expenses*','office-assets*','recharges*','visiting-cards*','office-paper-expenses*','tea-pantry-expenses*','office-cleaning-expenses*','office-accessories-expenses*','office-cleaning-expenses*','office-accessories-expenses*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-coins"></i>
                        <span class="nav-text">Finance Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['office-expenses*','pantry-expenses*','event-expenses*','travel-expenses*','office-assets*','recharges*','visiting-cards*','office-paper-expenses*','tea-pantry-expenses*','office-cleaning-expenses*','office-accessories-expenses*','office-cleaning-expenses*','office-accessories-expenses*']) }}">
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
                            <a class="{{ isChildActive('office-paper-expenses*') }}"
                               href="{{ route('office-paper-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Paper Expenses
                            </a>
                        </li>
                        
                         <li>
                            <a class="{{ isChildActive('tea-pantry-expenses*') }}"
                               href="{{ route('tea-pantry-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Tea Pantry Expenses
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('office-cleaning-expenses*') }}"
                               href="{{ route('office-cleaning-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Cleaning Expenses
                            </a>
                        </li>

                        <li>
                            <a class="{{ isChildActive('office-accessories-expenses*') }}"
                               href="{{ route('office-accessories-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Accessories Expenses
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

                        <li>
                            <a class="{{ isChildActive('recharges*') }}"
                               href="{{ route('recharges.index') }}">
                                <!-- <i class="fas fa-building me-2"></i> -->
                                Recharges
                            </a>
                        </li>

                         <li class="{{ isParent(['visiting-cards*']) }}">
                            <a href="{{ route('visiting-cards.index') }}">
                                <!-- <i class="fa-regular fa-file-lines"></i> -->
                                <span class="nav-text">Visiting Cards</span>
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

             @if(auth()->guard('web')->check())
               @hasanyrole('Manager|HR|Custom')

                     <li class="nav-label first"></li>
                    @canany(['dashboard.view','analytics.view'])
                        {{-- Dashboard --}}
                        <li class="{{ isParent(['dashboard','analytics.index']) }}">
                            <a class="has-arrow" href="javascript:void(0)">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                            <ul class="{{ showSubmenu(['dashboard']) }}">
                                @can('dashboard.view')<li>
                                    <a class="{{ isChildActive('dashboard') }}"
                                        href="{{ route('dashboard') }}">
                                        Dashboard Overview
                                    </a>
                                </li>
                                @endcan
                                @can('analytics.view')
                                <li>
                                    <a class="{{ isChildActive('analytics.index') }}"
                                        href="{{ route('analytics.index') }}">
                                        Analytics
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanany

                        {{-- Analytics --}}
                        @can('all_analytics.view')
                <li class="{{ isParent(['admin.analytics']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-text">Analytics</span>
                    </a>

                    <ul class="{{ showSubmenu(['admin.analytics']) }}">

                        {{-- Overview --}}
                        <li>
                            <a href="{{ route('admin.analytics') }}"
                               class="{{ request()->routeIs('admin.analytics') && !request('tab') ? 'mm-active' : '' }}">
                                Overview
                            </a>
                        </li>

                         {{-- Sales --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'leads']) }}"
                               class="{{ request('tab') == 'leads' ? 'mm-active' : '' }}">
                                Sales
                            </a>
                        </li>

                        {{-- Mentors --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'trainers']) }}"
                               class="{{ request('tab') == 'trainers' ? 'mm-active' : '' }}">
                                Mentors
                            </a>
                        </li>

                        {{-- Students --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'students']) }}"
                               class="{{ request('tab') == 'students' ? 'mm-active' : '' }}">
                                Students
                            </a>
                        </li>

                        {{-- Company General --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'company']) }}"
                               class="{{ request('tab') == 'company' ? 'mm-active' : '' }}">
                                Company General
                            </a>
                        </li>


                        {{-- Ads Management --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'ads']) }}"
                               class="{{ request('tab') == 'ads' ? 'mm-active' : '' }}">
                                Ads Management
                            </a>
                        </li>

                        {{-- HR Management --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'hr']) }}"
                               class="{{ request('tab') == 'hr' ? 'mm-active' : '' }}">
                                HR Management
                            </a>
                        </li>

                         {{-- Security --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'security']) }}"
                               class="{{ request('tab') == 'security' ? 'mm-active' : '' }}">
                                Security
                            </a>
                        </li>

                        {{-- Tests --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'tests']) }}"
                               class="{{ request('tab') == 'tests' ? 'mm-active' : '' }}">
                                Tests
                            </a>
                        </li>

                        {{-- Workshops --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'workshops']) }}"
                               class="{{ request('tab') == 'workshops' ? 'mm-active' : '' }}">
                                Workshops
                            </a>
                        </li>
                        {{-- Student Finance --}}
                        <li>
                            <a href="{{ route('admin.analytics', ['tab' => 'student-finance']) }}"
                               class="{{ request('tab') == 'student-finance' ? 'mm-active' : '' }}">
                                Student Finance
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                        @can('sessions.view')
                            {{-- Sessions --}}
                            <li class="{{ isParent(['sessions*']) }}">
                                <a class="" href="{{ route('sessions.index') }}">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="nav-text">Sessions</span>
                                </a>
                                 
                            </li>
                        @endcan

                        @can('courses.view')
                         {{-- Courses --}}
                        <li class="{{ isParent(['courses*']) }}">
                            <a  href="{{ route('courses.index') }}">
                                <i class="fas fa-book"></i>
                                <span class="nav-text">Technologies</span>
                            </a>
                             
                        </li>

                        @endcan
                        @canany(['states.view','districts.view'])

                        <li class="{{ isParent(['states*','districts*']) }}">
                            <a href="{{ route('states.index') }}">
                                <i class="fas fa-university"></i>
                                <span class="nav-text">Colleges Locations</span>
                            </a>
                        </li>

                        @endcanany 

                    @canany(['colleges.view','mous.view','hods.view','tests.view','offline_tests.view','test_categories.view','external_attendance.view','test_analytics.view'])
                        {{-- Colleges --}}

                        <li class="{{ isParent(['colleges*','mous*', 'hods*','tests*']) }}">
                            <a class="has-arrow" href="javascript:void(0)">
                                 <i class="fas fa-university"></i>
                                <span class="nav-text">Colleges</span>
                            </a>
                            <ul class="{{ showSubmenu(['colleges*','mous*', 'hods*','tests*']) }}">
                               {{-- Colleges --}}
                               @can('colleges.view')
                                <li class="{{ isParent(['colleges*']) }}">
                                    <a href="{{ route('colleges.index') }}">
                                       
                                        <span class="nav-text">Colleges</span>
                                    </a>
                                     
                                </li>
                                @endcan
                                @can('mous.view')
                                <li>
                                    <a class="{{ isChildActive('mous*') }}"
                                        href="{{ route('mous.index') }}">
                                        College MoU
                                    </a>
                                </li> 
                                @endcan
                                @can('hods.view')
                                <li>
                                    <a class="{{ isChildActive('hods*') }}"
                                       href="{{ route('hods.index') }}">
                                        <!-- <i class="fas fa-bed me-2"></i> -->
                                       College Authority
                                    </a>
                                </li>
                                @endcan  
                                 @canany(['tests.view','offline_tests.view','test_categories.view','external_attendance.view','test_analytics.view'])
                                <li class="{{ isParent(['tests.*','admin.offline-tests*','test-categories.*']) }}">
                                    <a class="has-arrow" href="javascript:void(0)">
                                        <i class="fas fa-pen-to-square"></i>
                                        <span class="nav-text">College Exams</span>
                                    </a>
                                    <ul class="{{ showSubmenu(['tests*','test-categories.*']) }}">

                                         @can('test_categories.view')
                                        <li>
                                            <a class="{{ isChildActive('test-categories.*') }}"
                                                href="{{ route('test-categories.index') }}">
                                                Exam Category
                                            </a>
                                        </li>
                                        @endcan   
                                        
                                        @can('tests.view')
                                        <li>
                                            <a class="{{ isChildActive('tests.*') }}"
                                                href="{{ route('admin.tests.index') }}">
                                                Online Exams
                                            </a>
                                        </li>
                                        @endcan
                                        @can('external_attendance.view')
                                        <li class="{{ request()->routeIs('admin.external-attendance.*') ? 'mm-active' : '' }}">
                                            <a href="{{ route('admin.external-attendance.index') }}">
                                                Attendance Form
                                            </a>
                                        </li>
                                        @endcan
                                        

                                        @can('test_analytics.view')
                                        <li class="{{ request()->routeIs('admin.test.analytics') ? 'mm-active' : '' }}">
                                            <a href="{{ route('admin.test.analytics') }}">
                                                Test Analytics
                                            </a>
                                        </li>
                                         @endcan
                                        
                                         <!-- @can('offline_tests.view')

                                         <li>
                                            <a class="{{ isChildActive('admin.offline-tests.index') }}"
                                                href="{{ route('admin.offline-tests.index') }}">
                                                Offline Exams
                                            </a>
                                        </li>
                                        @endcan   --> 
                                    </ul>
                                </li>
                                @endcanany
                                                                       
                            </ul>
                        </li>
                        @endcanany

                         @canany(['workshop.view','college_emails.view','college_calls.view'])
                        {{-- Workshop --}}

                        <li class="{{ isParent([ 'workshops*','admin.college-emails*', 'admin.college-calls*']) }}">
                            <a class="has-arrow" href="javascript:void(0)">
                                 <i class="fas fa-university"></i>
                                <span class="nav-text">Workshop</span>
                            </a>
                            <ul class="{{ showSubmenu(['workshops*','admin.college-emails*', 'admin.college-calls*']) }}">  
                                @can('workshop.view')
                                <li class="{{ isParent(['workshops*']) }}">
                                    <a href="{{ route('workshops.index') }}">
                                        <!-- <i class="fa-regular fa-file-lines"></i> -->
                                        <span class="nav-text">Workshops</span>
                                    </a>
                                </li> 
                                @endcan 

                                @can('college_emails.view')
                                <li>
                                    <a class="{{ isChildActive('admin.college-emails*') }}"
                                       href="{{ route('admin.college-emails.index') }}">
                                        <!-- <i class="fas fa-bed me-2"></i> -->
                                      Colleges Emails Records
                                    </a>
                                </li> 
                                @endcan

                                @can('college_calls.view')
                                <li>
                                    <a class="{{ isChildActive('admin.college-calls*') }}"
                                       href="{{ route('admin.college-calls.index') }}">
                                        <!-- <i class="fas fa-bed me-2"></i> -->
                                      Colleges Calls Records
                                    </a>
                                </li> 
                                @endcan                                        
                            </ul>
                        </li>
                        @endcanany

                          {{-- Tests --}}

                

                @canany(['enquiries.view','salespersons.view','calls.view','registrations.view','manual_data.view','hard_data.view'])
                 {{-- Leads --}}
                <li class="{{ isParent(['enquiries*','admin.enquiries.dashboard','salespersons*','admin.enquiries.performance','registrations*','admin.calls']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Sales Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['enquiries*','admin.enquiries.dashboard','salespersons','admin.enquiries.performance','registrations*','admin.calls']) }}">
                       
                       @can('enquiries.view') <li>
                            <a class="{{ isChildActive('enquiries*') }}"
                                href="{{ route('enquiries.index') }}">
                               Manage  Sales Data
                            </a>
                        </li>
                         @endcan  
                        @can('salespersons.view')
                        <li>
                            <a class="{{ isChildActive('salespersons*') }}"
                                href="{{ route('salespersons.list') }}">
                                Sales Teams
                            </a>
                        </li>
                         @endcan   
                        @can('calls.view')
                        <li>
                            <a class="{{ isChildActive('admin.calls') }}"
                                href="{{ route('admin.calls') }}">
                                Team Status
                            </a>
                        </li>
                         @endcan   
                        @can('registrations.view')
                        <li>
                            <a class="{{ isChildActive('registrations*') }}"
                                href="{{ route('registrations.index') }}">
                                Pending Registrations
                            </a>
                        </li>
                         @endcan   

                         @can('manual_data.view')
                        <li class="{{ request()->routeIs('admin.manual_data.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.manual_data.index') }}">
                                Manual Upload Data
                            </a>
                        </li>
                        @endcan

                        @can('hard_data.view')
                        <li class="{{ request()->routeIs('admin.hard_data.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.hard_data.index') }}">
                                Hard Data
                            </a>
                        </li>
                        @endcan
 
                    </ul>
                </li>
                @endcanany

                @canany(['passouts.view'])
                <li class="{{ isParent(['passouts*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Passout Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['passouts*']) }}">
                        @can('passouts.view')
                        <li>
                            <a class="{{ isChildActive('passouts*') }}"
                                href="{{ route('passouts.index') }}">
                               Manage Passout Data
                            </a>
                        </li>
                        @endcan   
                                                
                    </ul>
                </li>
                @endcanany

                @canany(['mentors.view','batches.view'])
                 {{-- Trainers --}}
                <li class="{{ isParent(['trainers*','batches*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Mentor Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['trainers*','batches*']) }}">
                        {{-- Trainers --}}
                        @can('mentors.view')
                        <li class="{{ isParent(['trainers*']) }}">
                            <a href="{{ route('trainers.index') }}">
                                <i class="fas fa-chalkboard-teacher"></i>
                                <span class="nav-text">Mentors</span>
                            </a>
                            
                        </li>
                        @endcan   

                        {{-- Batches --}}
                        @can('batches.view')
                        <li class="{{ isParent(['batches*']) }}">
                            <a href="{{ route('batches.index') }}">
                                <i class="fas fa-layer-group"></i>
                                <span class="nav-text">Batches</span>
                            </a>
                             
                        </li>
                        @endcan   
                    </ul>
                </li>
                @endcanany



                @canany(['students.view','certificates.view','close_students.view','student_evaluations.view','fee_status.view','student_letters.view','students_office_test.view','student_request.view','online_exam.view','student_leave.view','projects.view','tutorials.view','latest_tech_articles.view','seo_tips.view','interview_preparation_blogs.view','cpanel_explanation.view','hosting.view','faqs_section.view','helpdesk_categories.view','student_projects.view','student_project_assignments.view','student_project_submissions.view','student_project_reviews.view','cv_templates.view','student_cvs.view','student_ppt.view','placement_companies.view','part_time_jobs.view','pgs.view','placements.view','references.view'])
                {{-- Student Main Admin --}}
                <li class="{{ isParent(['students*','certificates*','close_student*','student-evaluations*','fee.status','student-additional-letters*','admin.office-tests*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-user-check"></i>
                        <span class="nav-text">Student Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['students*','certificates*','close_student*','student-evaluations*','fee.status','student-additional-letters*','admin.office-tests*']) }}">
                        

                        
                        {{-- Students Confirmation --}}
                         @can('students.view')
                        <li class="{{ isParent(['students*']) }}">
                            <a  href="{{ route('students.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Students Confirmations</span>
                            </a>
                            
                        </li>
                        @endcan   
                        {{-- Certificates --}}
                         @can('certificates.view')
                        <li class="{{ isParent(['certificates*']) }}">
                            <a href="{{ route('certificates.index') }}">
                                <i class="fas fa-certificate"></i>
                                <span class="nav-text">Students Certifications</span>
                            </a>
                             
                        </li>
                        @endcan   
                        {{-- Certificates --}}
                         @can('close_students.view')
                        <li class="{{ isParent(['close_student*']) }}">
                            <a href="{{ route('close_student.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Close Students</span>
                            </a>
                             
                        </li>
                        @endcan      
                         @can('student_evaluations.view')
                         <li class="{{ isParent(['student-evaluations*']) }}">
                            <a href="{{ route('student-evaluations.index') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Students Report Card</span>
                            </a>
                             
                        </li> 
                        @endcan      
                         @can('fee_status.view')
                        <li class="{{ isParent(['fee.status']) }}">
                            <a href="{{ route('fee.status') }}">
                                <i class="fas fa-user-check"></i>
                                <span class="nav-text">Payments (Fee) Status</span>
                            </a>
                        </li>
                        @endcan   
                         @can('student_letters.view')
                        <li>
                            <a class="{{ isChildActive('student-additional-letters*') }}"
                                href="{{ route('student-additional-letters.index') }}">
                                <i class="fas fa-file-signature"></i>
                                <span class="nav-text">Student Letters</span>
                            </a>
                        </li>
                        @endcan  

                        @can('accepted_letter.view')
                        <li>
                            <a class="{{ isChildActive('student-accepted-letters*') }}"
                                href="{{ route('student-accepted-letters.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Accepted Letters
                            </a>
                        </li>
                        @endcan 
                        @can('students_office_test.view')
                        <li>
                            <a class="{{ isChildActive('admin.office-tests*') }}"
                                href="{{ route('admin.office-tests.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Student Office Exams
                            </a>
                        </li>
                        @endcan

                        @can('online_exam.view')
                        <li>
                            <a class="{{ isChildActive('admin.office-online-tests*') }}"
                                href="{{ route('admin.office-online-tests.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Student Online Exams
                            </a>
                        </li> 


                        @endcan   
                        @can('student_request.view')
                        <li>
                            <a class="{{ isChildActive('admin.pending_request.index*') }}"
                                href="{{ route('admin.pending_request.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Registration Request
                            </a>
                        </li> 
                        @endcan    
                         @can('student_leave.view')
                        <li>
                            <a class="{{ isChildActive('admin.student.leave*') }}"
                                href="{{ route('admin.student.leave.index') }}">
                                <i class="fas fa-file-signature"></i>
                                Students Leaves
                            </a>
                        </li>   
                         @endcan

                          {{-- Help Desk --}}
                 @canany(['projects.view','tutorials.view','latest_tech_articles.view','seo_tips.view','interview_preparation_blogs.view','cpanel_explanation.view','hosting.view','faqs_section.view','helpdesk_categories.view','student_projects.view','student_project_assignments.view','student_project_submissions.view','student_project_reviews.view','cv_templates.view','student_cvs.view','student_ppt.view'])
                <li class="{{ request()->routeIs('admin.helpdesk.*') || isParent(['projects*','tutorials*']) ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Student Help Desk</span>
                    </a>
                    <ul class="{{ request()->routeIs('admin.helpdesk.*') || showSubmenu(['projects*','tutorials*']) ? 'mm-show' : '' }}">
                        @can('projects.view')
                         <li>
                            <a class="{{ isChildActive('projects*') }}"
                               href="{{ route('projects.index') }}">
                                <!-- <i class="fas fa-user-clock me-2"></i> -->
                                Student Projects
                            </a>
                        </li>
                         @endcan   
                         @can('tutorials.view')
                        <li>
                            <a class="{{ isChildActive('tutorials*') }}"
                               href="{{ route('tutorials.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                               Student Tutorials
                            </a>
                        </li>
                         @endcan  
                        
                        @foreach($helpdeskCategories as $cat)

                            @can($cat->slug.'.view')
                                <li class="{{ request()->get('category') == $cat->id ? 'mm-active' : '' }}">
                                    <a href="{{ route('admin.helpdesk.articles.index',['category'=>$cat->id]) }}">
                                        {{ $cat->name }}
                                    </a>
                                </li>
                            @endcan

                        @endforeach

                        @can('helpdesk_categories.view')
                        <li class="{{ request()->routeIs('admin.helpdesk.categories.index.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.helpdesk.categories.index') }}">
                                
                                <span class="nav-text">Helpdesk Categories</span>
                            </a>
                        </li>
                        @endcan
                        @can('student_projects.view')
                        <li class="{{ request()->routeIs('student-projects.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('student-projects.index') }}">
                                <span class="nav-text">Projects</span>
                            </a>
                        </li>
                        @endcan
                        @can('student_project_assignments.view')
                        <li class="{{ request()->routeIs('student-project-assignments.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('student-project-assignments.index') }}">
                                <span class="nav-text">Projects Assignments</span>
                            </a>
                        </li>
                        @endcan
                        @can('student_project_submissions.view')
                         <li class="{{ request()->routeIs('student-project-submissions.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('student-project-submissions.index') }}">
                                <span class="nav-text">Projects Submissions</span>
                            </a>
                        </li>
                        @endcan
                        @can('student_project_reviews.view')
                        <li class="{{ request()->routeIs('student-project-reviews.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('student-project-reviews.index') }}">
                                <span class="nav-text">Projects Reviews</span>
                            </a>
                        </li>
                        @endcan
                        @can('cv_templates.view')
                         <li class="{{ request()->routeIs('admin.student.cv-templates.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.student.cv-templates.index') }}">
                                <span class="nav-text">CVs Templates</span>
                            </a>
                        </li>
                        @endcan
                        @can('student_cvs.view')
                        <li class="{{ request()->routeIs('admin.student.cv.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.student.cv.index') }}">
                                <span class="nav-text">Generated CVs</span>
                            </a>
                        </li> 
                        @endcan 

                        @can('student_ppt.view')
                        <li class="{{ request()->routeIs('student_ppt.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('student_ppt.index') }}">
                                <span class="nav-text">Students PPT</span>
                            </a>
                        </li> 
                        @endcan                      
                    </ul>
                </li>
                @endcanany    

                {{-- services start--}}           

                 @canany(['placement_companies.view','part_time_jobs.view','pgs.view','placements.view','references.view'])
                {{-- Leads --}}
                <li class="{{ isParent(['pgs*','part-time-jobs*','placement-companies*','placements*','references*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-tasks"></i>
                        <span class="nav-text">Student Services</span>
                    </a>
                    <ul class="{{ showSubmenu(['pgs*','part-time-jobs*','placement-companies*','placements*','references*']) }}">

                         @can('placement_companies.view')
                        <li>
                            <a class="{{ isChildActive('placement-companies*') }}"
                               href="{{ route('placement-companies.index') }}">
                                <!-- <i class="fas fa-building me-2"></i> -->
                                Placement Companies
                            </a>
                        </li>
                            @endcan   
                         @can('part_time_jobs.view')
                        <li>
                            <a class="{{ isChildActive('part-time-jobs*') }}"
                               href="{{ route('part-time-jobs.index') }}">
                                <!-- <i class="fas fa-user-clock me-2"></i> -->
                                Part-Time Jobs Companies
                            </a>
                        </li>
                        @endcan   
                         @can('pgs.view')
                        <li>
                            <a class="{{ isChildActive('pgs*') }}"
                               href="{{ route('pgs.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                                Paying Guest
                            </a>
                        </li>
                        @endcan   
                         @can('placements.view')
                         {{-- Placement --}}
                        <li class="{{ isParent(['placements.*']) }}">
                            <a href="{{ route('placements.index') }}">
                                <!-- <i class="fa-solid fa-photo-film"></i> -->
                                <span class="nav-text">Placed Students</span>
                            </a>
                        </li>
                        @endcan   
                         @can('references.view')

                        {{-- References --}}
                        <li class="{{ isParent(['references.*']) }}">
                            <a href="{{ route('references.index') }}">
                                <!-- <i class="fas fa-address-book"></i> -->
                                <span class="nav-text"> Student References</span>
                            </a>
                            
                        </li>
                        @endcan  
                    </ul>
                </li>
                @endcanany

                {{-- services end--}}           
                    </ul>
                </li>   
                @endcanany
                

        

                

                {{-- Joined Students --}}

                @canany(['joined_students.view'])
                <li class="{{ isParent(['joined_students*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Joined Students</span>
                    </a>
                    <ul class="{{ showSubmenu(['joined_students*']) }}">
                          
                         @can('joined_students.view')
                        <li>
                            <a class="{{ isChildActive('joined_students.adminUrl') }}"
                                href="{{ route('joined_students.adminUrl') }}">
                                Joined Students Link
                            </a>
                        </li>
                        @endcan   
                         @can('joined_students.view')
                        <li>
                            <a class="{{ isChildActive('admin.joined_students.index') }}"
                                href="{{ route('joined_students.index') }}">
                                Joined Students Lists
                            </a>
                        </li>
                        @endcan   
                         
                    </ul>
                </li>
                 @endcanany


                    {{-- Verify Students --}}
                    @can('verify_students.view')
                <li class="{{ isParent(['verify-students*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Verify Students</span>
                    </a>
                    <ul class="{{ showSubmenu(['verify-students*']) }}">
                        <li>
                            <a class="{{ isChildActive('verify-students-index.index') }}"
                                href="{{ route('verify-students-index.index') }}">
                                Verify Students Link
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('verify-students.index') }}"
                                href="{{ route('verify-students.index') }}">
                                Verify Students Lists
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan 


             
                



                 {{-- Attendence --}}
              
                @canany(['employees.view','attendance.view','letters.view','accepted_letters.view','salary_slips.view','job_description.view','employee_leave.view','management_letters.view','interview_questions.view','interview_technology.view','interviews.view','cvs.view','daily_interviews.view'])
                {{-- Attendence --}}
                <li class="{{ isParent(['attendance*','employees*','letters*','salary-slips*','accepted-letters*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">HR Management </span>
                    </a>
                    <ul class="{{ showSubmenu(['attendance*','employees*','letters*','salary-slips*','accepted-letters*']) }}">
                        @can('employees.view')
                         <li>
                            <a class="{{ isChildActive('employees*') }}"
                                href="{{ route('employees.index') }}">
                                Employees Lists 
                            </a>
                        </li>
                        @endcan   
                         @can('attendance.view')
                         <li>
                            <a class="{{ isChildActive('attendance*') }}"
                                href="{{ route('attendance.employees') }}">
                                Employees Attendence 
                            </a>
                        </li>
                        @endcan   
                         @can('letters.view')
                         <li>
                            <a class="{{ isChildActive('letters*') }}"
                                href="{{ route('letters.index') }}">
                                Emp Official Letters
                            </a>
                        </li>
                        @endcan  
                         @can('management_letters.view')
                        <li>
                            <a class="{{ isChildActive('managements_letters*') }}"
                                href="{{ route('managements_letters.index') }}">
                                Management Official Letters
                            </a>
                        </li> 
                        @endcan 

                        @can('accepted_letters.view')
                         <li>
                            <a class="{{ isChildActive('accepted-letters*') }}"
                                href="{{ route('accepted-letters.index') }}">
                                Emp Signed Letters
                            </a>
                        </li>
                        @endcan   
                         @can('salary_slips.view')
                        <li>
                            <a class="{{ isChildActive('salary-slips*') }}"
                                href="{{ route('salary-slips.index') }}">
                                Emp Salary Slips
                            </a>
                        </li>
                        @endcan  
                        @can('job_description.view') 
                        <li>
                            <a class="{{ isChildActive('jd.*') }}"
                                href="{{ route('jd.index') }}">
                                Job Desciptions
                            </a>
                        </li>
                        @endcan
                        @can('employee_leave.view')
                        <li>
                            <a class="{{ isChildActive('admin.employee.leave*') }}"
                                href="{{ route('admin.employee.leave.index') }}">
                                Employees Leaves
                            </a>
                        </li>
                        @endcan


                @canany(['interview_questions.view','interview_technology.view','interviews.view','cvs.view','daily_interviews.view'])
                 {{-- Joined Students --}}
                <li class="{{ isParent(['technologies*','interview-questions.practice','interview-questions*','interviews*','cvs*','daily-interviews*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Interviews</span>
                    </a>
                    <ul class="{{ showSubmenu(['technologies*','interview-questions.practice','interview-questions*','interviews*','cvs*','daily-interviews*']) }}">

                         @can('interview_technology.view')
                        <li>
                            <a class="{{ isChildActive('technologies*') }}"
                                href="{{ route('technologies.index') }}">
                                Technologies
                            </a>
                        </li>
                        @endcan   
                         @can('interview_questions.view')
                        <li>
                            <a class="{{ isChildActive('interview-questions*') }}"
                                href="{{ route('interview-questions.index') }}">
                                Interview Questions
                            </a>
                        </li>
                        @endcan   
                         @can('interview_questions.view')
                         <li>
                            <a class="{{ isChildActive('interview-questions.practice') }}"
                                href="{{ route('interview-questions.practice') }}">
                                Interview Q&As
                            </a>
                        </li>
                        @endcan   
                         @can('interviews.view')
                        <li>
                            <a class="{{ isChildActive('interviews*') }}"
                                href="{{ route('interviews.index') }}">
                                Candidates
                            </a>
                        </li>
                        @endcan   
                         @can('cvs.view')
                        <li>
                            <a class="{{ isChildActive('cvs*') }}"
                               href="{{ route('cvs.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                                CVs or Resumes
                            </a>
                        </li>
                        @endcan   
                         @can('daily_interviews.view')
                        <li>
                            <a class="{{ isChildActive('daily-interviews*') }}"
                               href="{{ route('daily-interviews.index') }}">
                                <!-- <i class="fas fa-bed me-2"></i> -->
                                Daily Interview Schedules
                            </a>
                        </li>
                        @endcan   
                         
                    </ul>
                </li>
                @endcanany
                         
                    </ul>
                </li>
                 @endcanany







            @canany(['upcoming_events.view','college_events.view','student_events.view','employee_events.view','brochures.view','company_profile.view','company_ppt.view','scanners.view'])
                 
                <li class="{{ isParent(['student.events*','college.events*','upcoming-events*','employee.events.*','company_ppt*','brochures*','company_profile*','scanners*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fas fa-chalkboard-teacher"></i>
                        <span class="nav-text">Company Uses</span>
                    </a>
                    <ul class="{{ showSubmenu(['student.events*','college.events*','upcoming-events*','employee.events.*','company_ppt*','brochures*','company_profile*','scanners*']) }}">
                        
                         @can('upcoming_events.view')
                        <li>
                            <a class="{{ isChildActive('upcoming-events.*') }}"
                               href="{{ route('upcoming-events.index') }}">
                              Upcoming Events
                            </a>
                        </li>
                        @endcan   
                         @can('college_events.view')
                     {{-- College Events --}}
                        <li>
                            <a class="{{ isChildActive('college.events.*') }}"
                               href="{{ route('college.events.index') }}">
                                 College Memory Events
                            </a>
                        </li>
                        @endcan   
                         @can('student_events.view')

                        {{-- Student Events --}}
                        <li>
                            <a class="{{ isChildActive('student.events.*') }}"
                               href="{{ route('student.events.index') }}">
                               Student Memory  Events
                            </a>
                        </li>
                        @endcan   
                         @can('employee_events.view')

                        {{-- Employee Events --}}
                        <li>
                            <a class="{{ isChildActive('employee.events.*') }}"
                               href="{{ route('employee.events.index') }}">
                              Employee  Memory  Events
                            </a>
                        </li>

                        @endcan   
                         @can('brochures.view')
                         {{-- Brochures --}}

                    <li class="{{ isParent(['brochures.*']) }}">
                        <a href="{{ route('brochures.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Manage Brochures</span>
                        </a>
                    </li>
                    @endcan   
                             @can('company_profile.view')
                     {{-- Brochures --}}
                    <li class="{{ isParent(['company_profile*']) }}">
                        <a href="{{ route('company_profile.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Company Profile Manage</span>
                        </a>
                    </li>
                    @endcan   
                             @can('company_ppt.view')
                    <li class="{{ isParent(['company_ppt.*']) }}">
                        <a href="{{ route('company_ppt.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Company PPT Manage</span>
                        </a>
                    </li>
                    @endcan   
                             @can('scanners.view')
                    <li class="{{ isParent(['scanners*']) }}">
                        <a href="{{ route('scanners.index') }}">
                            <!-- <i class="fa-regular fa-file-lines"></i> -->
                            <span class="nav-text">Social Share Scanners</span>
                        </a>
                    </li>

                    @endcan  
                         
                    </ul>
                </li>

              @endcanany



        
               
               
                 

               
            




                 {{-- Ads Management --}}
                 @canany(['pages.view','internship_registrations.view','services_registrations.view','ads_analytics.view','testimonial.view','single_product_registrations.view','products_registrations.view'])
                <li class="{{ isParent(['internship-registrations*','pages*','services-registrations*','products-registrations*','single-product-registrations*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Ads Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['internship-registrations*','pages*','services-registrations*','products-registrations*','single-product-registrations*']) }}">
                        @can('pages.view')
                        <li>
                            <a class="{{ isChildActive('pages.*') }}"
                                href="{{ route('pages.index') }}">
                                Ads Pages
                            </a>
                        </li>
                        @endcan 

                        @can('internship_registrations.view')
                        <li>
                            <a class="{{ isChildActive('internship-registrations*') }}"
                                href="{{ route('internship-registrations.index') }}">
                                Internship Entries
                            </a>
                        </li>
                        @endcan 
                        
                        @can('services_registrations.view')
                        <li>
                            <a class="{{ isChildActive('services-registrations*') }}"
                                href="{{ route('services-registrations.index') }}">
                                Services Entries
                            </a>
                        </li>
                        @endcan
                        @can('products_registrations.view')
                        <li>
                            <a class="{{ isChildActive('products-registrations*') }}"
                                href="{{ route('products-registrations.index') }}">
                                Products Entries
                            </a>
                        </li>
                        @endcan
                        @can('single_product_registrations.view')
                        <li>
                            <a class="{{ isChildActive('single-product-registrations*') }}"
                                href="{{ route('single-product-registrations.index') }}">
                                Single Products Entries
                            </a>
                        </li>
                        @endcan
                        @can('testimonial.view')
                        <li class="{{ request()->routeIs('testimonials.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('testimonials.index') }}">
                                Testimonials
                            </a>
                        </li> 
                        @endcan
                         @can('ads_analytics.view')
                        <li class="{{ request()->routeIs('admin.ads.analytics') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.ads.analytics') }}">
                                Ads Analytics
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- Gmail Form Entries --}}
                    @canany(['gmail_form_entries.view','gmail_queries.view'])    
                  <li class="{{ isParent(['admin.form-entries.*','admin.gmail*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Gmail</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.form-entries.*','admin.gmail*']) }}">
                        @can('gmail_form_entries.view')
                        <li class="{{ request()->routeIs('admin.form-entries.*') ? 'mm-active' : '' }}">
                          <a href="{{ route('admin.form-entries.index') }}">
                              <i class="fas fa-file-alt"></i>
                              <span class="nav-text">Gmail Form Entries</span>
                          </a>
                        </li>
                        @endcan
                        @can('gmail_queries.view')
                        <li class="{{ isParent(['admin.gmail*']) }}">

                            <a href="{{ route('admin.gmail.index') }}">

                                <i class="fas fa-envelope"></i>

                                <span class="nav-text">Gmail (Queries & HR)</span>

                            </a>

                        </li>
                        @endcan
                    </ul>
                </li>
                 @endcanany
                 @can('gmail_form_entries.view')
                        <!-- <li class="{{ request()->routeIs('admin.form-entries.*') ? 'mm-active' : '' }}">
                          <a href="{{ route('admin.form-entries.index') }}">
                              <i class="fas fa-file-alt"></i>
                              <span class="nav-text">Gmail Form Entries</span>
                          </a>
                        </li> -->
                        @endcan

                     {{-- Security --}}

@canany(['blocked_ip.view','allowed_ip.view','activity.view'])
                <li class="{{ isParent(['admin.blocked-ips.*','admin.system-activity*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-pen-to-square"></i>
                        <span class="nav-text">Security</span>
                    </a>
                    <ul class="{{ showSubmenu(['admin.blocked-ips.*','admin.system-activity*']) }}">
                        @can('blocked_ip.view')
                        <li class="{{ request()->routeIs('admin.blocked-ips.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.blocked-ips.index') }}">
                                <i class="fas fa-ban"></i>
                                <span class="nav-text">Blocked IPs</span>
                            </a>
                        </li>
                        @endcan
                        @can('allowed_ip.view')
                        <li class="{{ request()->routeIs('admin.allowed-ips.*') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.allowed-ips.index') }}">
                                <i class="fas fa-check-circle"></i>
                                <span class="nav-text">Allowed IPs (Script Access)</span>
                            </a>
                        </li>
                        @endcan

                        {{-- System Activity (logins, page views, IP) --}}
                        @can('activity.view')
                        <li class="{{ request()->routeIs('admin.system-activity') ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.system-activity') }}">
                                <i class="fas fa-history"></i>
                                <span class="nav-text">Activity</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @can('blocked_numbers.view')
                <li class="{{ isParent(['blocked-numbers.*']) }}">
                    <a href="{{ route('admin.blocked-numbers.index') }}">
                        <i class="fas fa-certificate"></i>
                        <span class="nav-text">Blocked Numbers</span>
                    </a>
                     
                </li>
            @endcan



                        

                {{-- Users --}}    
                @can('users.view')
                 <li class="{{ isParent(['users*']) }}">
                    <a href="{{ route('users.index') }}">
                         <i class="fas fa-users"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                </li>
                @endcan


@canany(['office_expenses.view','pantry_expenses.view','event_expenses.view','travel_expenses.view','office_assets.view','recharges.view','visiting_cards.view','office_paper_expenses.view','tea_pantry_expenses.view'])
               {{-- Finance --}}
                <li class="{{ isParent(['office-expenses*','pantry-expenses*','tea-pantry-expenses*','office-paper-expenses','event-expenses*','travel-expenses*','office-assets*','recharges*','visiting-cards*']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                        <i class="fas fa-coins"></i>
                        <span class="nav-text">Finance Management</span>
                    </a>
                    <ul class="{{ showSubmenu(['office-expenses*','pantry-expenses*','tea-pantry-expenses*','office-paper-expenses','event-expenses*','travel-expenses*','office-assets*','recharges*','visiting-cards*']) }}">

                         @can('office_expenses.view')
                        <li>
                            <a class="{{ isChildActive('office-expenses*') }}"
                               href="{{ route('office-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Electricty Bill
                            </a>
                        </li>
                        @endcan   
                         @can('pantry_expenses.view')
                         <li>
                            <a class="{{ isChildActive('pantry-expenses*') }}"
                               href="{{ route('pantry-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Pantry Expenses
                            </a>
                        </li>
                        @endcan   
                         @can('office_paper_expenses.view')
                         <li>
                            <a class="{{ isChildActive('office-paper-expenses*') }}"
                               href="{{ route('office-paper-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Paper Expenses
                            </a>
                        </li>
                        @endcan   
                         @can('tea_pantry_expenses.view')
                         <li>
                            <a class="{{ isChildActive('tea-pantry-expenses*') }}"
                               href="{{ route('tea-pantry-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Tea Pantry Expenses
                            </a>
                        </li>
                        @endcan  
                        @can('office_cleaning_expenses.view')
                        <li>
                            <a class="{{ isChildActive('office-cleaning-expenses*') }}"
                               href="{{ route('office-cleaning-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Cleaning Expenses
                            </a>
                        </li>
                        @endcan  
                        @can('office_accessories_expenses.view')
                        <li>
                            <a class="{{ isChildActive('office-accessories-expenses*') }}"
                               href="{{ route('office-accessories-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Accessories Expenses
                            </a>
                        </li> 
                        @endcan  
                         @can('event_expenses.view')
                         <li>
                            <a class="{{ isChildActive('event-expenses*') }}"
                               href="{{ route('event-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Events Expenses
                            </a>
                        </li>
                        @endcan   
                         @can('travel_expenses.view')

                         <li>
                            <a class="{{ isChildActive('travel-expenses*') }}"
                               href="{{ route('travel-expenses.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Travel Expenses
                            </a>
                        </li>
                        @endcan   
                         @can('office_assets.view')

                         <li>
                            <a class="{{ isChildActive('office-assets*') }}"
                               href="{{ route('office-assets.index') }}">
                                <!-- <i class="fas fa-file-invoice-dollar me-2"></i> -->
                                Office Asset Expenses
                            </a>
                        </li>

                        @endcan   
                         @can('recharges.view')
                        <li>
                            <a class="{{ isChildActive('recharges*') }}"
                               href="{{ route('recharges.index') }}">
                                <!-- <i class="fas fa-building me-2"></i> -->
                                Recharges
                            </a>
                        </li>
                        @endcan   
                         @can('visiting_cards.view')

                         <li class="{{ isParent(['visiting-cards*']) }}">
                            <a href="{{ route('visiting-cards.index') }}">
                                <!-- <i class="fa-regular fa-file-lines"></i> -->
                                <span class="nav-text">Visiting Cards</span>
                            </a>
                        </li>
                        @endcan 

                         
                    </ul>
                </li>

                @endcanany
                         
            @can('gmail_queries.view')
            <!--  <li class="{{ isParent(['admin.gmail*']) }}">

                    <a href="{{ route('admin.gmail.index') }}">

                        <i class="fas fa-envelope"></i>

                        <span class="nav-text">Gmail (Queries & HR)</span>

                    </a>

                </li> -->
               @endcan 
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
               


                @endhasrole
            @endif



            {{-- ========================================================= --}}
            {{-- TRAINER MENU (role = 2)                                  --}}
            {{-- ========================================================= --}}
            @if(Auth::guard('trainer')->check())

            
                {{-- Batches --}}
                <li class="nav-label first"></li>
                <li class="{{ isParent(['batches.mybatches']) }}">
                    <a  href="{{ route('batches.mybatches') }}">
                        <i class="fas fa-layer-group"></i>
                        <span class="nav-text">Batches</span>
                    </a>
                    
                </li>

                {{-- Attendence --}}
                <!-- <li class="{{ isParent(['attendance.employee','attendance.myDetail']) }}">
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
                </li> -->

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
            @if(Auth::guard('sales_staff')->check())

                
                {{-- Dashboard --}}
                
               

                {{-- Leads --}}
                <li class="nav-label first"></li>
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
           <!--      <li class="{{ isParent(['attendance.employee','attendance.myDetail']) }}">
                    <a class="has-arrow" href="javascript:void(0)">
                         <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Calling Status</span>
                    </a>
                    <ul class="{{ showSubmenu(['attendance.employee','attendance.myDetail']) }}">
                        <li>
                            <a class="{{ isChildActive('attendance.employee') }}"
                                href="{{ route('attendance.employee') }}">
                                Calling Timing
                            </a>
                        </li>
                        <li>
                            <a class="{{ isChildActive('attendance.myDetail') }}"
                                href="{{ route('attendance.myDetail') }}">
                                Calling History
                            </a>
                        </li>
                    </ul>
                </li> -->

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


            @if(Auth::guard('employee')->check())

            
                

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
