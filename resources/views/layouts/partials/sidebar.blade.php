{{-- resources/views/layouts/partials/sidebar.blade.php --}}
@php
    $navigation = [
        ['name' => 'Dashboard', 'icon' => 'layout-dashboard', 'active' => request()->routeIs('admin.dashboard')],
        ['name' => 'Bookings', 'icon' => 'calendar', 'active' => request()->routeIs('admin.bookings.*')],
        ['name' => 'Units (Cars)', 'icon' => 'car', 'active' => request()->routeIs('admin.units.*')],
        ['name' => 'Users', 'icon' => 'users', 'active' => request()->routeIs('admin.users.*')],
        ['name' => 'Drivers', 'icon' => 'user-check', 'active' => request()->routeIs('admin.drivers.*')],
        ['name' => 'Rides', 'icon' => 'map-pin', 'active' => request()->routeIs('admin.rides.*')],
        ['name' => 'Financial', 'icon' => 'rupee', 'active' => request()->routeIs('admin.financial.*')],
        ['name' => 'Tour Packages', 'icon' => 'package', 'active' => request()->routeIs('admin.packages.*')],
        ['name' => 'SOS Monitoring', 'icon' => 'alert-triangle', 'active' => request()->routeIs('admin.sos.*')],
        ['name' => 'Settings', 'icon' => 'settings', 'active' => request()->routeIs('admin.settings.*')],
    ];

    // Icon SVGs (Lucide-inspired paths)
    $icons = [
        'layout-dashboard' => '<path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>',
        'calendar' => '<rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>',
        'car' => '<rect width="18" height="11" x="3" y="6" rx="2"/><path d="M21 10h-3V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1z"/><circle cx="7" cy="14" r="1"/><circle cx="17" cy="14" r="1"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-8 0v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'user-check' => '<path d="M16 21v-2a4 4 0 0 0-8 0v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/>',
        'map-pin' => '<path d="M20 10c0-6-8-12-8-12s-8 6-8 12a8 8 0 0 0 16 0z"/><line x1="9" x2="9.01" y1="10" y2="13"/><line x1="15" x2="15.01" y1="10" y2="13"/>',
        'rupee' => '<path d="M6.5 2h11a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z"/><path d="M5 12a2 2 0 0 1 2-2 2 2 0 0 1 2 2v7a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-7z"/><path d="M12 2v20M16 16h4"/>',
        'package' => '<path d="M22 7H2a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><line x1="9" x2="15" y1="9" y2="15"/><line x1="15" x2="9" y1="9" y2="15"/>',
        'alert-triangle' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
    ];

    $collapsed = session('sidebar_collapsed', false) || (request()->cookie('sidebar_collapsed') === 'true');
    $isMobile = request()->header('User-Agent') && preg_match('/Mobile|Android|iPhone/', request()->header('User-Agent'));
@endphp

{{-- Mobile Backdrop --}}
@if($isMobile)
<div class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden hidden" id="mobile-backdrop" onclick="closeSidebar()"></div>
@endif

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex flex-col {{ $collapsed ? 'w-16' : 'w-72' }} bg-white border-r border-gray-200 transition-all duration-300 ease-in-out {{ $isMobile ? '-translate-x-full lg:translate-x-0 hidden lg:block' : 'translate-x-0 lg:block' }}">
    {{-- Header --}}
    <div class="flex h-16 items-center justify-between px-4 border-b border-gray-200">
        <div class="flex items-center space-x-2 {{ $collapsed ? 'hidden' : '' }}">
            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <h1 class="text-xl font-bold text-gray-900">TaxiAdmin</h1>
        </div>
        
        <div class="flex items-center space-x-2">
            <button onclick="toggleCollapsed()" class="hidden lg:block p-1.5 rounded-md hover:bg-gray-100">
                <svg class="h-4 w-4 transition-transform {{ $collapsed ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            
            <button onclick="closeSidebar()" class="lg:hidden p-1.5 rounded-md hover:bg-gray-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
        @foreach ($navigation as $item)
            <a
               class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors {{ $item['active'] ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
               title="{{ $collapsed ? $item['name'] : '' }}">
                <svg class="flex-shrink-0 {{ $collapsed ? 'mx-auto h-6 w-6' : 'mr-3 h-5 w-5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icons[$item['icon']] !!}
                </svg>
                <span class="{{ $collapsed ? 'hidden' : '' }}">{{ $item['name'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center {{ $collapsed ? 'justify-center' : 'space-x-3' }}">
            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                <span class="text-sm font-medium text-white">A</span>
            </div>
            <div class="{{ $collapsed ? 'hidden' : 'flex-1 min-w-0' }}">
                <p class="text-sm font-medium text-gray-900 truncate">Admin User</p>
                <p class="text-xs text-gray-500 truncate">admin@taxi.com</p>
            </div>
        </div>
    </div>
</aside>