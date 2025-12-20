<style>
    /* Bell Icon Styling */
    .bell-icon {
        font-size: 22px;
        color: #555;
        transition: 0.2s ease;
    }

    .nav-link:hover .bell-icon {
        color: #000;
        transform: scale(1.1);
    }

    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        background: #dc3545;   /* Bootstrap red */
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 10px;
        line-height: 1.2;
        font-weight: bold;
        border: 1px solid #fff;
        display: inline-block;
        min-width: 18px;
        text-align: center;
    }

    /* Glow Effect when unread exists */
.bell-icon.unread-glow {
    color: #dc3545 !important;
    text-shadow: 0 0 6px rgba(220, 53, 69, 0.8);
}

/* Shake animation */
@keyframes shake {
    0% { transform: rotate(0deg); }
    20% { transform: rotate(-10deg); }
    40% { transform: rotate(10deg); }
    60% { transform: rotate(-6deg); }
    80% { transform: rotate(6deg); }
    100% { transform: rotate(0deg); }
}

.bell-icon.shake {
    animation: shake 0.6s ease-in-out;
}

/* Badge improvements */
.notification-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 10px;
    line-height: 1.2;
    font-weight: bold;
    border: 1px solid #fff;
    min-width: 18px;
    text-align: center;
}
/* Ensure notification text wraps inside the dropdown */
.notification-message {
    white-space: normal !important;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 260px; /* keep inside the dropdown */
    display: block;
}


</style>
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">

                {{-- Left side (search, etc.) --}}
                <div class="header-left">
                    <div class="search_bar dropdown" style="display: none;">
                        <span class="search_icon p-3 c-pointer" data-toggle="dropdown">
                            <i class="mdi mdi-magnify"></i>
                        </span>
                        <div class="dropdown-menu p-0 m-0" style="display: none;">
                            <form>
                                <input class="form-control" type="search" placeholder="Search" aria-label="Search">
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Right side (session switcher + user profile + logout) --}}
                <ul class="navbar-nav header-right">

                    {{-- 🔥 SESSION SWITCHER (ROLE = 1 ONLY) --}}
                    @if(Auth::check() && Auth::user()->role == 1)
                        @if(isset($sessions) && count($sessions) > 0)
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                   role="button" data-bs-toggle="dropdown" aria-expanded="false">

                                    <i class="mdi mdi-calendar me-1"></i>

                                   @if($currentSession)
                                        <span>
                                            {{ ucwords($currentSession->session_name) }}
                                            ({{ \Carbon\Carbon::parse($currentSession->start_date)->format('M Y') }})
                                        </span>
                                    @else
                                        <span>Select Session</span>
                                    @endif
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                    <li class="dropdown-item text-muted small">Change Session</li>

                                    <li>
                                       <form action="{{ route('admin.changeSession') }}" method="POST">
                                            @csrf
                                            <select name="session_id" class="form-select mt-1" onchange="this.form.submit()">
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->id }}"
                                                        {{ session('admin_session_id') == $session->id ? 'selected' : '' }}>
                                                        {{ ucwords($session->session_name) }}
                                                        ({{ \Carbon\Carbon::parse($session->start_date)->format('M Y') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endif

                    {{-- USER MENU --}}

           @php
    $unreadCount = Auth::check() ? Auth::user()->unreadNotifications()->count() : 0;
    $notifications = Auth::check() ? Auth::user()->unreadNotifications()->take(5)->get() : collect();

    // 🔥 ICON + COLOR MAP
    $iconMap = [
        'lead.assigned'            => ['icon' => 'mdi-account-plus', 'color' => 'text-primary'],
        'sales.followups.today'    => ['icon' => 'mdi-calendar-today', 'color' => 'text-warning'],
        'sales.followups.missed'   => ['icon' => 'mdi-calendar-remove', 'color' => 'text-danger'],
        'batch.assigned'           => ['icon' => 'mdi-school', 'color' => 'text-success'],
        'fee.pending.summary'      => ['icon' => 'mdi-currency-inr', 'color' => 'text-danger'],
    ];
@endphp

{{-- 🔔 NOTIFICATION BELL --}}
<li class="nav-item dropdown mx-2">

    <a class="nav-link dropdown-toggle position-relative" href="#" role="button"
       data-bs-toggle="dropdown" aria-expanded="false">

        <i class="mdi {{ $unreadCount > 0 ? 'mdi-bell shaking-bell unread-glow' : 'mdi-bell-outline' }} bell-icon"></i>

        @if($unreadCount > 0)
           <span class="notification-badge">
                {{ $unreadCount }}
            </span>
        @endif
    </a>

    <ul class="dropdown-menu dropdown-menu-end p-0" style="width: 330px;">

        {{-- HEADER --}}
        <li class="dropdown-header p-2 fw-bold">
            Notifications ({{ $unreadCount }})
        </li>
        <li><hr class="dropdown-divider m-0"></li>

        {{-- NOTIFICATION ITEMS --}}
        @forelse($notifications as $notification)

            @php
                $key = $notification->data['template_key'] ?? '';
                $icon  = $iconMap[$key]['icon']  ?? 'mdi-bell-outline';
                $color = $iconMap[$key]['color'] ?? 'text-muted';
            @endphp

            <li>
                <div class="dropdown-item d-flex {{ is_null($notification->read_at) ? 'fw-bold' : '' }}">

                    {{-- ICON --}}
                    <i class="mdi {{ $icon }} {{ $color }} me-3"
                       style="font-size: 22px;"></i>

                    {{-- TEXT --}}
                    <div style="width: 100%;">
                        <strong class="{{ $color }}">{{ $notification->data['title'] }}</strong>

                        <div class="small text-muted notification-message">
                            {{ $notification->data['message'] }}
                        </div>

                        <a href="{{ route('notifications.view', $notification->id) }}"
                           class="btn btn-sm btn-primary mt-1">
                            View
                        </a>
                    </div>

                </div>
            </li>

            <li><hr class="dropdown-divider m-0"></li>

        @empty
            <li class="dropdown-item text-muted small">No new notifications</li>
        @endforelse

        {{-- FOOTER --}}
        <li class="text-center">
            <a class="dropdown-item p-2 fw-bold" href="{{ route('notifications.index') }}">
                View All
            </a>
        </li>

    </ul>

</li>


                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">

                                {{-- Profile Picture --}}
                                @if(Auth::user()->profile_picture && file_exists(public_path(Auth::user()->profile_picture)))
                                    <img src="{{ asset(Auth::user()->profile_picture) }}"
                                         alt="Profile Picture"
                                         class="rounded-circle me-2"
                                         width="32" height="32"
                                         style="object-fit: cover;">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}"
                                         alt="Default Avatar"
                                         class="rounded-circle me-2"
                                         width="32" height="32">
                                @endif

                                {{-- Username --}}
                                <span class="ms-2">{{ Auth::check() ? ucwords(Auth::user()->name) : '' }}</span>
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="mdi mdi-account"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="mdi mdi-logout"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth

                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="icon-key"></i> Login
                            </a>
                        </li>
                    @endguest

                </ul>

            </div>
        </nav>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let bell = document.querySelector('.shaking-bell');
    if (bell) {
        bell.classList.add('shake');
        setTimeout(() => bell.classList.remove('shake'), 800);
    }
});
</script>

