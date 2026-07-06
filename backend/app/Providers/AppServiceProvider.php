<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\View\Composers\MenuComposer;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register view composer for admin menu
        View::composer('layouts.menu-cms', MenuComposer::class);
        
        // Set PHP upload limits at runtime (if allowed)
        $this->setUploadLimits();

        // Configure API rate limiters
        $this->configureRateLimiting();

        // Use Bootstrap 5 for pagination links globally
        Paginator::useBootstrapFive();
    }
    
    /**
     * Set PHP upload limits at runtime
     */
    private function setUploadLimits(): void
    {
        // Try to increase upload limits if current values are too low
        $currentUploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $currentPostMax = $this->parseSize(ini_get('post_max_size'));
        
        $targetUploadMax = 200 * 1024 * 1024; // 200MB
        $targetPostMax = 210 * 1024 * 1024; // 210MB
        
        // Only increase if current limit is lower than target
        if ($currentUploadMax < $targetUploadMax) {
            @ini_set('upload_max_filesize', '200M');
        }
        
        if ($currentPostMax < $targetPostMax) {
            @ini_set('post_max_size', '210M');
        }
        
        // Set execution time limits for large uploads
        @ini_set('max_execution_time', '300');
        @ini_set('max_input_time', '300');
    }
    
    /**
     * Parse size string (e.g., "200M", "8M") to bytes
     */
    private function parseSize(string $size): int
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);
        $size = (int) $size;
        
        switch ($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }
        
        return $size;
    }

    /**
     * Configure API rate limiting rules to mitigate abuse/DDOS
     */
    private function configureRateLimiting(): void
    {
        // Global API limiter for most public GET endpoints
        RateLimiter::for('global', function (Request $request) {
            $key = $request->user()?->id ? ('user:' . $request->user()->id) : ('ip:' . $request->ip());
            return Limit::perMinute(300)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                ], 429);
            });
        });

        // Analytics endpoints (receive high volume, but still controlled)
        RateLimiter::for('analytics', function (Request $request) {
            $key = 'ip:' . $request->ip();
            return Limit::perMinute(120)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate limit exceeded for analytics.',
                ], 429);
            });
        });

        // Support ticket creation/status checks
        RateLimiter::for('support', function (Request $request) {
            $key = 'ip:' . $request->ip();
            return Limit::perMinute(30)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many support requests. Try again shortly.',
                ], 429);
            });
        });

        // Contact form submissions
        RateLimiter::for('contact', function (Request $request) {
            $key = 'ip:' . $request->ip();
            return Limit::perMinute(30)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many contact requests. Please wait and retry.',
                ], 429);
            });
        });

        // Partner registrations
        RateLimiter::for('partners', function (Request $request) {
            $key = 'ip:' . $request->ip();
            return Limit::perMinute(20)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many registration attempts. Please try later.',
                ], 429);
            });
        });

        // Sales requirements
        RateLimiter::for('sales', function (Request $request) {
            $key = 'ip:' . $request->ip();
            return Limit::perMinute(20)->by($key)->response(function () {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many submissions. Please try again later.',
                ], 429);
            });
        });
    }
}
