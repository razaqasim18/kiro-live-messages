<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class CallGiftEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $sender;
    public $receiver;
    public $gift;
    public $isgift;
    public function __construct($sender, $receiver, $gift, $isgift)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->gift = $gift;
        $this->isgift = $isgift;
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('callgift.' .   $this->receiver->id),
        ];
    }

    public function broadcastAs()
    {
        return 'call-gift-message';
    }

    public function broadcastWith()
    {
        return [
            'sender'   => $this->sender,
            'receiver' => $this->receiver,
            'gift'  => $this->gift,
            'isgift'   => $this->isgift,
        ];
    }
}
