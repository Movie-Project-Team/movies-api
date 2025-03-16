<?php
namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService
{
    /**
     * Lấy Data HTML hoặc XML từ URL hoặc JSON
     *
     * @param string $url
     * @param bool $isDom
     * @return mixed
     */
    public static function getDataFromUrl(string $url, bool $isDom = true)
    {
        $client = new Client();
        $response = $client->request('GET', $url, [
            'headers' => [
                'Accept' => $isDom ? 'text/html' : 'application/json',
            ],
            'verify' => false
        ]);
        if ($isDom) {
            $htmlContent = $response->getBody()->getContents();
            return new Crawler($htmlContent);
        }

        $contentType = $response->getHeaderLine('Content-Type');
        if (strpos($contentType, 'image') !== false) {
            return $response->getBody()->getContents();
        }

        $jsonData = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
        }

        return $jsonData;
    }

    /**
     * Kiểm tra xem URL có bị chặn bởi robots.txt không
     *
     * @param string $url
     * @return bool
     */
    public static function isBlockedByRobotsTxt(string $url): bool
    {
        $parsedUrl = parse_url($url);
        $robotsUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}/robots.txt";

        try {
            $client = new Client();
            $response = $client->request('GET', $robotsUrl);
            if ($response->getStatusCode() !== 200) {
                return false;
            }

            $robotsContent = $response->getBody()->getContents();
            return str_contains($robotsContent, "Disallow: /");
        } catch (\Exception $e) {
            return false;
        }
    }

     /**
     * Lấy thuộc tính từ các selector (ví dụ: src, href)
     *
     * @param string $url
     * @param string $selector
     * @param string $attribute
     * @return array
     */
    public static function getAttributesFromSelector(string $url, string $selector, string $attribute): array
    {
        $crawler = self::getDataFromUrl($url, true);
        return $crawler->filter($selector)->each(fn(Crawler $node) => $node->attr($attribute));
    }

    /**
     * Tìm kiếm văn bản cụ thể trong HTML
     *
     * @param string $url
     * @param string $text
     * @return bool
     */
    public static function searchTextInHtml(string $url, string $text): bool
    {
        $crawler = self::getDataFromUrl($url, true);
        return str_contains($crawler->text(), $text);
    }

    /**
     * Lấy meta tags từ trang
     *
     * @param string $url
     * @return array
     */
    public static function getMetaTags(string $url): array
    {
        $crawler = self::getDataFromUrl($url, true);
        $metaTags = $crawler->filter('meta');
        return $metaTags->each(function (Crawler $node) {
            return [
                'name' => $node->attr('name'),
                'content' => $node->attr('content'),
                'property' => $node->attr('property'),
            ];
        });
    }
}
