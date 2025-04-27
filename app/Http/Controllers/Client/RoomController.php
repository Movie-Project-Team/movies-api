<?php

namespace App\Http\Controllers\Client;

use App\Events\RoomStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\VerifyPasswordRoomRequest;
use App\Http\Resources\Client\RoomResource;
use App\Services\CommonService;
use App\Support\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    public function list(Request $request)
    {
        try {
            $data = CommonService::getModel('Room')->getList();

            return $this->sendResponseApi(['data' => RoomResource::collection($data), 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function create(Request $request) {
        try {
            $roomCode = Str::upper(Str::random(6));
            $roomThumbArray = Config::get('image.room_thumb');
            $thumbUrl = $roomThumbArray[array_rand($roomThumbArray)];

            $data = CommonService::getModel('Room')->create([
                'room_code' => $roomCode,
                'name' => $request->name,
                'host_id' => $request->profileId,
                'is_locked' => $request->isLocked,
                'movie_id' => $request->movieId,
                'password' => $request->password,
                'capacity' => $request->capacity,
                'thumbnail_url' => $thumbUrl
            ]);

            return $this->getDetailData(new RoomResource($data));
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function verify(VerifyPasswordRoomRequest $request)
    {
        try {
            $room = CommonService::getModel('Room')->getDetail($request->roomId);

            if (!$room) {
                return $this->sendResponseApi(['message' => 'Room not found', 'code' => 404]);
            }

            return $this->getDetailData(new RoomResource($room));
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function closeRoom(Request $request) {
        try {
            $room = CommonService::getModel('Room')->getByParam(['room_code' => $request->roomCode]);

            if (!$room) {
                return $this->sendResponseApi(['message' => 'Room not found', 'code' => 404]);
            }

            $room->update([
                'status' => Constants::ROOM_STATUS_CLOSE
            ]);

            broadcast(new RoomStatusChanged($room))->toOthers();

            return $this->sendResponseApi(['message' => 'Change Status Success']);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function listClose(Request $request, $hostId) {
        try {
            $room = CommonService::getModel('Room')->getListClose($hostId);

            return $this->sendResponseApi(['data' => RoomResource::collection($room), 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }

    public function reOpenRoom(Request $request) {
        try {
            $room = CommonService::getModel('Room')->getByParam([
                'room_code' => $request->roomCode, 
                'host_id' => $request->hostId
            ]);

            if (!$room) {
                return $this->sendResponseApi(['message' => 'Room not found', 'code' => 404]);
            }

            $room->update([
                'status' => Constants::ROOM_STATUS_OPEN
            ]);
            
            broadcast(new RoomStatusChanged($room))->toOthers();

            return $this->sendResponseApi(['message' => 'Change Status Success']);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }
}
