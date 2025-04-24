<?php

namespace App\Jobs;

use App\Services\CommonService;
use App\Services\CrawlerService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;

class CrawlMovieJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public $slug;

    public $tries = 2;
    public $backoff = 5;

    protected $logger;

    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    protected function initLogger()
    {
        $datetime = now()->format('Y-m-d');
        $logPath = storage_path("logs/crawlers/crawler-{$datetime}.log");

        // Tạo thư mục nếu chưa có
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $output = "[%datetime%] %level_name%: %message%\n";
        $formatter = new LineFormatter($output, null, true, true);

        $logger = new Logger('crawler');
        $stream = new StreamHandler($logPath, Level::Debug);
        $stream->setFormatter($formatter);
        $logger->pushHandler($stream);

        return $logger;
    }

    public function handle()
    {
        $this->logger = $this->initLogger();

        $this->logger->info("==================== BẮT ĐẦU CRAWL PHIM ====================");
        $this->logger->info("[Bắt đầu crawl] Slug: {$this->slug}");

        $detailUrl = config('crawler.detail_url');

        try {
            $movieData = CrawlerService::getDataFromUrl($detailUrl . $this->slug, false);
        } catch (\Exception $e) {
            $this->logger->error("[Dữ liệu phim không hợp lệ] Slug: {$this->slug} | " . $e->getMessage());
            $this->logger->info("==================== KẾT THÚC PHIM ====================\n");
            return;
        }

        if (empty($movieData['movie'])) {
            $this->logger->warning("[Không có dữ liệu] Slug: {$this->slug}");
            $this->logger->info("==================== KẾT THÚC PHIM ====================\n");
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
            $this->logger->error("[Dữ liệu phim không hợp lệ] Slug: {$this->slug}");
            $this->logger->info("==================== KẾT THÚC PHIM ====================\n");
            return;
        }

        $movie_instance = CommonService::getModel('Movies')->upsert([
            'slug' => $movie['slug']
        ], $data);

        $this->logger->info("[Phim đã lưu] Slug: {$this->slug} | Tên phim: {$movie['name']}");

        $server = $movieData['episodes'] ?? [];
        if (empty($server)) {
            $this->logger->warning("[Không có server episodes] Slug: {$this->slug}");
            $this->logger->info("==================== KẾT THÚC PHIM ====================\n");
            return;
        }

        $dataS = [
            'name' => $server[0]['server_name'],
            'kind' => "Vietsub"
        ];

        $server_instance = CommonService::getModel('Server')->upsert([
            'name' => $server[0]['server_name']
        ], $dataS);

        $episodes = $server[0]['server_data'];

        for ($i = 0; $i < count($episodes); $i++) {
            $dataE = [
                'movie_id' => $movie_instance['id'],
                'title' => $episodes['filename'],
                'slug' => $episodes['slug'],
            ];

            $episode_instance = CommonService::getModel('Episodes')->upsert([
                'title' => $episodes['filename']
            ], $dataE);

            $dataSE = [
                'episodes_id' => $episode_instance['id'],
                'server_id' => $server_instance['id'],
                'name' => $episodes['name'],
                'slug' => $episodes['slug'],
                'filename' => $episodes['filename'],
                'link_download' => $episodes['link_m3u8'],
                'link_watch' => $episodes['link_embed']
            ];

            CommonService::getModel('ServerEpisode')->upsert([
                'filename' => $episodes['filename']
            ], $dataSE);
        }

        $this->logger->info("==================== BẮT ĐẦU CRAWL GENRE ====================");
        foreach ($movie['category'] as $genre) {
            $genre_instance = CommonService::getModel(modelName: 'Genres')->createOrPass([
                'slug' => $genre['slug']
            ], ['title' => $genre['name'], 'slug' => $genre['slug']]);
            CommonService::getModel(modelName: 'MovieGenre')->create([
                'genre_id' => $genre_instance['id'],
                'movie_id' => $movie_instance['id']
            ]);
        }

        $this->logger->info("==================== BẮT ĐẦU CRAWL LANGUAGE ====================");
        foreach ($movie['country'] as $lang) {
            $lang_instance = CommonService::getModel(modelName: 'Languages')->createOrPass([
                'slug' => $lang['slug']
            ], ['title' => $lang['name'], 'slug' => $lang['slug']]);
            CommonService::getModel(modelName: 'MovieLanguage')->create([
                'language_id' => $lang_instance['id'],
                'movie_id' => $movie_instance['id']
            ]);
        }
    }
}
