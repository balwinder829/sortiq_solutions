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
.notification-scroll {
    max-height: 400px;   /* adjust height as you want */
    overflow-y: auto;
}

/* Optional: smooth scrollbar */
.notification-scroll::-webkit-scrollbar {
    width: 6px;
}

.notification-scroll::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
}
#notificationTabs .nav-link {
    font-size: 12px;
    padding: 4px 8px;
}

#notificationTabs {
    border-bottom: 1px solid #eee;
}
</style>
<div class="header">
    <div class="header-content">
 <!--        <div class="nav-control">
    <div class="hamburger" id="sidebarToggle">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>
</div> -->
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                @include('common.logo') 
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
                    @if(Auth::check() && (Auth::user()->role == 1|| Auth::user()->role == 4 ))
                       @if(isset($sessions) && count($sessions) > 0)
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
       role="button" data-bs-toggle="dropdown">
        <i class="mdi mdi-calendar me-1"></i>
        {{ $currentSession
            ? ucwords($currentSession->session_name).' ('.\Carbon\Carbon::parse($currentSession->start_date)->format('M Y').')'
            : 'Select Session' }}
    </a>

    <ul class="dropdown-menu dropdown-menu-end">
        @foreach($sessions as $session)
            <li>
                <form action="{{ route('admin.changeSession') }}" method="POST">
                    @csrf
                    <input type="hidden" name="session_id" value="{{ $session->id }}">
                    <button type="submit"
                            class="dropdown-item {{ session('admin_session_id') == $session->id ? 'active' : '' }}">
                        {{ ucwords($session->session_name) }}
                        ({{ \Carbon\Carbon::parse($session->start_date)->format('M Y') }})
                        - ({{ $session->students_count }})
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
</li>
@endif

                    @endif

                    {{-- USER MENU --}}

         @php
// 🔥 ICON + COLOR MAP (unchanged)
$iconMap = [
    'lead.assigned'              => ['icon' => 'mdi-account-plus', 'color' => 'text-primary'],
    'sales.followups.today'      => ['icon' => 'mdi-calendar-today', 'color' => 'text-warning'],
    'sales.followups.missed'     => ['icon' => 'mdi-calendar-remove', 'color' => 'text-danger'],
    'batch.assigned'             => ['icon' => 'mdi-school', 'color' => 'text-success'],
    'fee.pending.summary'        => ['icon' => 'mdi-currency-inr', 'color' => 'text-danger'],

    // 🔥 YOUR NEW TYPES
    'student.registered.sales'   => ['icon' => 'mdi-account-check', 'color' => 'text-success'],
    'upcoming.event'             => ['icon' => 'mdi-calendar', 'color' => 'text-primary'],
    'admin.interviews.today' => ['icon'  => 'mdi-calendar-check', 'color' => 'text-info'],
];

// These variables now come from View Composer
 
 
$categoryMap = [
    'student.registered.sales'   => 'Registered Students',
    'student.registered.summary' => 'Registered Students',
    'batch.assigned'             => 'Batch Assigned',
    'sales.followups.today'      => 'Sales Followups',
    'sales.followups.missed'     => 'Sales Followups',
    'fee.pending.summary'        => 'Fees',
    'upcoming.event'             => 'Events',
    'bin.ready.summary'          => 'BIN Ready',
    'sales.leads.low.percent'       => 'Low Leads',
    'sales.leads.low.percent.admin' => 'Low Leads',
    'admin.interviews.today' => 'Interviews',
];
@endphp

{{-- 🔔 NOTIFICATION BELL --}}
<li class="nav-item dropdown mx-2">

    <a class="nav-link dropdown-toggle position-relative" href="#"
   data-bs-toggle="dropdown"
   data-bs-auto-close="outside"
   aria-expanded="false">

        <i class="mdi {{ $unreadCount > 0 ? 'mdi-bell shaking-bell unread-glow' : 'mdi-bell-outline' }} bell-icon"></i>

        @if($unreadCount > 0)
            <span class="notification-badge">{{ $unreadCount }}</span>
        @endif
    </a>

    <ul class="dropdown-menu dropdown-menu-end p-0 notification-scroll" style="width: 330px;">

        {{-- HEADER --}}
        <li class="dropdown-header p-2 fw-bold d-flex justify-content-between">
            <span>Notifications ({{ $unreadCount }})</span>

            @if($unreadCount > 0)
                <a href="{{ route('notifications.clearAll') }}"
                   class="text-danger small"
                   data-swal-confirm="Clear all notifications?">
                    Clear All
                </a>
            @endif
        </li>
        <li class="px-2 pb-2">
    <!-- <select id="notificationFilter" class="form-select form-select-sm">
        <option value="all">All</option>
        <option value="Registered Students">Registered Students</option>
        <option value="Batch Assigned">Batch Assigned</option>
        <option value="Sales Followups">Sales Followups</option>
        <option value="Fees">Fees</option>
        <option value="Events">Events</option>
        <option value="BIN Ready">BIN Ready</option>
        <option value="Low Leads">Low Leads</option>
        <option value="Interviews">Interviews</option>
    </select> -->
    <ul class="nav nav-tabs px-2 pt-2" id="notificationTabs">
        <li class="nav-item">
            <button class="nav-link active" data-category="all">All</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Registered Students">Students</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Batch Assigned">Batch</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Sales Followups">Followups</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Fees">Fees</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Events">Events</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="BIN Ready">BIN Ready</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Low Leads">Low Leads</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-category="Interviews">Interviews</button>
        </li>
    </ul>
