<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        Gate::before(function ($user, $ability) {
            return $user->id == 1 ? true : null;
        });

        Route::bind('material', function (string $value) {
            return Service::query()->findOrFail($value);
        });

        View::composer('adminlte::page', function ($view) {
            $lowStockCount = 0;

            if (Schema::hasTable('services') && Schema::hasColumn('services', 'quantity')) {
                $lowStockCount = Service::query()
                    ->whereColumn('quantity', '<=', 'min_stock_level')
                    ->count();
            }

            $view->with('lowStockCount', $lowStockCount);
        });
    }
}
