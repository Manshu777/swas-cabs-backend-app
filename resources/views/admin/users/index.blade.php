@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Bookings Management</h1>
    <p class="text-gray-600 mt-1">Manage all ride bookings for passengers.</p>
</div>

@php
    // Dummy data with role field to simulate User model
    $dummyData = [
        [
            'id' => 'RID001',
            'customer' => ['name' => 'John Doe', 'role' => 'passenger'],
            'driver' => ['name' => 'Mike Johnson', 'role' => 'driver'],
            'route' => ['from' => 'Delhi', 'to' => 'Noida'],
            'status' => ['label' => 'completed', 'color' => 'completed'],
            'type' => 'one-way',
            'time' => '2:30 PM - 3:15 PM',
            'fare' => 450.00
        ],
        [
            'id' => 'RID002',
            'customer' => ['name' => 'Jane Smith', 'role' => 'passenger'],
            'driver' => ['name' => 'Sarah Wilson', 'role' => 'driver'],
            'route' => ['from' => 'Mumbai', 'to' => 'Pune'],
            'status' => ['label' => 'pending', 'color' => 'pending'],
            'type' => 'round-trip',
            'time' => '10:00 AM - 12:00 PM',
            'fare' => 1200.00
        ],
        [
            'id' => 'RID003',
            'customer' => ['name' => 'Bob Johnson', 'role' => 'driver'], // This will be filtered out
            'driver' => ['name' => 'Tom Brown', 'role' => 'driver'],
            'route' => ['from' => 'Bangalore', 'to' => 'Mysore'],
            'status' => ['label' => 'cancelled', 'color' => 'cancelled'],
            'type' => 'one-way',
            'time' => '6:45 PM - 8:30 PM',
            'fare' => 0.00
        ],
        [
            'id' => 'RID004',
            'customer' => ['name' => 'Alice Brown', 'role' => 'passenger'],
            'driver' => ['name' => 'David Lee', 'role' => 'driver'],
            'route' => ['from' => 'Chennai', 'to' => 'Salem'],
            'status' => ['label' => 'completed', 'color' => 'completed'],
            'type' => 'one-way',
            'time' => '9:00 AM - 10:30 AM',
            'fare' => 600.00
        ],
    ];

    // Filter for passengers only
    $rides = array_filter($dummyData, function ($ride) {
        return $ride['customer']['role'] === 'passenger';
    });

    // Calculate summary statistics
    $totalRides = count($rides);
    $totalFare = array_sum(array_column($rides, 'fare'));
    $averageFare = $totalRides > 0 ? $totalFare / $totalRides : 0;
    $statusCounts = array_reduce($rides, function ($carry, $ride) {
        $status = $ride['status']['label'];
        $carry[$status] = ($carry[$status] ?? 0) + 1;
        return $carry;
    }, []);

    $stats = [
        'total_rides' => $totalRides,
        'total_fare' => $totalFare,
        'average_fare' => $averageFare,
        'status_counts' => $statusCounts
    ];

    $headers = ['Action', 'Ride ID', 'Customer', 'Driver', 'Route', 'Status', 'Type', 'Time', 'Fare'];
@endphp

<!-- Summary Statistics -->
<div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold">Total Rides</h3>
        <p class="text-2xl font-bold">{{ $stats['total_rides'] }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold">Total Fare</h3>
        <p class="text-2xl font-bold">₹{{ number_format($stats['total_fare'], 2) }}</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold">Average Fare</h3>
        <p class="text-2xl font-bold">₹{{ number_format($stats['average_fare'], 2) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 flex flex-col md:flex-row gap-4">
    <div>
        <label for="status-filter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
        <select id="status-filter" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">All</option>
            <option value="completed">Completed</option>
            <option value="pending">Pending</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>
</div>

<!-- Table -->
<div class="bg-white shadow-sm rounded-lg p-4 w-full max-w-full overflow-auto" style="max-height: calc(100vh - 200px);">
    <x-table :data="$rides" :headers="$headers" caption="Ride data for passengers." />
</div>

<!-- Chart -->
<div class="mt-6 bg-white shadow-sm rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Rides by Status</h3>
   
@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
// Initialize DataTables for sorting and searching
document.addEventListener('DOMContentLoaded', function() {
const table = document.querySelector('table');
if (table) {
const dataTable = $(table).DataTable({
responsive: true,
columnDefs: [
{ orderable: false, targets: 0 } // Disable sorting on Action column
]
});

// Filter by status
document.getElementById('status-filter').addEventListener('change', function() {
const status = this.value;
if status {
dataTable.column(5).search(status).draw(); // Column 5 is Status
} else {
dataTable.column(5).search('').draw();
}
});
}

// Load sidebar state
const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
if (collapsed) {
document.getElementById('sidebar').classList.add('w-16');
document.getElementById('sidebar').classList.remove('w-72');
document.querySelector('.flex.flex-1.flex-col').classList.add('lg:ml-16');
document.querySelector('.flex.flex-1.flex-col').classList.remove('lg:ml-72');
}

const toggleBtn = document.getElementById('sidebar-toggle');
if (toggleBtn) {
toggleBtn.addEventListener('click', toggleSidebar);
}
});

function toggleCollapsed() {
const sidebar = document.getElementById('sidebar');
const isCollapsed = sidebar.classList.contains('w-16');
const newCollapsed = !isCollapsed;

sidebar.classList.toggle('w-16', newCollapsed);
sidebar.classList.toggle('w-72', !newCollapsed);

const mainContent = document.querySelector('.flex.flex-1.flex-col');
mainContent.classList.toggle('lg:ml-16', newCollapsed);
mainContent.classList.toggle('lg:ml-72', !newCollapsed);

localStorage.setItem('sidebar_collapsed', newCollapsed.toString());
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

// Sidebar state debugging
const checkSidebarState = () => {
const sidebar = document.getElementById('sidebar');
const isCollapsed = sidebar.classList.contains('w-16');
const isMobile = /Mobile|Android|iPhone/.test(navigator.userAgent);

console.log('=== Sidebar State Debug ===');
console.log('Collapsed:', isCollapsed);
console.log('Mobile View:', isMobile);
console.log('Current Width Class:', sidebar.classList.contains('w-16') ? 'w-16 (collapsed)' : 'w-72 (expanded)');
console.log('TranslateX Class:', sidebar.classList.contains('-translate-x-full') ? '-translate-x-full (hidden on mobile)' : 'translate-x-0 (visible)');
console.log('Session/Cookie Source (approx):', localStorage.getItem('sidebar_collapsed') || 'N/A');

return { collapsed: isCollapsed, mobile: isMobile };
};
checkSidebarState();
</script>
@endpush
@endsection