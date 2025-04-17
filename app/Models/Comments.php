<?php

namespace App\Models;

use App\Support\Constants;

class Comments extends BaseRepository
{
    public function __construct() {
        parent::__construct('Comments');
    }

    public function getList($movieId)
    {
        return $this->getData([
            'type' => 3,
            'select' => ['*'],
            'with' => ['replies'],
            'where' => [
                'movie_id' => $movieId,
                'parent_id' => null
            ],
            'orderBy' => [
                'created_at' => Constants::ORDER_BY_ASC
            ],
        ]);
    }

    public function getDetailById($id)
    {
        return $this->getData([
            'type' => 1,
            'where' => [
                'id' => $id
            ]
        ]);
    }
}
