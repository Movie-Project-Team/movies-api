<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SignalingEvent implements \Illuminate\Contracts\Broadcasting\ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $senderId;
    public $signalData;

    public function __construct($roomId, $senderId, $signalData)
    {
        $this->roomId    = $roomId;
        $this->senderId  = $senderId;
        $this->signalData = $signalData;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room.' . $this->roomId);
    }
}
