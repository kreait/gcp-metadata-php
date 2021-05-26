<?php

declare(strict_types=1);

namespace Kreait;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Kreait\GcpMetadata\Error;
use PHPUnit\Framework\TestCase;

class GcpMetadataTest extends TestCase
{
    private $client;
    private $metadata;

    protected function setUp(): void
    {
        $this->client = $this->createMock(ClientInterface::class);

        $this->metadata = new GcpMetadata($this->client);
    }

    /**
     * @test
     */
    public function it_uses_a_default_client(): void
    {
        $metadata = new GcpMetadata();

        // We cannot unit test this with Gcp being available, but the default client expects it to be
        $this->expectException(Error::class);
        $metadata->instance();
    }

    /**
     * @test
     */
    public function it_is_available(): void
    {
        $this->client->method('request')->willReturn($this->createResponse());

        $this->assertTrue($this->metadata->isAvailable());
    }

    /**
     * @test
     */
    public function it_is_not_available(): void
    {
        $this->client->method('request')
            ->willThrowException(new ConnectException('Connection refused', new Request('GET', GcpMetadata::baseUrl)));

        $this->assertFalse($this->metadata->isAvailable());
    }

    /**
     * @test
     */
    public function it_is_requires_certain_http_response_headers(): void
    {
        $this->client->method('request')->willReturn(new Response(200));

        $this->assertFalse($this->metadata->isAvailable());
    }

    /**
     * @test
     */
    public function it_requires_a_successful_http_response(): void
    {
        $this->client->method('request')->willReturn($this->createResponse(500, 'details'));

        $this->expectException(Error::class);
        $this->metadata->instance();
    }

    /**
     * @test
     * @dataProvider responseStrings
     */
    public function it_parses_http_responses_containing($expectedResult, $responseString): void
    {
        $this->client->method('request')->willReturn($this->createResponse(200, $responseString));

        $this->assertSame($expectedResult, $this->metadata->instance('foo'));
        $this->assertSame($expectedResult, $this->metadata->project('foo'));
    }

    /**
     * @test
     */
    public function it_caches_its_results(): void
    {
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->createResponse());

        $this->metadata->isAvailable();
        $this->metadata->isAvailable();
    }

    public function responseStrings(): array
    {
        return [
            'an empty body' => ['', null],
            'a single line' => ['foo', 'foo'],
            'multiple lines' => [['foo', 'bar'], "foo\nbar"],
        ];
    }

    private function createResponse(int $status = 200, $body = ''): Response
    {
        $headers = [GcpMetadata::flavorHeaderName => GcpMetadata::flavorHeaderValue];

        return new Response($status, $headers, $body);
    }
}
