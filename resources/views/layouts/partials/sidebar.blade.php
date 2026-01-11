@php
    // Logic for your existing arrays remains the same
    $collapsed = session('sidebar_collapsed', false);
    // Added a helper for the mobile class logic
    $sidebarWidth = $collapsed ? 'w-20' : 'w-72';
@endphp

{{-- Mobile Overlay: Subtle blur for a premium feel --}}
<div 
    id="mobile-backdrop" 
    class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm lg:hidden hidden transition-opacity" 
    onclick="closeSidebar()"
    aria-hidden="true">
</div>

<aside
    id="sidebar"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-gray-100 transition-all duration-300 ease-in-out shadow-sm
           {{ $sidebarWidth }} transform -translate-x-full lg:translate-x-0"
    aria-label="Main Navigation"
>
    {{-- Header: Clean Branding --}}
    <div class="flex h-20 items-center justify-between px-6">
        <div class="flex items-center space-x-3 {{ $collapsed ? 'hidden' : 'flex' }}">
            <div class="bg-black p-1.5 rounded-lg">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-lg font-bold tracking-tight text-black">TaxiAdmin</h1>
        </div>

        {{-- Desktop Toggle --}}
        <button 
            onclick="toggleCollapsed()" 
            class="hidden lg:flex items-center justify-center p-2 rounded-full hover:bg-gray-50 text-gray-400 hover:text-black transition-colors"
            aria-label="Toggle Sidebar"
        >
            <svg class="h-5 w-5 transition-transform {{ $collapsed ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>

        {{-- Mobile Close --}}
        <button onclick="closeSidebar()" class="lg:hidden p-2 text-gray-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Navigation: Minimalist List --}}
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto scrollbar-hide">
        @foreach ($navigation as $item)
            <a href="{{ $item['href'] }}"
               class="group relative flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200
                      {{ $item['active'] 
                         ? 'bg-black text-white shadow-md shadow-gray-200' 
                         : 'text-gray-500 hover:bg-gray-50 hover:text-black' }}"
               title="{{ $collapsed ? $item['name'] : '' }}">
                
                <svg class="flex-shrink-0 transition-colors {{ $collapsed ? 'mx-auto h-6 w-6' : 'mr-3 h-5 w-5' }}" 
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    {!! $icons[$item['icon']] !!}
                </svg>
                
                <span class="truncate transition-opacity duration-200 {{ $collapsed ? 'hidden' : 'opacity-100' }}">
                    {{ $item['name'] }}
                </span>

                {{-- Active Indicator for Collapsed State --}}
                @if($item['active'] && $collapsed)
                    <div class="absolute left-0 w-1 h-6 bg-black rounded-r-full"></div>
                @endif
            </a>
        @endforeach
    </nav>

    {{-- Footer: Profile Section --}}
    <div class="p-4 bg-gray-50/50 border-t border-gray-100">
        <div class="flex items-center p-2 rounded-xl {{ $collapsed ? 'justify-center' : 'space-x-3' }}">
            {{-- Avatar --}}
            <div class="h-9 w-9 rounded-full bg-black flex items-center justify-center ring-2 ring-white shadow-sm">
                <span class="text-xs font-bold text-white">{{ strtoupper(auth()->user()->name[0]) }}</span>
            </div>

            <div class="{{ $collapsed ? 'hidden' : 'flex-1 min-w-0' }}">
                <p class="text-sm font-semibold text-black truncate">{{ auth()->user()->name }}</p>
                <p class="text-[11px] text-gray-400 uppercase tracking-wider truncate font-medium">Administrator</p>
            </div>
        </div>

        <div class="mt-4 px-2 {{ $collapsed ? 'hidden' : '' }}">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center w-full px-2 py-2 text-xs font-bold text-gray-400 hover:text-red-500 transition-colors group">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    LOGOUT
                </button>
            </form>
        </div>
    </div>
</aside>