</li>

        <li><hr class="dropdown-divider m-0"></li>

        {{-- 🔥 GROUPED NOTIFICATIONS (ONE PER TEMPLATE KEY) --}}
       @forelse($notifications as $group)

    @php
        $notification = $group['notification'] ?? null;
        $count = $group['count'] ?? 0;

        // 🔒 SAFETY CHECK (THIS FIXES THE ERROR)
        if (!$notification || !isset($notification->data['template_key'])) {
            continue; // skip invalid group safely
        }

        $key   = $notification->data['template_key'];

        $icon  = $iconMap[$key]['icon']  ?? 'mdi-bell-outline';
        $color = $iconMap[$key]['color'] ?? 'text-muted';
    @endphp


            @php
$category = $categoryMap[$key] ?? 'Other';
@endphp

<li class="notification-item" data-category="{{ $category }}">
                <div class="dropdown-item d-flex justify-content-between align-items-start fw-bold">

                    <div class="d-flex">
                        <i class="mdi {{ $icon }} {{ $color }} me-3"
                           style="font-size: 22px;"></i>

                        <div>
                            <strong class="{{ $color }}">
                                {{ $notification->data['title'] }}
                            </strong>

                            <div class="small text-muted notification-message">

                            @if($key === 'bin.ready.summary')
                                {{ $count }} {{ \Illuminate\Support\Str::plural('student', $count) }}
                                are ready to be moved to BIN.

                            @elseif($key === 'fee.pending.summary')
                                {{ $count }} {{ \Illuminate\Support\Str::plural('student', $count) }}
                                have pending fees today.

                            @elseif($key === 'upcoming.event')
                                {{ $count }} upcoming {{ \Illuminate\Support\Str::plural('event', $count) }}.

                            @else
                                {{ $count }} new notifications.
                            @endif

                        </div>


                            <a href="{{ route('notifications.byType', $key) }}"
                               class="btn btn-sm btn-primary mt-1">
                                View
                            </a>
                        </div>
                    </div>

                    {{-- ❌ CLEAR ALL OF THIS TEMPLATE --}}
                    <form action="{{ route('notifications.clearByTemplate', $key) }}"
                          method="POST"
                          data-swal-confirm="Clear all notifications of this type?">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm text-danger p-0 ms-2">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </form>

                </div>
            </li>

            <li><hr class="dropdown-divider m-0"></li>

        @empty
            <li class="dropdown-item text-muted small">
                No new notifications
            </li>
        @endforelse

        {{-- FOOTER --}}
        <li class="text-center">
            <a class="dropdown-item p-2 fw-bold"
               href="{{ route('notifications.index') }}">
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
                                    @if(Auth::user()->role == 1)
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        <i class="mdi mdi-account"></i> Profile
                                    </a>
                                @else
                                    <a class="dropdown-item disabled" href="javascript:void(0)">
                                        <i class="mdi mdi-account"></i> Profile
                                    </a>
                                @endif
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

<!-- <script>
document.addEventListener('change', function(e){

    if(e.target.id !== 'notificationFilter') return;

    let val = e.target.value;

    document.querySelectorAll('.notification-item').forEach(function(item){

        if(val === 'all'){
            item.style.display = '';
            return;
        }

        item.style.display =
            item.dataset.category === val ? '' : 'none';
    });
});


</script> -->

<script>
document.addEventListener('click', function(e){

    if(!e.target.matches('#notificationTabs .nav-link')) return;

    // Remove active class
    document.querySelectorAll('#notificationTabs .nav-link')
        .forEach(tab => tab.classList.remove('active'));

    // Add active to clicked tab
    e.target.classList.add('active');

    let category = e.target.dataset.category;

    document.querySelectorAll('.notification-item').forEach(item => {

        if(category === 'all'){
            item.style.display = '';
        } else {
            item.style.display =
                item.dataset.category === category ? '' : 'none';
        }

    });
});
</script>