<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CommonService;

class GenresController extends Controller
{
    /**
     * Get List Genres
     */
    public function getListGenres()
    {
        try {
            $genres = CommonService::getModel('Genres')->getList();
            return $this->sendResponseApi(['data' => $genres, 'code' => 200]);
        } catch (\Exception $e) {
            return $this->sendResponseApi(['error' => $e->getMessage(), 'code' => 500]);
        }
    }
}
