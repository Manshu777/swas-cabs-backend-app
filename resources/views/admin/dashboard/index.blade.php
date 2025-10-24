@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold ">Dashboard</h1>
    <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with your application.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Revenue --}}
    <div class="cards-data p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                <p class="text-2xl font-bold ">$45,231</p>
                <p class="text-sm text-green-600">+20.1% from last month</p>
            </div>
            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- New Bookings --}}
    <div class="cards-data p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">New Bookings</p>
                <p class="text-2xl font-bold ">156</p>
                <p class="text-sm text-green-600">+15.3% from last month</p>
            </div>
            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                    <line x1="16" x2="16" y1="2" y2="6"></line>
                    <line x1="8" x2="8" y1="2" y2="6"></line>
                    <line x1="3" x2="21" y1="10" y2="10"></line>
                </svg>
            </div>
        </div>
    </div>

    {{-- Rented Cars --}}
    <div class="cards-data p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Rented Cars</p>
                <p class="text-2xl font-bold ">89</p>
                <p class="text-sm text-green-600">+12.5% from last month</p>
            </div>
            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect width="18" height="11" x="3" y="6" rx="2"></rect>
                    <path d="M21 10h-3V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v3H3a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1z"></path>
                    <circle cx="7" cy="14" r="1"></circle>
                    <circle cx="17" cy="14" r="1"></circle>
                </svg>
            </div>
        </div>
    </div>

    {{-- Available Cars --}}
    <div class="cards-data p-6 rounded-lg shadow-md">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Available Cars</p>
                <p class="text-2xl font-bold ">67</p>
                <p class="text-sm text-red-600">-8.2% from last month</p>
            </div>
            <div class="h-12 w-12 rounded-full bg-blue-50 flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Activity Table -->
    <div class=" rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold ">Recent Activity</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    </tr>
                </thead>
                <tbody class=" divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Created new order #123</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2 hours ago</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Updated profile</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1 day ago</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium ">Bob Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Logged in</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">3 days ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class=" rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold  mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="text-gray-700">Add New User</span>
            </a>
            <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-gray-700">Create Order</span>
            </a>
            <a href="#" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="text-gray-700">View Reports</span>
            </a>
        </div>
    </div>
</div>



@push('scripts')

@endpush
@endsection
