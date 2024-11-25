<?php

namespace App\Providers;

use App\Models\Item;
use App\Models\User;
use App\Observers\ItemObserver;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register application services if needed
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Attach observer to the Item model
        // Item::observe(ItemObserver::class);

        Gate::before(function ($user) {
            if ($user->hasRole('super admin')) {
                return true;
            }
        });

        Gate::define('viewer', function ($user) {
            return $user->role === 'viewer'; // Example condition
        });
        // Gate::define('manage-team', function ($user) {
        //     return $user->hasRole(['super admin', 'team admin']);
        // });

        // Gate::define('view-items', function ($user) {
        //     return $user->can('view items');
        // });

        // Gate::define('edit-items', function ($user) {
        //     return $user->can('edit items');
        // });
    }
}