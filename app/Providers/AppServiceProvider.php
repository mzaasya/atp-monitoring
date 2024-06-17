<?php

namespace App\Providers;

use App\Models\Task;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // $invitation = Task::where('status', '=', 'invitation')->count();
        // $rectification = Task::where('status', '=', 'rectification')->count();
        // View::share('unconfirmed', $invitation);
        // View::share('rectification', $rectification);
    }
}
