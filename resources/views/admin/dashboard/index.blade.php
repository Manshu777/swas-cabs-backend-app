@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
{{-- Page Header --}}
<div class="mb-10">
    <h1 class="text-4xl font-extrabold tracking-tight text-black">Dashboard</h1>
    <p class="text-gray-400 mt-2 font-medium">Welcome back, {{ auth()->user()->name }}. Here is your fleet at a glance.</p>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    {{-- Total Revenue --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Total Revenue</p>
                <p class="text-3xl font-black text-black">$45,231</p>
                <div class="mt-2 flex items-center text-xs font-bold text-black bg-gray-50 px-2 py-1 rounded-full w-fit">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    20.1%
                </div>
            </div>
            <div class="p-3 bg-black rounded-xl group-hover:scale-110 transition-transform">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- New Bookings --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">New Bookings</p>
                <p class="text-3xl font-black text-black">156</p>
                <p class="text-xs font-bold text-gray-500 mt-2">+15.3% <span class="font-medium text-gray-400">vs last month</span></p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <svg class="h-5 w-5 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                    <path d="M16 2v4M8 2v4M3 10h18"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Rented Cars --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Rented Cars</p>
                <p class="text-3xl font-black text-black">89</p>
                <p class="text-xs font-bold text-gray-500 mt-2">+12.5% <span class="font-medium text-gray-400">vs last month</span></p>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <svg class="h-5 w-5 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M7 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM17 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"></path>
                    <path d="M5 17H3V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10h-2m-3 0h-4"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Available Cars --}}
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Available Cars</p>
                <p class="text-3xl font-black text-black">67</p>
                <div class="mt-2 flex items-center text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full w-fit">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    8.2%
                </div>
            </div>
            <div class="p-3 bg-gray-50 rounded-xl">
                <svg class="h-5 w-5 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Recent Activity Table --}}
    <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-black">Recent Activity</h2>
            <button class="text-xs font-bold text-gray-400 hover:text-black transition-colors uppercase tracking-widest">View All</button>
        </div>
        <div class="overflow-x-auto px-4 pb-4">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">User</th>
                        <th class="px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Action</th>
                        <th class="px-4 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold">JD</div>
                                <span class="text-sm font-bold text-black">John Doe</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">Created new order <span class="text-black font-semibold">#123</span></td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400 text-right">2 hours ago</td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold">JS</div>
                                <span class="text-sm font-bold text-black">Jane Smith</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">Updated <span class="text-black font-semibold">Profile</span></td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-400 text-right">1 day ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="space-y-6">
        <div class="bg-black rounded-3xl p-8 shadow-xl shadow-gray-200">
            <h2 class="text-lg font-bold text-white mb-6">Quick Actions</h2>
            <div class="space-y-4">
                <a href="#" class="flex items-center justify-between p-4 bg-white/10 rounded-2xl border border-white/10 hover:bg-white/20 transition-all group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mr-4 group-hover:rotate-6 transition-transform">
                            <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <span class="text-sm font-bold text-white">Add User</span>
                    </div>
                    <svg class="w-4 h-4 text-white/40 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="#" class="flex items-center justify-between p-4 bg-white/10 rounded-2xl border border-white/10 hover:bg-white/20 transition-all group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-black" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <span class="text-sm font-bold text-white">New Order</span>
                    </div>
                    <svg class="w-4 h-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- Mini Chart Placeholder / System Health --}}
        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">System Health</span>
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
            </div>
            <div class="flex items-end space-x-1 h-12">
                <div class="flex-1 bg-gray-200 rounded-t-sm h-[60%]"></div>
                <div class="flex-1 bg-gray-200 rounded-t-sm h-[80%]"></div>
                <div class="flex-1 bg-black rounded-t-sm h-[100%]"></div>
                <div class="flex-1 bg-gray-200 rounded-t-sm h-[70%]"></div>
                <div class="flex-1 bg-gray-200 rounded-t-sm h-[90%]"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Chart.js logic could go here --}}
@endpush
@endsection