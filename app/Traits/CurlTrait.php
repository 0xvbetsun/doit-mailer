<?php
declare(strict_types=1);

namespace App\Traits;

/**
 * Trait CurlTrait
 * @package App\Traits
 */
trait CurlTrait
{
    /**
     * @param $url
     * @return mixed
     */
    private function sendGetRequest($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36'
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}