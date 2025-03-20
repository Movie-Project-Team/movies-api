<?php

namespace App\Models;

use App\Support\Constants;

class Movies extends BaseRepository
{
    public function __construct() {
        parent::__construct('Movies');
    }

    public function getList($params)
    {
        $params = array_merge([
            'item' => null,
            'page' => null,
            'keyword' => '',
            'orderBy' => [
                'updated_at' => Constants::ORDER_BY_DESC,
            ],
        ], $params);

        $where = [];
        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $keywords = explode(' ', $keyword);
    
            $where[] = function ($query) use ($keywords, $keyword) {
                $query->where(function ($subQuery) use ($keywords) {
                    foreach ($keywords as $word) {
                        $subQuery->where('title', 'like', "%$word%");
                    }
                })->orWhere(function ($subQuery) use ($keywords) {
                    foreach ($keywords as $word) {
                        $subQuery->where('name', 'like', "%$word%");
                    }
                });

                // $query->orWhereRaw("MATCH(title, name) AGAINST(? IN BOOLEAN MODE)", [$keyword]);
            };
        }

        return $this->getData([
            'type' => 2,
            'where' => $where,
            'item' => $params['item'],
            'page' => $params['page'],
            'orderBy' => $params['orderBy']
        ]);
    }

    public function getDetailBySlug($slug)
    {
        return $this->getData([
            'type' => 1,
            'where' => [
                'slug' => $slug
            ]
        ]);
    }

    public function getRankingByType($type) 
    {
        $validTypes = ['view', 'vote_count', 'updated_at'];
        $orderByField = in_array($type, $validTypes) ? $type : 'view';

        return $this->getData([
            'type' => 2,
            'item' => 5,
            'orderBy' => [
                $orderByField => Constants::ORDER_BY_DESC
            ]
        ]);
    }
}
