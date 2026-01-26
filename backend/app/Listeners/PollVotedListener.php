<?php

namespace App\Listeners;

use App\Events\PollVotedEvent;
use App\Jobs\PollVotedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PollVotedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PollVotedEvent $event): void
    {
        PollVotedJob::dispatch(
            $event->pollId,
            $event->optionId,
            $event->userId
        );
    }
}