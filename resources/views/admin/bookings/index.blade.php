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

@endpush
@endsection