<?php

namespace App\Models;

use App\Services\CommonService;
use App\Support\Constants;
use Illuminate\Support\Arr;

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
        $whereHas = [
            [
                'episodes',
                function ($query) {
                    $query->whereHas('episodeServers');
                }
            ],
        ];

        $with = [
            'episodes.servers',
        ];

        return $this->getData([
            'type' => 1,
            'where' => [
                'slug' => $slug
            ],
            'whereHas' => $whereHas,
            'with' => $with
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

    public function getListWatchHistory($profileId, $movieDuration = null)
    {
        $whereHas = [
            [
                'watchHistories',
                function ($query) use ($profileId) {
                    $query->where('profile_id', $profileId)
                        ->where('time_process', '>', 0);;
                }
            ]
        ];

        $where = [];
        if (!empty($movieDuration)) {
            $threshold = 30; 
            $where['time_process'] = ['<' , $movieDuration - $threshold];
        }

        return $this->getData([
            'type' => 2,
            'item' => 5,
            'where' => $where,
            'whereHas' => $whereHas,
            'orderBy' => [
                'updated_at' => Constants::ORDER_BY_DESC
            ]
        ]);
    }

    public function getAllExceptMovieId($id)
    {
        return $this->getData([
            'type' => 2,
            'item' => 100,
            'where' => [
                'id' => ['!=' , $id]
            ]
        ]);
    }

    public function getByIds($ids)
    {
        return $this->getData([
            'type' => '3',
            'where' => [
                'id' => Arr::wrap($ids)
            ]
        ]);
    }

    public function recommendList($id) 
    {
        $movie = CommonService::getModel('Movies')->getDetail($id);
        $otherMovies = $this->getAllExceptMovieId($id);

        $corpus = [$movie->description];
        $movieIds = [];

        foreach ($otherMovies->items() as $other) {
            $corpus[] = $other->description;
            $movieIds[] = $other->id;
        }

        // Sử dụng TokenCountVectorizer để chuyển đổi văn bản thành vector đếm từ
        $vectorizer = new \Phpml\FeatureExtraction\TokenCountVectorizer(new \Phpml\Tokenization\WhitespaceTokenizer());
        $vectorizer->fit($corpus);
        $vectorizer->transform($corpus);

        // Sử dụng TfIdfTransformer để chuyển vector đếm thành TF-IDF
        $transformer = new \Phpml\FeatureExtraction\TfIdfTransformer($corpus);
        $transformer->transform($corpus);

        // Lấy vector của phim hiện tại
        $currentVector = $corpus[0];

        // Tính cosine similarity giữa phim hiện tại và các phim còn lại của trang
        $similarities = [];
        foreach ($corpus as $index => $vector) {
            if ($index === 0) continue; // bỏ qua phim hiện tại
            $sim = $this->cosineSimilarity($currentVector, $vector);
            $similarities[] = [
                'movie_id'   => $movieIds[$index - 1],
                'similarity' => $sim,
            ];
        }

        // Sắp xếp theo độ tương đồng giảm dần
        usort($similarities, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Lấy top 10 phim có độ tương đồng cao nhất từ trang hiện tại
        $recommendedIds = array_slice(array_column($similarities, 'movie_id'), 0, 10);
        $recommendations = $this->getByIds($recommendedIds);

        // Thêm thông tin phân trang nếu cần
        return $recommendations;
    }

    private function cosineSimilarity(array $vec1, array $vec2)
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        foreach ($vec1 as $i => $value) {
            $dot += $value * $vec2[$i];
            $normA += $value * $value;
            $normB += $vec2[$i] * $vec2[$i];
        }
        if ($normA == 0 || $normB == 0) {
            return 0;
        }
        return $dot / (sqrt($normA) * sqrt($normB));
    }
}
