<?php

namespace App\Providers;

use App\Models\Page;
use App\Observers\PageObserver;
use Illuminate\Support\ServiceProvider;
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
        Schema::defaultStringLength(191);
        
        // Регистрируем наблюдатель
        Page::observe(PageObserver::class);
    }
}
