<?php

declare(strict_types=1);

namespace Kreait;

use function GuzzleHttp\choose_handler;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Kreait\GcpMetadata\Error;
use Psr\Http\Message\ResponseInterface;

class GcpMetadata
{
    const baseUrl = 'http://metadata.google.internal/computeMetadata/v1/';
    const flavorHeaderName = 'Metadata-Flavor';
    const flavorHeaderValue = 'Google';

    /**
     * @var ClientInterface|null
     */
    private $client;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client;
    }

    public function isAvailable(): bool
    {
        try {
            $this->instance();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function instance(string $property = '', array $params = [])
    {
        return $this->request('instance', $property, $params);
    }

    public function project(string $property = '', array $params = [])
    {
        return $this->request('project', $property, $params);
    }

    private function request(string $type, string $property = '', array $params = [])
    {
        $property = ltrim($property, '/');

        $url = self::baseUrl.$type.'/'.$property;

        $options = [
            'headers' => [self::flavorHeaderName => self::flavorHeaderValue],
            'query' => $params,
        ];

        try {
            $response = $this->client()->request('GET', $url, $options);

            $this->verifyHttpStatus($response);
            $this->verifyHeaders($response);

            return $this->parseResponse($response);
        } catch (RequestException $e) {
            $message = $e->getMessage();

            if ($response = $e->getResponse()) {
                $message .= ': '.$response->getBody();
            }

            throw new Error($message);
        }
    }

    private function verifyHttpStatus(ResponseInterface $response)
    {
        if (($statusCode = $response->getStatusCode()) !== 200) {
            throw new Error('Unsuccessful response status code: '.$statusCode);
        }
    }

    private function verifyHeaders(ResponseInterface $response)
    {
        if ($response->getHeaderLine(self::flavorHeaderName) !== self::flavorHeaderValue) {
            throw new Error('"'.self::flavorHeaderName.'" header is missing or incorrect.');
        }
    }

    private function parseResponse(ResponseInterface $response)
    {
        $body = trim((string) $response->getBody());
        $lines = explode("\n", $body);

        if (\count($lines) === 1) {
            return $body;
        }

        return $lines;
    }

    private function client(): ClientInterface
    {
        if (!$this->client) {
            $decider = function ($retries) {
                return $retries < 3;
            };

            $stack = new HandlerStack(choose_handler());
            $stack->push(Middleware::redirect(), 'allow_redirects');
            $stack->push(Middleware::retry($decider));

            $this->client = new Client([
                'handler' => $stack,
            ]);
        }

        return $this->client;
    }
}
