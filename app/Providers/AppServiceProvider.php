<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Required
use Illuminate\Support\Facades\Cookie; // Optional but helpful

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
   public function boot(): void
{
    \Illuminate\Support\Facades\View::composer('layouts.partials.sidebar', function ($view) {
        // 1. Sidebar State
        $collapsed = session('sidebar_collapsed', false) 
                     || (request()->cookie('sidebar_collapsed') === 'true');

        // 2. Navigation Array (Moved from Blade to here)
        $navigation = [
            ['name' => 'Dashboard', 'icon' => 'layout-dashboard', 'active' => request()->routeIs('admin.dashboard'), 'href' => route('admin.dashboard')],
            ['name' => 'Bookings', 'icon' => 'calendar', 'active' => request()->routeIs('admin.bookings.*'), 'href' => route('admin.bookings')],
            ['name' => 'Units (Cars)', 'icon' => 'car', 'active' => request()->routeIs('admin.units.*'), 'href' => route('admin.units')],
            ['name' => 'Users', 'icon' => 'users', 'active' => request()->routeIs('admin.users.*'), 'href' => route('admin.users')],
            ['name' => 'Drivers', 'icon' => 'user-check', 'active' => request()->routeIs('admin.drivers.*'), 'href' => route('admin.drivers')],
            ['name' => 'Drivers Documents', 'icon' => 'document', 'active' => request()->routeIs('admin.documents.*'), 'href' => route('admin.documents')],
            ['name' => 'Drivers vehicle', 'icon' => 'vehicle', 'active' => request()->routeIs('admin.vehicles.*'), 'href' => route('admin.vehicles')],
            ['name' => 'Rides', 'icon' => 'map-pin', 'active' => request()->routeIs('admin.rides.*'), 'href' => route('admin.rides')],
            ['name' => 'Financial', 'icon' => 'rupee', 'active' => request()->routeIs('admin.financial.*'), 'href' => route('admin.financial')],
            ['name' => 'Tour Packages', 'icon' => 'package', 'active' => request()->routeIs('admin.packages.*'), 'href' => route('admin.packages')],
            ['name' => 'SOS Monitoring', 'icon' => 'alert-triangle', 'active' => request()->routeIs('admin.sos.*'), 'href' => route('admin.sos')],
            ['name' => 'Settings', 'icon' => 'settings', 'active' => request()->routeIs('admin.settings.*'), 'href' => route('admin.settings')],
        ];

        // 3. Icons Array
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
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l-.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
            'document' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>',
            'vehicle' => '<path d="M3 13l2-5h14l2 5M5 13v6a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1h8v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-6M3 13h18"/><circle cx="7.5" cy="18.5" r="1.5"/><circle cx="16.5" cy="18.5" r="1.5"/>',
        ];

        // Pass all variables to the view
        $view->with([
            'collapsed' => $collapsed,
            'navigation' => $navigation,
            'icons' => $icons
        ]);
    });
}
}