<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncomingCall implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $caller;
    public $receiver;
    public $channelname;
    public $joinUrl;
    public $declineUrl;

    public function __construct(
        $caller,
        $receiver,
        $channelname,
        $joinUrl,
    ) {
        $this->caller = $caller;           // Caller info: ['id' => 5, 'name' => 'Qasim']
        $this->receiver = $receiver;   // The remote user who will receive the popup
        $this->channelname = $channelname; // Agora channel name
        $this->joinUrl = $joinUrl;
    }

    public function broadcastOn()
    {
        // Broadcast specifically to the remote user's private channel
        return new PrivateChannel('calluser.' . $this->receiver->id);
    }

    public function broadcastAs()
    {
        return 'incoming-call';
    }
}
