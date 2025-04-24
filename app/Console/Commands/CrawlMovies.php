<?php

namespace App\Console\Commands;

use App\Exports\CrawlerExport;
use App\Jobs\CrawlMovieJob;
use App\Models\CrawlMovieLog;
use App\Services\CrawlerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use App\Services\CommonService;
use Carbon\Carbon;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Throwable;

class CrawlMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawl-movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl movie data from API and store it in the database';

    /**
     * Execute the console command.
     */

    private int $successCount = 0;
    private int $failedCount = 0;

    private $logger;

    public function handle()
    {
        $client = new Client([
            'timeout' => 60.0,
            'connect_timeout' => 15.0,
            'verify' => false,
        ]);
        $baseUrl = config('crawler.movies_url');
        $detailUrl = config('crawler.detail_url');
        $pages = 1214;
        $batchSize = 200;
        DB::connection()->disableQueryLog();
        $this->info("Memory usage: " . round(memory_get_usage()/1024/1024, 2) . "MB");
        $this->info('Starting movie data crawl...');
        $slugs = $this->getSlugs($client, $pages, $baseUrl);

        $startTime = microtime(true);
        foreach (array_chunk($slugs, $batchSize) as $i => $chunk) {
            $this->getData($client, $chunk, $detailUrl);

            unset($chunk);
            if ($i % 5 === 0) gc_collect_cycles();
            
            $this->info("Memory usage [FINAL]: " . round(memory_get_usage()/1024/1024, 2) . "MB");
            $this->info("Processed batch " . ($i + 1));
        }

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->info("Hoàn thành lấy " . count($slugs) . " phim trong {$executionTime} giây");

        // $this->successCount = Cache::get('successful_jobs_count', 0);
        // $successRate = ($totalMovies > 0) ? ($this->successCount / $totalMovies) * 100 : 0;

        // CommonService::getModel('CrawlMovieLog')->upsert([
        //     'date' => Carbon::now(),
        // ], [
        //     'date' => Carbon::now(),
        //     'total_movies' => $totalMovies,
        //     'success' => $this->successCount,
        //     'failed' => $this->failedCount,
        //     'success_rate' => $successRate
        // ]);

        // $this->info('Crawling process completed.');

        // $fileName = 'crawl_movie_log_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        // Excel::store(new CrawlerExport(app(CrawlMovieLog::class)), $fileName, 'local');

        // $this->info("Exported Excel report: $fileName");
    }

    /**
     * Lấy danh sách slug từ nhiều trang
     */
    private function getSlugs($client, $pages, $baseUrl)
    {
        $slugs = [];
        $this->info("Đang tiến hành lấy slug");
        $startTime = microtime(true);
        
        $requests = function ($pages) use ($baseUrl, $client) {
            for ($page = 1; $page <= $pages; $page++) {
                yield function () use ($baseUrl, $page, $client) {
                    return $client->getAsync($baseUrl . $page);
                };
            }
        };
    
        $pool = new Pool($client, $requests($pages), [
            'concurrency' => 100,
            'fulfilled' => function (ResponseInterface $response) use (&$slugs) {
                $movies = json_decode($response->getBody(), true);
                if (!empty($movies['items'])) {
                    $pageSlugs = array_column($movies['items'], 'slug');
                    $slugs = array_merge($slugs, array_filter($pageSlugs));
                    unset($pageSlugs); 
                }
                unset($movies);
            },
            'rejected' => function (Throwable $reason) {
                // Log or handle all kinds of exceptions
                echo "Slug request failed: " . $reason->getMessage() . PHP_EOL;
            },
        ]);
    
        $promise = $pool->promise();
        $promise->wait();
    
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->info("Hoàn thành lấy " . count($slugs) . " slugs trong {$executionTime} giây");
    
        return $slugs;
    }

    private function initLogger()
    {
        $datetime = now()->format('Y-m-d');
        $logPath = storage_path("logs/crawlers/crawler-{$datetime}.log");

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

    private function getData($client, $slugs, $detailUrl)
    {
        $this->logger = $this->initLogger();
        $startTime = microtime(true);
        $moviesBatch = [];
        $serversBatch = [];
        $episodesBatch = [];
        $serverEpisodesBatch = [];

        $requests = function() use ($slugs, $client, $detailUrl) {
            foreach ($slugs as $slug) {
                yield function() use ($client, $detailUrl, $slug) {
                    return $client->getAsync($detailUrl . $slug);
                };
            }
        };

        $pool = new Pool($client, $requests(), [
            'concurrency' => 75,
            'fulfilled'   => function (ResponseInterface $res) use (&$moviesBatch, &$serversBatch, &$episodesBatch, &$serverEpisodesBatch){
                $body = json_decode($res->getBody(), true);
                $movie = $body['movie'];
                
                $moviesBatch[] = [
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

                $server = $body['episodes'][0] ?? [];
                $serversBatch[] = [
                    'name' => $server['server_name'],
                    'kind' => $server['server_name']
                ];

                foreach ($server['server_data'] as $episode) {
                    $episodesBatch[] = [
                        'slug'     => $episode['slug'],
                        'movie_id' => null,
                        'movie_slug' => $movie['slug'],
                        'title'    => $episode['filename'],
                    ];
                    $serverEpisodesBatch[] = [
                        'slug' => $episode['slug'],
                        'name' => $episode['name'],
                        'server_name' => $server['server_name'],
                        'filename' => $episode['filename'],
                        'link_download' => $episode['link_m3u8'],
                        'link_watch' => $episode['link_embed'],
                    ];


                }

                if (count($moviesBatch) >= 100) {
                    $this->insertData($moviesBatch, $serversBatch, $episodesBatch, $serverEpisodesBatch);
                    $moviesBatch = $serversBatch = $episodesBatch = $serverEpisodesBatch = [];
                }
            },
            'rejected' => function (Throwable $reason) {
                echo "Detail request failed: " . $reason->getMessage() . PHP_EOL;
            },
        ]);

        $pool->promise()->wait();
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->info("Hoàn thành thêm " . count($slugs) . " phim trong {$executionTime} giây");
    }

    private function insertData(array $moviesBatch, array $serversBatch, array $episodesBatch, array $serverEpisodesBatch)
    {
        DB::transaction(function () use ($moviesBatch, $serversBatch, $episodesBatch, $serverEpisodesBatch) {
            foreach (array_chunk($moviesBatch, 500) as $chunk) {
                CommonService::getModel('Movies')->upsertBulk($chunk, ['slug']);
                unset($chunk);
                gc_collect_cycles();
            }
            foreach (array_chunk($serversBatch, 500) as $chunk) {
                CommonService::getModel('Server')->upsertBulk($chunk, ['name']);
                unset($chunk);
                gc_collect_cycles();
            }

            $slugs = array_column($moviesBatch, 'slug');
            $movieMap = DB::table('movies')
                ->whereIn('slug', $slugs)
                ->pluck('id', 'slug')
                ->all();

            $serverNames = array_column($serversBatch, 'name');
            $serverMap = DB::table('server')
                ->whereIn('name', $serverNames)
                ->pluck('id', 'name')
                ->all();

            $filteredEpisodes = [];
            foreach ($episodesBatch as $ep) {
                if (isset($movieMap[$ep['movie_slug']])) {
                    $ep['movie_id'] = $movieMap[$ep['movie_slug']];
                    unset($ep['movie_slug']);
                    $filteredEpisodes[] = $ep;
                }
            }
            foreach (array_chunk($filteredEpisodes, 500) as $chunk) {
                CommonService::getModel('Episodes')->upsertBulk($chunk, ['title']);
                unset($chunk);
                gc_collect_cycles();
            }

            $episodesTitle = array_column($episodesBatch, 'title');
            $episodeMap = DB::table('episodes')
                ->whereIn('title', $episodesTitle)
                ->pluck('id', 'title')
                ->all();
            // $episodeMap = DB::table('episodes')->pluck('id', 'title')->toArray();
            $filteredServerEpisodes = [];
            foreach ($serverEpisodesBatch as $serverEp) {
                if (isset($serverMap[$serverEp['server_name']]) && isset($episodeMap[$serverEp['filename']])) {
                    $serverEp['server_id'] = $serverMap[$serverEp['server_name']];
                    $serverEp['episode_id'] = $episodeMap[$serverEp['filename']];
                    unset($serverEp['server_name']);
                    $filteredServerEpisodes[] = $serverEp;
                } else {
                    $this->logger->error("Không tìm thấy server or episode for: {$serverEp['server_name']} / {$serverEp['filename']}");
                }
            }
            foreach (array_chunk($filteredServerEpisodes, 500) as $chunk) {
                CommonService::getModel('ServerEpisode')->upsertBulk($chunk, ['filename']);
                unset($chunk);
                gc_collect_cycles();
            }

            $moviesBatch = $serversBatch = $episodesBatch = $serverEpisodesBatch = [];
        });
        gc_collect_cycles();
    }
}