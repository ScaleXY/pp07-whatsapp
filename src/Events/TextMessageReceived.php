<?php

namespace ScaleXY\Whatsapp\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TextMessageReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;

    public $message;

    public $full_change;

    /**
     * Create a new event instance.
     */
    public function __construct($sender, $message, $full_change)
    {
        $this->sender = $sender;
        $this->message = $message;
        $this->full_change = $full_change;
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
