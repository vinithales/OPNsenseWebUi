<?php

namespace App\Services\Opnsense;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;

class BaseService
{
    protected $client;

    public function __construct()
    {
        $baseUri = env('OPNSENSE_API_BASE_URL');
        $apiKey = env('OPNSENSE_API_KEY');
        $apiSecret = env('OPNSENSE_API_SECRET');
      




        // Log das configurações (remova em produção)
        Log::debug('OPNSense API Configuration', [
            'base_uri' => $baseUri,
            'api_key' => $apiKey,
            'api_secret' => substr($apiSecret, 0, 5) . '...' // Log parcial por segurança
        ]);

        $this->client = new Client([
            'base_uri' => $baseUri,
            'auth' => [$apiKey, $apiSecret],
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
            'allow_redirects' => false,
        ]);
    }
}
