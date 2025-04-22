<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
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
    public function boot()
{
    Inertia::share([
        'auth' => function () {
            $user = Auth::user();
            return [
                'user' => $user,
                'roles' => $user?->getRoleNames(), // colección de roles
                'permissions' => $user?->getAllPermissions()->pluck('name'), // permisos
            ];
        },
    ]);
}
}
