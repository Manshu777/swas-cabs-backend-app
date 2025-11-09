<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - @yield('title')</title>
      <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>

    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 font-sans transition-colors duration-500">

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')


        <div class="flex flex-1 flex-col overflow-x-hidden {{ session('sidebar_collapsed', false) ? 'lg:ml-16' : 'lg:ml-72' }} transition-all duration-300 ease-in-out">

            @include('layouts.partials.header')


            <main class="flex-1 p-6 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>




    <script>
    function toggleCollapsed() {
        const textappear = document.getElementById("textappear")
        const sidebar = document.getElementById('sidebar');
        const NavigationText = document.querySelectorAll('.navigation-bar-text')

        const isCollapsed = sidebar.classList.contains('w-16');
        const newCollapsed = !isCollapsed;

        sidebar.classList.toggle('w-16', newCollapsed);
        sidebar.classList.toggle('w-72', !newCollapsed);

        if (textappear) {
            textappear.classList.toggle('hidden', newCollapsed);
        }

// Toggle all navigation texts (multiple elements)
        NavigationText.forEach(el => {
            el.classList.toggle('hidden', newCollapsed);
        });


        // Update main content margin
        const mainContent = document.querySelector('.flex.flex-1.flex-col');
        mainContent.classList.toggle('lg:ml-16', newCollapsed);
        mainContent.classList.toggle('lg:ml-72', !newCollapsed);

        // Persist in localStorage
        localStorage.setItem('sidebar_collapsed', newCollapsed.toString());


    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobile-backdrop');
        sidebar.classList.toggle('-translate-x-full');
        if (backdrop) backdrop.classList.toggle('hidden');
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('mobile-backdrop');
        sidebar.classList.add('-translate-x-full');
        if (backdrop) backdrop.classList.add('hidden');
    }

    // Initialize on DOM load
    document.addEventListener('DOMContentLoaded', function() {
        // Load collapsed state
        const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (collapsed) {
            document.getElementById('sidebar').classList.add('w-16');
            document.getElementById('sidebar').classList.remove('w-72');
            document.querySelector('.flex.flex-1.flex-col').classList.add('lg:ml-16');
            document.querySelector('.flex.flex-1.flex-col').classList.remove('lg:ml-72');
        }

        // Mobile toggle
        const toggleBtn = document.getElementById('sidebar-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }
    });
</script>
 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;

    // Apply stored theme on load
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark');
    } else {
        body.classList.remove('dark');
    }

    // Toggle on click
    themeToggle?.addEventListener('click', function () {
        const isDark = body.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    });
});
</script>

<script>
    const checkSidebarState =()=>{
            const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.classList.contains('w-16'); // Based on your width class logic
        const isMobile = /Mobile|Android|iPhone/.test(navigator.userAgent);
           const NavigationText = document.querySelectorAll('.navigation-bar-text')
 const textappear = document.getElementById("textappear")
        console.log('isCollapsed',isCollapsed)
        

         sidebar.classList.toggle('w-16', isCollapsed);
        sidebar.classList.toggle('w-72', !isCollapsed);

                    if (textappear) {
            textappear.classList.toggle('hidden', !isCollapsed);
        }

// Toggle all navigation texts (multiple elements)
        NavigationText.forEach(el => {
            el.classList.toggle('hidden', !isCollapsed);
        });

        

         

        console.log('=== Sidebar State Debug ===');
        console.log('Collapsed:', isCollapsed);
        console.log('Mobile View:', isMobile);
        console.log('Current Width Class:', sidebar.classList.contains('w-16') ? 'w-16 (collapsed)' : 'w-72 (expanded)');
        console.log('TranslateX Class:', sidebar.classList.contains('-translate-x-full') ? '-translate-x-full (hidden on mobile)' : 'translate-x-0 (visible)');
        console.log('Session/Cookie Source (approx):', sessionStorage.getItem('sidebar_collapsed') || 'N/A (check network tab for cookies)');

        return { collapsed: isCollapsed, mobile: isMobile };
    }
    checkSidebarState()

</script>
    @stack('scripts')


</body>
</html>
