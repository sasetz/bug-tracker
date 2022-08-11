<?php

namespace App\Providers;

use App\Events\NewUpdateCreated;
use App\Listeners\SendInviteReceivedNotification;
use App\Listeners\SendTicketUpdateNotification;
use App\Notifications\InviteReceived;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        InviteReceived::class => [
            SendInviteReceivedNotification::class,
        ],
        NewUpdateCreated::class => [
            SendTicketUpdateNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
