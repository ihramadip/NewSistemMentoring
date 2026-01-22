<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleSheetService
{
    protected $cacheDuration = 1; // in minutes

    protected function fetchAndParse($url, $cacheKey)
    {
        if (!$url) {
            return [];
        }

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($url) {
            try {
                $response = Http::get($url);

                if ($response->failed()) {
                    return []; // Return empty array if request fails
                }

                $csvData = $response->body();
                $lines = explode(PHP_EOL, $csvData);
                $header = str_getcsv(array_shift($lines));
                $data = [];

                foreach ($lines as $line) {
                    if (empty(trim($line))) {
                        continue;
                    }
                    $row = str_getcsv($line);
                    if (count($header) == count($row)) {
                        $data[] = array_combine($header, $row);
                    }
                }

                return $data;
            } catch (\Exception $e) {
                // Log the error if needed
                return []; // Return empty on exception
            }
        });
    }

    public function getStats()
    {
        $url = config('googlesheet.sheets.stats');
        return $this->fetchAndParse($url, 'googlesheet.stats');
    }

    public function getPrograms()
    {
        $url = config('googlesheet.sheets.programs');
        return $this->fetchAndParse($url, 'googlesheet.programs');
    }

    public function getDocumentation()
    {
        $url = config('googlesheet.sheets.documentation');
        return $this->fetchAndParse($url, 'googlesheet.documentation');
    }

    public function getBlogPosts()
    {
        $url = config('googlesheet.sheets.blog_posts');
        return $this->fetchAndParse($url, 'googlesheet.blog_posts');
    }

    public function getTestimonials()
    {
        $url = config('googlesheet.sheets.testimonials');
        return $this->fetchAndParse($url, 'googlesheet.testimonials');
    }

    public function getContacts()
    {
        $url = config('googlesheet.sheets.contacts');
        return $this->fetchAndParse($url, 'googlesheet.contacts');
    }
}
