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
    // Use getenv to avoid static analysis warnings; defaults to false
    $httpDebug = filter_var(getenv('OPNSENSE_HTTP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN);

        // Optional: request/response logging (no headers to avoid leaking secrets)
        $stack = HandlerStack::create();
        if ($httpDebug) {
            $stack->push(Middleware::log(
                // Use Laravel's logger (PSR-3)
                Log::channel('stack'),
                new MessageFormatter('{method} {uri} - ReqBody: {req_body} | Status: {code} | ResBody: {res_body}')
            ));
        }

        $this->client = new Client([
            'base_uri' => $baseUri,
            'auth' => [$apiKey, $apiSecret],
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json',
            ],
            'http_errors' => false,
            'allow_redirects' => false,
            'handler' => $stack,
        ]);
    }
}
