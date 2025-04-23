<?php

namespace App\Models;

class Favorite extends BaseRepository
{
    public function __construct() {
        parent::__construct('Favorite');
    }

    public function getList($userId)
    {
        return $this->getData([
            'type' => '2',
            'where' => [
                'profile_id' => $userId
            ]
        ]);
    }

    public function getByMovieAndProfile($movieId, $profileId)
    {
        return $this->getData([
            'type' => '1',
            'where' => [
                'movie_id' => $movieId,
                'profile_id' => $profileId
            ]
        ]);
    }
}
