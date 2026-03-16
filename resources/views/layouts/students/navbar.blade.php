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

</style>

<div class="quixnav">
    <!-- Mobile Close Button -->
<div class="sidebar-close d-lg-none">
    <span>&times;</span>
</div>

    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">

            <li class="nav-label first"></li>
               

                {{-- Leads --}}
                <li class="{{ isParent(['students.dashboard']) }}">
                    <a href="{{ route('students.dashboard') }}">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                {{-- Attendence --}}
                <li class="{{ isParent(['students.projects']) }}">
                    <a href="{{ route('students.projects') }}">
                         <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Assigned Projects</span>
                    </a>
                </li>

                {{-- Attendence --}}
                <li class="{{ isParent(['students.attendance']) }}">
                    <a href="{{ route('students.attendance') }}">
                         <i class="fa-regular fa-file-lines"></i>
                        <span class="nav-text">Attendance</span>
                    </a>
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
 

        </ul>
    </div>
</div>
