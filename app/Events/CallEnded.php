<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallEnded implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $receiver;
    public $decliner;

    public function __construct($receiver, $decliner)
    {
        $this->receiver = $receiver;
        $this->decliner = $decliner;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('enduser.' . $this->receiver->id);
    }

    public function broadcastAs()
    {
        return 'call-ended';
    }
}
