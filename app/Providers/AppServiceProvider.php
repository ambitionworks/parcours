<?php

namespace App\Providers;

use App\Observers\DropboxTokenObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Dcblogdev\Dropbox\Models\DropboxToken;

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
        DropboxToken::observe(DropboxTokenObserver::class);

        Blade::directive('datetime', function ($expression) {
            return "<?php
                echo ($expression)->isToday()
                    ? __('Today')
                    :  (($expression)->isYesterday()
                        ? __('Yesterday')
                        : ($expression)->format('M d')) .
                (($expression)->format('Y') !== date('Y') ? ($expression)->format(' Y ') : '') .
                ' ' . __('at') . ' ' .
                ($expression)->format('h:i a');
            ?>";
        });
    }
}
