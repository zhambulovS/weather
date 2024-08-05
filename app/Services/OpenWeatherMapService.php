<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenWeatherMapService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENWEATHERMAP_API_KEY');
    }

    public function getWeatherByCity($city)
    {
        try {
            $response = $this->client->get("https://api.openweathermap.org/data/2.5/weather", [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ]
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                if ($statusCode == 404) {
                    return ['cod' => 404, 'message' => 'City not found'];
                }
            }
            return ['cod' => 500, 'message' => 'An error occurred'];
        }
    }

    public function getWeatherByCityForHour($city)
    {
        try {
            $response = $this->client->get("https://api.openweathermap.org/data/2.5/forecast", [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ]
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                if ($statusCode == 404) {
                    return ['cod' => 404, 'message' => 'City not found'];
                }
            }
            return ['cod' => 500, 'message' => 'An error occurred'];
        }
    }
}
