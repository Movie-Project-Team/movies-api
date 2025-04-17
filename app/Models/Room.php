<?php

namespace App\Models;

use App\Support\Constants;

class Room extends BaseRepository
{
    public function __construct() {
        parent::__construct('Rooms');
    }

    public function getList()
    {
        $where = [];
        $where[] = function ($query) {
            return $query->whereNotNull('movie_id')
                ->where('status', '!=' , Constants::ROOM_STATUS_CLOSE);
        };

        return $this->getData([
            'type' => 2,
            'where' => $where
        ]);
    }

    public function getByParam(array $params = [])
    {
        $where = [];
    
        foreach ($params as $key => $value) {
            $where[$key] = $value;
        }
    
        return $this->getData([
            'type' => 1,
            'where' => $where,
        ]);
    }

    public function getListClose($hostId)
    {
        return $this->getData([
            'type' => 2,
            'where' => [
                'status' => Constants::ROOM_STATUS_CLOSE,
                'host_id' => $hostId
            ]
        ]);
    }
}
