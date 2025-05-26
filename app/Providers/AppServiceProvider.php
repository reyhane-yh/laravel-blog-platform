<?php

namespace App\Providers;

use App\Events\PostDeleted;
use App\Events\PostPublished;
use App\Events\PostUpdated;
use App\Listeners\DeleteScheduledJob;
use App\Listeners\NotifyUsers;
use Illuminate\Support\Facades\Event;
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
        Event::listen(
            PostUpdated::class,
            [DeleteScheduledJob::class, 'handle']
        );

        Event::listen(
            PostDeleted::class,
            [DeleteScheduledJob::class, 'handle']
        );

        Event::listen(
            PostPublished::class,
            [NotifyUsers::class, 'handle']
        );
    }
}
