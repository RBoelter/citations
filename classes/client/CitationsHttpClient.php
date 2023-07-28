<?php

namespace APP\plugins\generic\citations\classes\client;

use APP\core\Application;
use GuzzleHttp\Exception\GuzzleException;

class CitationsHttpClient
{
    public static function get(string $url, string $type = "text/xml"): string
    {
        $data = '';
        $httpClient = Application::get()->getHttpClient();
        try {
            $response = $httpClient->request(
                'GET',
                $url,
                [
                    'headers' => [
                        'Accept' => $type,
                        'Content-Type' => $type,
                        'Cache-Control' => 'no-cache',
                    ],
                ]
            );
            if ($response->getStatusCode() == 200) {
                $data = $response->getBody()->getContents();
            }
        } catch (GuzzleException $e) {
            error_log($e->getMessage()); // TODO: remove
        }

        return $data;
    }
}
