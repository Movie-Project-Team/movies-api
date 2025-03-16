<?php

namespace App\Models;

class Genres extends BaseRepository
{
    public function __construct() {
        parent::__construct('Genres');
    }

    public function getList()
    {
        return $this->getData([
            'type' => 3,
        ]);
    }
}
