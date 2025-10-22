@extends('layouts.admin')

@section('title', 'Driver Bookings')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Driver Bookings Management</h1>
    <p class="text-gray-600 mt-1">Manage all ride bookings assigned to drivers.</p>
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
            'customer' => ['name' => 'Bob Johnson', 'role' => 'passenger'],
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
            'driver' => ['name' => 'David Lee', 'role' => 'passenger'], // This will be filtered out
            'route' => ['from' => 'Chennai', 'to' => 'Salem'],
            'status' => ['label' => 'completed', 'color' => 'completed'],
            'type' => 'one-way',
            'time' => '9:00 AM - 10:30 AM',
            'fare' => 600.00
        ],
    ];

    // Filter for drivers only
    $rides = array_filter($dummyData, function ($ride) {
        return $ride['driver']['role'] === 'driver';
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
    <x-table :data="$rides" :headers="$headers" caption="Ride data for drivers." />
</div>

<!-- Chart -->
<div class="mt-6 bg-white shadow-sm rounded-lg p-4">
    <h3 class="text-lg font-semibold mb-4">Rides by Status (Drivers)</h3>
    ```chartjs
    {
        "type": "bar",
        "data": {
            "labels": ["Completed", "Pending", "Cancelled"],
            "datasets": [{
                "label": "Number of Rides",
                "data": [
                    {{ $stats['status_counts']['completed'] ?? 0 }},
                    {{ $stats['status_counts']['pending'] ?? 0 }},
                    {{ $stats['status_counts']['cancelled'] ?? 0 }}
                ],
                "backgroundColor": ["#34D399", "#FBBF24", "#EF4444"],
                "borderColor": ["#10B981", "#F59E0B", "#DC2626"],
                "borderWidth": 1
            }]
        },
        "options": {
            "scales": {
                "y": {
                    "beginAtZero": true,
                    "title": {
                        "display": true,
                        "text": "Number of Rides"
                    }
                },
                "x": {
                    "title": {
                        "display": true,
                        "text": "Status"
                    }
                }
            },
            "plugins": {
                "legend": {
                    "display": false
                }
            }
        }
    }


    ```
    @push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>



@endpush
@endsection