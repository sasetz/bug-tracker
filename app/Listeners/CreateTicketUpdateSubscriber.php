<?php

namespace App\Listeners;

use App\Events\AssigneeChanged;
use App\Events\CommentPosted;
use App\Events\LabelsChanged;
use App\Events\PriorityChanged;
use App\Events\StatusChanged;
use App\Events\TitleChanged;

class CreateTicketUpdateSubscriber
{
    /**
     * @param AssigneeChanged $event
     * @return void
     */
    public function handleAssignee(AssigneeChanged $event): void
    {

    }

    /**
     * @param CommentPosted $event
     * @return void
     */
    public function handleComment(CommentPosted $event): void
    {
        
    }

    /**
     * @param LabelsChanged $event
     * @return void
     */
    public function handleLabel(LabelsChanged $event): void
    {
        //
    }

    /**
     * @param PriorityChanged $event
     * @return void
     */
    public function handlePriority(PriorityChanged $event): void
    {
        //
    }

    /**
     * @param StatusChanged $event
     * @return void
     */
    public function handleStatus(StatusChanged $event): void
    {
        //
    }

    /**
     * @param TitleChanged $event
     * @return void
     */
    public function handleTitle(TitleChanged $event): void
    {
        //
    }

    public function subscribe($events): void
    {
        $events->listen(
            AssigneeChanged::class,
            [CreateTicketUpdateSubscriber::class, 'handleAssignee']
        );


        $events->listen(
            CommentPosted::class,
            [CreateTicketUpdateSubscriber::class, 'handleComment']
        );

        $events->listen(
            LabelsChanged::class,
            [CreateTicketUpdateSubscriber::class, 'handleLabel']
        );

        $events->listen(
            PriorityChanged::class,
            [CreateTicketUpdateSubscriber::class, 'handlePriority']
        );

        $events->listen(
            StatusChanged::class,
            [CreateTicketUpdateSubscriber::class, 'handleStatus']
        );

        $events->listen(
            TitleChanged::class,
            [CreateTicketUpdateSubscriber::class, 'handleTitle']
        );
    }
}
