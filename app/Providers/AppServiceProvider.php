<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function($view) {
          $view->with('authUser', Auth::user());
        });
        Blade::if('hasRole', function($roles) {
            $user = Auth::user();
            return collect($roles)->reduce(function($value, $role) use ($user) {
                return $value || $user->hasRole($role);
            }, false);
        });

        Blade::if('hasAllRoles', function($roles) {
            $user = Auth::user();
            return collect($roles)->reduce(function($value, $role) use ($user) {
                return $value && $user->hasRole($role);
            }, false);
        });

        Blade::if('puedeAvanzar', function($doc) {
            return $doc->puedeAvanzar(Auth::user());
        });
    }
}
