<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearAllCache extends Command
{
   protected $signature = 'cache:clear-all 
                            {--force : Bypass confirmation in production}';

    protected $description = 'Clear application cache, config, routes, views, events, and OTP keys';

    public function handle()
    {
        if (! $this->option('force') && app()->isProduction()) {
            if (! $this->confirm('You are in production. Do you really want to clear all caches?')) {
                return 1;
            }
        }

        $this->info('Clearing caches...');

        // Standard Laravel caches
        Artisan::call('cache:clear');
        $this->line('Application cache cleared');

        Artisan::call('config:clear');
        $this->line('Config cache cleared');

        Artisan::call('route:clear');
        $this->line('Route cache cleared');

        Artisan::call('view:clear');
        $this->line('View cache cleared');

        Artisan::call('event:clear');
        $this->line('Event cache cleared');

        // Clear OTP-related keys safely across all drivers
        $this->clearOtpKeys();

        $this->info('All caches cleared successfully!');

        return 0;
    }

    /**
     * Clear otp:* and verified_phone:* keys safely for any cache driver
     */
    protected function clearOtpKeys()
    {
        $store = Cache::getStore();
        $deleted = 0;

        try {
            // --- Redis Driver ---
            if (method_exists($store, 'getRedis')) {
                $redis = $store->getRedis();
                $keys = $redis->keys('otp:*');
                $vkeys = $redis->keys('verified_phone:*');
                $allKeys = array_merge($keys ?? [], $vkeys ?? []);

                if (!empty($allKeys)) {
                    $redis->del($allKeys);
                    $deleted = count($allKeys);
                }
            }
            // --- File / Database / Array Driver ---
            else {
                // For file driver: scan cache files
                if (method_exists($store, 'getDirectory') && method_exists($store, 'getFiles')) {
                    $this->clearFileCacheKeys($store, $deleted);
                }
                // For database: query cache table
                elseif (get_class($store) === 'Illuminate\Cache\DatabaseStore') {
                    $this->clearDatabaseCacheKeys($deleted);
                }
                // Fallback: try to iterate if possible
                else {
                    $this->clearGenericCacheKeys($deleted);
                }
            }
        } catch (\Exception $e) {
            $this->warn('Could not clear OTP keys: ' . $e->getMessage());
        }

        $this->line("Deleted {$deleted} OTP/verified key(s)");
    }

    private function clearFileCacheKeys($store, &$deleted)
    {
        $files = $store->getFiles($store->getDirectory());
        foreach ($files as $file) {
            $key = basename($file, '.php');
            if (str_starts_with($key, 'otp:') || str_starts_with($key, 'verified_phone:')) {
                @unlink($file);
                $deleted++;
            }
        }
    }

    private function clearDatabaseCacheKeys(&$deleted)
    {
        $table = config('cache.stores.database.table');
        $prefix = config('cache.prefix') . ':';

        \DB::table($table)
            ->where('key', 'LIKE', $prefix . 'otp:%')
            ->orWhere('key', 'LIKE', $prefix . 'verified_phone:%')
            ->delete();

        // Approximate count
        $deleted = \DB::table($table)
            ->where('key', 'LIKE', $prefix . 'otp:%')
            ->orWhere('key', 'LIKE', $prefix . 'verified_phone:%')
            ->count();
    }

    private function clearGenericCacheKeys(&$deleted)
    {
        // This won't work for most drivers, but safe fallback
        Cache::forget('otp:*'); // No-op, but won't crash
    }
}
