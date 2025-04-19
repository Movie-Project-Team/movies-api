<?php

namespace App\Jobs;

use App\Services\CommonService;
use App\Services\CrawlerService;
use GuzzleHttp\Client;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CrawlMovieJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    public $slug;

    /**
     * Số lần retry nếu job thất bại
     */
    public $tries = 2;

    /**
     * Thời gian delay trước khi retry (giây)
     */
    public $backoff = 5;

    /**
     * Tạo job mới
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Xử lý công việc crawl
     */
    public function handle()
    {
        Log::info("Bắt đầu crawl phim: {$this->slug}");

        $detailUrl = config('crawler.detail_url');       
        
        try {
            $movieData = CrawlerService::getDataFromUrl($detailUrl . $this->slug, false);
        } catch (\Exception $e) {
            Log::error("Lỗi khi lấy dữ liệu phim {$this->slug}: " . $e->getMessage());
            return;
        }

        if (empty($movieData['movie'])) {
            Log::warning("Không tìm thấy dữ liệu cho phim: {$this->slug}");
            return;
        }

        $movie = $movieData['movie'];

        $data = [
            'title' => $movie['name'] ?? '',
            'name' => $movie['origin_name'] ?? '',
            'slug' => $movie['slug'] ?? '',
            'description' => $movie['content'] ?? '',
            'thumbnail' => $movie['thumb_url'] ?? '',
            'poster' => $movie['poster_url'] ?? '',
            'time' => $movie['time'] ?? '',
            'esp_current' => $movie['episode_current'] ?? '',
            'esp_total' => $movie['episode_total'] ?? '',
            'type' => $movie['type'] ?? '',
            'season' => $movie['tmdb']['season'] ?? '',
            'vote_average' => $movie['tmdb']['vote_average'] ?? null,
            'vote_count' => $movie['tmdb']['vote_count'] ?? null,
            'status' => $movie['status'] ?? '',
            'quality' => $movie['quality'] ?? '',
            'lang' => $movie['lang'] ?? '',
            'year' => $movie['year'] ?? '',
            'view' => $movie['view'] ?? 0,
            'IMDb' => $movie['imdb']['id'] ?? null,
            'trailer' => $movie['trailer_url'] ?? '',
            'produce_by' => isset($movie['director']) ? implode(',', $movie['director']) : '',
        ];

        if (empty($data['title']) || empty($data['slug'])) {
            Log::error("Dữ liệu phim không hợp lệ, bỏ qua: {$this->slug}");
            return;
        }

        $movie_instance = CommonService::getModel('Movies')->upsert([
            'slug' => $movie['slug']
        ], $data);

        $server = $movieData['episodes'];

        $dataS = [
            'name' => $server[0]['server_name'],
            'kind' => "Vietsub"
        ];

        $server_instance = CommonService::getModel('Server')->upsert([
            'name' => $server[0]['server_name']
        ], $dataS);

        $episodes = $server[0]['server_data'];

        // Log::info("hahahaha: " . json_encode($episodes[0]));

        for ($i = 0; $i < count($episodes); $i++) {
            $dataE = [
                'movie_id' => $movie_instance['id'],
                'title' => $episodes[$i]['filename'],
                'slug' => $episodes[$i]['slug'],
            ];

            $episode_instance = CommonService::getModel('Episodes')->upsert([
                'title' => $episodes[$i]['filename']
            ], $dataE);

            $dataSE = [
                'episode_id' => $episode_instance['id'],
                'server_id' => $server_instance['id'],
                'name' => $episodes[$i]['name'],
                'slug' => $episodes[$i]['slug'],
                'filename' => $episodes[$i]['filename'],
                'link_download' => $episodes[$i]['link_m3u8'],
                'link_watch' => $episodes[$i]['link_embed']
            ];

            CommonService::getModel('ServerEpisode')->upsert([
                'filename' => $episodes[$i]['filename']
            ], $dataSE);
        }

        Log::info("Crawl phim thành công: {$this->slug}");
    }
}
