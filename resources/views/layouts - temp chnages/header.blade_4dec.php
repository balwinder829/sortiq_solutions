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

                {{-- Right side (user profile + logout) --}}
                <ul class="navbar-nav header-right">
                    @auth
                  <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                            <span class="ms-2">{{ Auth::user()->username ?? Auth::user()->name }}</span>
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
