<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use Illuminate\Console\Command;
use Psr\Http\Message\ResponseInterface;

class CrawlImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:image {--site=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $site = $this->option('site');
    
        $this->info("========================================");
        $this->info("BẮT ĐẦU QUY TRÌNH CRAWL ẢNH");
        $this->info("========================================\n");
    
        // Lấy slugs
        $this->info("========== BƯỚC 1: Lấy Slug ==========");
        $slugs = $this->getSlugs(10);
    
        // Lấy ảnh từ các slug đã lấy được
        $this->info("========== BƯỚC 2: Lấy Ảnh ==========");
        $this->fetchMovieDetails($slugs);
    
        $this->info("\n========================================");
        $this->info("QUÁ TRÌNH CRAWL ẢNH HOÀN TẤT!");
        $this->info("========================================\n");
    }    

    private function getSlugs($pages)
    {
        $slugs = [];
        $url = config('crawler.movies_url');
    
        $this->info("Đang tiến hành lấy slug");
        $this->output->newLine(1);
        $startTime = microtime(true);

        $progressBar = $this->output->createProgressBar($pages);
        $progressBar->start();
    
        $client = new Client([
            'timeout' => 15.0,
            'connect_timeout' => 10.0,
            'verify' => false,
        ]);
        
        $requests = function ($pages) use ($url, $client) {
            for ($page = 1; $page <= $pages; $page++) {
                yield function () use ($url, $page, $client) {
                    return $client->getAsync($url . $page);
                };
            }
        };
    
        $pool = new Pool($client, $requests($pages), [
            'concurrency' => 100,
            'fulfilled' => function (ResponseInterface $response) use (&$slugs, $progressBar) {
                $movies = json_decode($response->getBody(), true);
    
                if (!empty($movies['items'])) {
                    foreach ($movies['items'] as $movie) {
                        if (!empty($movie['slug'])) {
                            $slugs[] = $movie['slug'];
                        }
                    }
                }

                $progressBar->advance();
            },
            'rejected' => function (RequestException $reason) {
                echo "Request failed: " . $reason->getMessage() . PHP_EOL;
            },
        ]);
    
        $promise = $pool->promise();
        $promise->wait();
    
        $progressBar->finish();
        $this->output->newLine(1);

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->info("Hoàn thành lấy " . count($slugs) . " slugs trong {$executionTime} giây");
        $this->output->newLine(1);
    
        return $slugs;
    }

    private function fetchMovieDetails(array $slugs)
    {
        $client = new Client([
            'timeout' => 15.0,
            'connect_timeout' => 10.0,
            'verify' => false,
        ]);
    
        $url = config('crawler.detail_url');
    
        $this->info("Đang tiến hành lấy ảnh");
        $this->output->newLine(1);
        $startTime = microtime(true);

        $progressBar = $this->output->createProgressBar(count($slugs));
        $progressBar->start();

        $requests = function ($slugs) use ($url, $client) {
            foreach ($slugs as $slug) {
                yield function () use ($url, $slug, $client) {
                    return $client->getAsync($url . '/' . $slug);
                };
            }
        };
    
        $imageUrls = [];
    
        $pool = new Pool($client, $requests($slugs), [
            'concurrency' => 50,
            'fulfilled' => function (ResponseInterface $response) use (&$imageUrls, $progressBar) {
                $data = json_decode($response->getBody(), true);
                if (!empty($data)) {
                    // Lưu link ảnh
                    if (!empty($data['movie']['thumb_url'])) {
                        $imageUrls[] = [
                            'slug' => $data['movie']['slug'],
                            'url' => $data['movie']['thumb_url'],
                            'type' => 'thumb'
                        ];
                    }
                    if (!empty($data['movie']['poster_url'])) {
                        $imageUrls[] = [
                            'slug' => $data['movie']['slug'],
                            'url' => $data['movie']['poster_url'],
                            'type' => 'poster'
                        ];
                    }
                }
                $progressBar->advance();
            },
            'rejected' => function (RequestException $reason) {
                echo "Request failed: " . $reason->getMessage() . PHP_EOL;
            },
        ]);
    
        $promise = $pool->promise();
        $promise->wait();
    
        
        $progressBar->finish();
        $this->output->newLine(1);

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->info("Hoàn thành lấy " . count($imageUrls) . " ảnh trong {$executionTime} giây");
        $this->output->newLine(1);

        file_put_contents(storage_path('app/image_urls.json'), json_encode($imageUrls, JSON_PRETTY_PRINT));
    
        $this->info("========== BƯỚC 3: Download Ảnh ==========");
        $this->downloadImages($imageUrls);
    }    

    private function downloadImages(array $imageUrls)
    {
        $client = new Client([
            'timeout' => 60.0,
            'connect_timeout' => 20.0,
            'verify' => false,
        ]);

        $this->info("Đang tiến hành download ảnh");
        $this->output->newLine(1);
        $startTime = microtime(true);

        $progressBar = $this->output->createProgressBar(count($imageUrls));
        $progressBar->start();
    
        $storagePath = storage_path('app/images');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
    
        $requests = function () use ($imageUrls, $client, $storagePath, $progressBar) {
            foreach ($imageUrls as $image) {
                $slug = $image['slug'];
                $url = $image['url'];
                $type = $image['type'];
                $filePath = "$storagePath/{$slug}_{$type}.jpg";
    
                if (file_exists($filePath)) {
                    continue;
                }
    
                yield function () use ($client, $url, $filePath) {
                    return $client->getAsync($url)->then(
                        function (ResponseInterface $response) use ($filePath) {
                            file_put_contents($filePath, $response->getBody());
                        }
                    );
                };

                $progressBar->advance();
            }
        };
    
        $pool = new Pool($client, $requests(), [
            'concurrency' => 20,
            'rejected' => function ($reason) {
                echo "Image download failed: " . $reason->getMessage() . PHP_EOL;
            },
        ]);
    
        $promise = $pool->promise();
        $promise->wait();

        
        $progressBar->finish();
        $this->output->newLine(1);

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        $this->info("Hoàn thành download " . count($imageUrls) . " ảnh trong {$executionTime} giây");
        $this->output->newLine(1);
    }    
}
