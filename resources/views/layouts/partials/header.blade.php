<header class="sticky top-0 z-30 bg-white border-b border-gray-100 transition-all duration-300">
    <div class="flex h-20 items-center justify-between px-8">
        
        {{-- Left Section: Toggle & Title --}}
        <div class="flex items-center">
            {{-- Mobile menu toggle: Minimal Hamburger --}}
            <button id="sidebar-toggle"
                class="lg:hidden p-2 -ml-2 rounded-full hover:bg-gray-50 text-black transition-colors"
                aria-label="Open Sidebar">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 8h16M4 16h16" /> {{-- Modern 2-line hamburger look --}}
                </svg>
            </button>

            {{-- Title: Bold Typography --}}
            <div class="lg:ml-0 ml-4">
                <h1 class="text-xl font-bold tracking-tight text-black">
                    @yield('title', 'Dashboard')
                </h1>
            </div>
        </div>

        {{-- Right Section: Action Icons --}}
        <div class="flex items-center space-x-1 sm:space-x-3">
            
            {{-- Search Icon (Added for Premium Utility) --}}
            <button class="p-2.5 rounded-full text-gray-400 hover:text-black hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            {{-- Clear Cache --}}
            <button id="clear-cache-btn"
                title="Clear Cache"
                class="p-2.5 rounded-full text-gray-400 hover:text-black hover:bg-gray-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>

            {{-- Theme Toggle --}}
            <button id="theme-toggle"
                class="p-2.5 rounded-full text-gray-400 hover:text-black hover:bg-gray-50 transition-all">
                <svg id="theme-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-10H21m-18 0h1m15.364 6.364l-.707.707M5.343 5.343l.707.707m12.02 12.02l.707.707M5.343 18.657l.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
            </button>

            {{-- Notifications --}}
            <div class="relative">
                <button id="notification-btn"
                    class="p-2.5 rounded-full text-gray-400 hover:text-black hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-black rounded-full ring-2 ring-white"></span>
                </button>
            </div>

            {{-- Vertical Divider --}}
            <div class="h-6 w-[1px] bg-gray-200 mx-2"></div>

            {{-- User Profile Dropdown --}}
            <div class="relative">
                <button id="user-menu"
                    class="flex items-center space-x-3 p-1 rounded-full hover:bg-gray-50 transition-all group">
                    <div class="h-9 w-9 rounded-full overflow-hidden ring-2 ring-gray-100 group-hover:ring-black transition-all">
                        <img src="{{ auth()->user()->profile_photo_url ?? asset('images/default-avatar.png') }}"
                            class="w-full h-full object-cover" alt="User">
                    </div>
                    <span class="text-sm font-bold text-black hidden sm:block pr-2">
                        {{ auth()->user()->name ?? 'Manshu' }}
                    </span>
                </button>

                {{-- Dropdown: Elegant Shadow --}}
                <div id="user-dropdown"
                    class="hidden absolute right-0 mt-3 w-56 bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden py-2">
                    <div class="px-4 py-2 border-b border-gray-50 mb-1">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-widest">Account</p>
                    </div>
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                        Edit Profile
                    </a>
                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black transition-colors">
                        Security Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1 pt-1 border-t border-gray-50">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2.5 text-sm text-red-500 font-bold hover:bg-red-50 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>