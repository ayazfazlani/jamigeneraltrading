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
        Item::observe(ItemObserver::class);

        // Define Gate for admin access
        // Gate::define('admin', function (User $user) {
        //     return $user->hasAnyRole(); // Ensure the user has the 'admin' role
        // });

        Gate::define('admin', function (User $user) {
            return $user->hasRole('user');
        });

        // Create default roles if they don't exist
        // if (Role::count() === 0) {
        //     Role::create(['name' => 'admin']);
        //     Role::create(['name' => 'user']);
        // }
    }
}
