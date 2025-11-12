<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheController extends Controller
{
    public function clear(Request $request)
    {
        // Optional: re-validate admin role
        // if (! $request->user()->isAdmin()) { abort(403); }

        try {
            // Run the artisan command silently
            Artisan::call('cache:clear-all', ['--force' => true]);

            // Or run inline (no Redis dependency):
            // $this->clearCachesManually();

            return response()->json([
                'message' => 'All caches cleared successfully.',
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Cache clear failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to clear cache.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Optional: Manual clear without Redis
    private function clearCachesManually()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('event:clear');

        // Clear OTP keys using pattern (works with file, database, etc.)
        $pattern = 'otp:*';
        foreach (Cache::getStore()->getAllKeys() ?? [] as $key) {
            if (str_starts_with($key, 'otp:') || str_starts_with($key, 'verified_phone:')) {
                Cache::forget($key);
            }
        }
    }
}
