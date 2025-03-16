<?php

namespace App\Models;

use App\Support\Constants;

class Languages extends BaseRepository
{
    public function __construct() {
        parent::__construct('Languages');
    }

    public function getList()
    {
        return $this->getData([
            'type' => 3,
            'orderBy' => [
                'id' => Constants::ORDER_BY_ASC,
            ]
        ]);
    }
}
