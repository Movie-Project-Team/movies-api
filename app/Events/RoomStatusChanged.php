<?php

namespace App\Events;

use App\Models\DB\Rooms;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RoomStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    /**
     * Khởi tạo event với thông tin phòng.
     */
    public function __construct(Rooms $room)
    {
        $this->room = $room;
    }

    /**
     * Định nghĩa kênh broadcast. Dùng Presence Channel cho phòng cụ thể.
     */
    public function broadcastOn()
    {
        // Ví dụ: kênh có tên "room.{id}" để các client subscribe cụ thể phòng đó.
        return new PresenceChannel('room.' . $this->room->id);
    }

    /**
     * Tên sự kiện broadcast (nếu cần tuỳ chỉnh).
     */
    public function broadcastAs()
    {
        return 'room.status.changed';
    }
}
