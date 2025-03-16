<?php

namespace App\Models;

use App\Support\Constants;

class CrawlMovieLog extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('CrawlMovieLog');
    }

    public function getList()
    {
        return $this->getData([
            'type' => 3,
            'select' => ['date', 'total_movies', 'success', 'failed', 'success_rate'],
        ]);
    }
}
