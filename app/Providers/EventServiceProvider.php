<?php

namespace App\Providers;

use App\Events\SendOtpEvent;
use App\Events\VerifyUserEvent;
use App\Listeners\JobProcessedListener;
use App\Listeners\SendOtpListener;
use App\Listeners\VerifyUserListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendOtpEvent::class => [
            SendOtpListener::class,
        ],
        VerifyUserEvent::class => [
            VerifyUserListener::class,
        ],
        JobProcessed::class => [
            JobProcessedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

    
}
