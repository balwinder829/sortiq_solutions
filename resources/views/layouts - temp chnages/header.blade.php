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

    .bell-icon.unread-glow {
        color: #dc3545 !important;
        text-shadow: 0 0 6px rgba(220, 53, 69, 0.8);
    }

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

    .notification-message {
        white-space: normal !important;
        max-width: 260px;
        display: block;
    }

    /* ===== MOBILE SIDEBAR PATCH ===== */
    @media (max-width: 991px) {
        .quixnav {
            position: fixed;
            top: 0;
            left: -260px;
            width: 260px;
            height: 100%;
            background: #fff;
            z-index: 99999;
            transition: left 0.3s ease;
            overflow-y: auto;
        }

        .quixnav.mobile-open {
            left: 0;
        }

        body.mobile-menu-open {
            overflow: hidden;
        }

        .mobile-menu-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 99998;
            display: none;
        }

        .mobile-menu-overlay.show {
            display: block;
        }

         #mobileMenuToggle {
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    }

   
}
</style>

<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand-lg">
              {{-- ☰ MOBILE MENU TOGGLE --}}
                <button class="btn btn-light d-lg-none me-2"
                        id="mobileMenuToggle"
                        aria-label="Toggle menu">
                    <i class="mdi mdi-menu"></i>
                </button>

            <div class="navbar-collapse justify-content-between">

              
                {{-- Left side --}}
                <div class="header-left">
                    <div class="search_bar dropdown" style="display:none;">
                        <span class="search_icon p-3 c-pointer">
                            <i class="mdi mdi-magnify"></i>
                        </span>
                    </div>
                </div>

                {{-- Right side --}}
                <ul class="navbar-nav header-right">

                    {{-- SESSION SWITCHER --}}
                    @if(Auth::check() && Auth::user()->role == 1 && isset($sessions) && count($sessions))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center"
                               href="#" data-bs-toggle="dropdown">

                                <i class="mdi mdi-calendar me-1"></i>

                                @if($currentSession)
                                    {{ ucwords($currentSession->session_name) }}
                                @else
                                    Select Session
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end p-2">
                                <li>
                                    <form action="{{ route('admin.changeSession') }}" method="POST">
                                        @csrf
                                        <select name="session_id" class="form-select"
                                                onchange="this.form.submit()">
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}"
                                                    {{ session('admin_session_id') == $session->id ? 'selected' : '' }}>
                                                    {{ ucwords($session->session_name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @php
                        $unreadCount = Auth::check()
                            ? Auth::user()->unreadNotifications()->count()
                            : 0;

                        $notifications = Auth::check()
                            ? Auth::user()->unreadNotifications()->take(5)->get()
                            : collect();
                    @endphp


                    {{-- NOTIFICATION BELL --}}
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link dropdown-toggle position-relative"
                           href="#" data-bs-toggle="dropdown">

                            <i class="mdi {{ $unreadCount > 0 ? 'mdi-bell shaking-bell unread-glow' : 'mdi-bell-outline' }} bell-icon"></i>

                            @if($unreadCount > 0)
                                <span class="notification-badge">{{ $unreadCount }}</span>
                            @endif
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end p-0" style="width:330px;">
                            <li class="dropdown-header p-2 fw-bold">
                                Notifications ({{ $unreadCount }})
                            </li>
                            <li><hr class="dropdown-divider m-0"></li>

                            @forelse($notifications as $notification)
                                <li class="dropdown-item">
                                    <strong>{{ $notification->data['title'] }}</strong>
                                    <div class="small text-muted notification-message">
                                        {{ $notification->data['message'] }}
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider m-0"></li>
                            @empty
                                <li class="dropdown-item text-muted">No new notifications</li>
                            @endforelse
                        </ul>
                    </li>

                    {{-- USER MENU --}}
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center"
                               href="#" data-bs-toggle="dropdown">

                                <img src="{{ Auth::user()->profile_picture
                                    ? asset(Auth::user()->profile_picture)
                                    : asset('images/default-avatar.png') }}"
                                     class="rounded-circle me-2"
                                     width="32" height="32">

                                {{ ucwords(Auth::user()->name) }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.index') }}">
                                        Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endauth

                </ul>
            </div>
        </nav>
    </div>
</div>

<div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const bell = document.querySelector('.shaking-bell');
    if (bell) {
        bell.classList.add('shake');
        setTimeout(() => bell.classList.remove('shake'), 800);
    }

    const toggleBtn = document.getElementById('mobileMenuToggle');
    // const sidebar  = document.querySelector('.quixnav');
    // const overlay  = document.getElementById('mobileMenuOverlay');

    // if (!toggleBtn || !sidebar || !overlay) return;

    // toggleBtn.addEventListener('click', function () {
    //     sidebar.classList.toggle('mobile-open');
    //     overlay.classList.toggle('show');
    //     document.body.classList.toggle('mobile-menu-open');
    // });

    // overlay.addEventListener('click', closeMenu);

    // function closeMenu() {
    //     sidebar.classList.remove('mobile-open');
    //     overlay.classList.remove('show');
    //     document.body.classList.remove('mobile-menu-open');
    // }

    // sidebar.querySelectorAll('a').forEach(link => {
    //     link.addEventListener('click', () => {
    //         if (window.innerWidth < 992) closeMenu();
    //     });
    // });
});
</script>
 

