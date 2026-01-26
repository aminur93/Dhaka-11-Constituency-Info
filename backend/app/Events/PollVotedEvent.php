<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PollVotedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $pollId;
    public int $optionId;
    public ?int $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(int $pollId, int $optionId, ?int $userId)
    {
        $this->pollId   = $pollId;
        $this->optionId = $optionId;
        $this->userId   = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}