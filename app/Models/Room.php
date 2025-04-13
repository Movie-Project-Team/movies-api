<?php

namespace App\Models;

class Room extends BaseRepository
{
    public function __construct() {
        parent::__construct('Rooms');
    }

    public function getList()
    {
        return $this->getData([
            'type' => 2,
            'where' => [
                'status' => ['!=' , 1]
            ]
        ]);
    }
}
