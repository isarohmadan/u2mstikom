<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use App\Models\DocumentTemplate;
use App\Policies\DocumentTemplatePolicy;

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
        Gate::policy(DocumentTemplate::class, DocumentTemplatePolicy::class);
        // Add formatBytes helper function
        Blade::directive('formatBytes', function ($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            
            $bytes /= (1 << (10 * $pow));
            
            return round($bytes, $precision) . ' ' . $units[$pow];
        });
        
        // Or as a global helper function
        if (!function_exists('formatBytes')) {
            function formatBytes($bytes, $precision = 2) {
                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                
                $bytes = max($bytes, 0);
                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                $pow = min($pow, count($units) - 1);
                
                $bytes /= (1 << (10 * $pow));
                
                return round($bytes, $precision) . ' ' . $units[$pow];
            }
        }
    }
}
