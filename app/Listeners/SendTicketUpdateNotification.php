<?php

namespace App\Listeners;

use App\Events\NewUpdateCreated;
use App\Notifications\SubscribedTicketUpdated;

class SendTicketUpdateNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NewUpdateCreated $event
     * @return void
     */
    public function handle(NewUpdateCreated $event): void
    {
        $event->update->ticket->subscribers->each(function ($item, $key) use ($event) {
            $item->notify(new SubscribedTicketUpdated($event->update));
        });
    }
}
