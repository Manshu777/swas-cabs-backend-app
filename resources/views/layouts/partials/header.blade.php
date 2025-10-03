{{-- resources/views/layouts/partials/header.blade.php --}}
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex h-16 items-center justify-between px-6">
        {{-- Mobile menu toggle --}}
        <button id="sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        
        {{-- Title --}}
        <div class="flex-1 lg:ml-0 ml-4">
            <h1 class="text-xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
        </div>
        
        {{-- User menu --}}
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700 hidden sm:block">Welcome, {{ auth()->user()->name ?? 'Manshu' }}</span>
            <div class="relative">
                <button class="flex items-center space-x-2 p-2 rounded-md hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                    <span class="sr-only">User menu</span>
                </button>
            </div>
        </div>
    </div>
</header>