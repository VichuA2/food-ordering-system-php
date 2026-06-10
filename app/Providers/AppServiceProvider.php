<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
        View::composer(['users.index', 'users.create', 'users.edit'], function ($view) {

            $roles = Cache::remember('roles_cache', now()->addHours(24), function () {
                return Role::pluck('name')->toArray();
            });

            $view->with('roles', $roles);
        });
    }
}
