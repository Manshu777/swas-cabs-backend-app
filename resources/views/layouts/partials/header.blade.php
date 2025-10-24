<header class="header-color shadow-sm transition-all duration-300">
    <div class="flex h-16 items-center justify-between px-6">
        {{-- Mobile menu toggle --}}
        <button id="sidebar-toggle"
            class="lg:hidden p-2 rounded-md  ">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Title --}}
        <div class="flex-1 lg:ml-0 ml-4">
            <h1 class="text-xl font-semibold ">@yield('title', 'Dashboard')</h1>
        </div>

        {{-- User menu --}}
        <div class="flex items-center space-x-4">
            {{-- Clear Cache --}}
            <button id="clear-cache-btn"
                class="p-2 rounded-md text-gray-600  transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 6h18M3 12h18m-9 6h9" />
                </svg>
                <span class="sr-only">Clear Cache</span>
            </button>

            {{-- Theme Toggle --}}
           <button id="theme-toggle"
    class="p-2 rounded-md text-gray-600   transition">
    <svg id="theme-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 3v1m0 16v1m8.66-10H21m-18 0h1m15.364 6.364l-.707.707M5.343 5.343l.707.707m12.02 12.02l.707.707M5.343 18.657l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
    </svg>
</button>

            {{-- Notifications --}}
            <div class="relative">
                <button id="notification-btn"
                    class="p-2 rounded-md text-gray-600   relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span
                        class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border border-white dark:border-gray-900"></span>
                </button>

                {{-- Notification dropdown --}}
                <div id="notification-dropdown"
                    class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-2 text-sm ">No new notifications</div>
                </div>
            </div>

            {{-- User Dropdown --}}
            <div class="relative">
                <button id="user-menu"
                    class="flex items-center space-x-2 p-2 rounded-md   transition">
                    <img src="{{ auth()->user()->profile_photo_url ?? asset('images/default-avatar.png') }}"
                        class="w-8 h-8 rounded-full object-cover" alt="User">
                    <span class="text-sm  hidden sm:block">
                        {{ auth()->user()->name ?? 'Manshu' }}
                    </span>
                </button>

                <div id="user-dropdown"
                    class="hidden absolute right-0 mt-2 w-48 border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                    <a 
                        class="block px-4 py-2 text-sm ">
                        Edit Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm text-red-600">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
