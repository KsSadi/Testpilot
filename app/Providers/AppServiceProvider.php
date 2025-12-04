<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AI Service
        $this->app->singleton('ai', function ($app) {
            return new \App\Modules\AI\Services\AIService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register module view namespaces
        $this->registerModuleViews();
        
        // Register module routes
        $this->registerModuleRoutes();
    }

    /**
     * Register view namespaces for all modules
     */
    protected function registerModuleViews(): void
    {
        $modulesPath = app_path('Modules');
        
        if (!is_dir($modulesPath)) {
            return;
        }

        $modules = scandir($modulesPath);
        
        foreach ($modules as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $modulePath = $modulesPath . DIRECTORY_SEPARATOR . $module;
            $viewsPath = $modulePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
            
            if (is_dir($viewsPath)) {
                View::addNamespace($module, $viewsPath);
            }
        }
    }

    /**
     * Register routes for all modules
     */
    protected function registerModuleRoutes(): void
    {
        $modulesPath = app_path('Modules');
        
        if (!is_dir($modulesPath)) {
            return;
        }

        $modules = scandir($modulesPath);
        
        foreach ($modules as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $modulePath = $modulesPath . DIRECTORY_SEPARATOR . $module;
            $routesPath = $modulePath . DIRECTORY_SEPARATOR . 'routes';
            
            // Load web routes
            $webRoutesFile = $routesPath . DIRECTORY_SEPARATOR . 'web.php';
            if (file_exists($webRoutesFile)) {
                \Route::middleware('web')
                    ->namespace("App\\Modules\\{$module}\\Http\\Controllers")
                    ->group($webRoutesFile);
            }
            
            // Load API routes
            $apiRoutesFile = $routesPath . DIRECTORY_SEPARATOR . 'api.php';
            if (file_exists($apiRoutesFile)) {
                \Route::prefix('api')
                    ->middleware('api')
                    ->namespace("App\\Modules\\{$module}\\Http\\Controllers")
                    ->group($apiRoutesFile);
            }
        }
    }
}
