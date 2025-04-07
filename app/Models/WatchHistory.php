<?php

namespace App\Models;

use App\Support\Constants;

class WatchHistory extends BaseRepository
{
    public function __construct() {
        parent::__construct('WatchHistory');
    }

    public function getList($profileId)
    {
        $where = [
            'profile_id' => $profileId,
            'time_process' => ['>' , 0]
        ];

        return $this->getData([
            'type' => 2,
            'item' => 5,
            'where' => $where,
            'orderBy' => [
                'updated_at' => Constants::ORDER_BY_DESC
            ]
        ]);
    }

    public function getByProfileId($profileId, $movieId) 
    {
        return $this->getData([
            'type' => 1,
            'where' => [
                'profile_id' => $profileId,
                'movie_id' => $movieId 
            ],
        ]);
    }
}
