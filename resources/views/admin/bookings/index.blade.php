@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Bookings Management</h1>
    <p class="text-gray-600 mt-1">Manage all ride bookings and details.</p>
</div>

@php
    // Dummy data - Replace with real data from your controller
    $dummyData = [
        [
            'id' => 'RID001',
            'customer' => ['name' => 'John Doe'],
            'driver' => ['name' => 'Mike Johnson'],
            'route' => ['from' => 'Delhi', 'to' => 'Noida'],
            'status' => ['label' => 'completed', 'color' => 'completed'],
            'type' => 'one-way',
            'time' => '2:30 PM - 3:15 PM',
            'fare' => 450.00
        ],
        [
            'id' => 'RID002',
            'customer' => ['name' => 'Jane Smith'],
            'driver' => ['name' => 'Sarah Wilson'],
            'route' => ['from' => 'Mumbai', 'to' => 'Pune'],
            'status' => ['label' => 'pending', 'color' => 'pending'],
            'type' => 'round-trip',
            'time' => '10:00 AM - 12:00 PM',
            'fare' => 1200.00
        ],
        [
            'id' => 'RID003',
            'customer' => ['name' => 'Bob Johnson'],
            'driver' => ['name' => 'Tom Brown'],
            'route' => ['from' => 'Bangalore', 'to' => 'Mysore'],
            'status' => ['label' => 'cancelled', 'color' => 'cancelled'],
            'type' => 'one-way',
            'time' => '6:45 PM - 8:30 PM',
            'fare' => 0.00
        ],
        // Add more dummy rows as needed
    ];

    $headers = ['Action', 'Ride ID', 'Customer', 'Driver', 'Route', 'Status', 'Type', 'Time', 'Fare'];
@endphp

<div class="bg-white shadow-sm rounded-lg p-4 w-full max-w-full overflow-auto" style="max-height: calc(100vh - 200px);">
    <x-table :data="$dummyData" :headers="$headers" caption="Ride data from the last 30 days." />
</div>

@push('scripts')
<script>
    function toggleCollapsed() {
        const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.classList.contains('w-16');
        const newCollapsed = !isCollapsed;
        
        sidebar.classList.toggle('w-16', newCollapsed);
        sidebar.classList.toggle('w-72', !newCollapsed);
        
        // Update main content margin
        const mainContent = document.querySelector('.flex.flex-1.flex-col');
        mainContent.classList.toggle('lg:ml-16', newCollapsed);
        mainContent.classList.toggle('lg:ml-72', !newCollapsed);
        console.log('Sidebar collapsed state:', newCollapsed);
        
        // Persist in localStorage
        localStorage.setItem('sidebar_collapsed', newCollapsed.toString());
        
        // Optional: Trigger a reflow or resize event for table responsiveness
        window.dispatchEvent(new Event('resize'));
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
    const checkSidebarState =()=>{
            const sidebar = document.getElementById('sidebar');
        const isCollapsed = sidebar.classList.contains('w-16'); // Based on your width class logic
        const isMobile = /Mobile|Android|iPhone/.test(navigator.userAgent);
        
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
@endpush
@endsection