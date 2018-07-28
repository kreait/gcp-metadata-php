<?php

declare(strict_types=1);

namespace Kreait;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Kreait\GcpMetadata\Error;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GcpMetadataTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var GcpMetadata
     */
    private $metadata;

    protected function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);

        $this->metadata = new GcpMetadata($this->client->reveal());
    }

    /**
     * @test
     */
    public function it_uses_a_default_client()
    {
        $metadata = new GcpMetadata();

        // We cannot unit test this with Gcp being available, but the default client expects it to be
        $this->expectException(Error::class);
        $metadata->instance();
    }

    /**
     * @test
     */
    public function it_is_available()
    {
        $this->client->request(Argument::cetera())->willReturn($this->createResponse());

        $this->assertTrue($this->metadata->isAvailable());
    }

    /**
     * @test
     */
    public function it_is_not_available()
    {
        $this->client->request(Argument::cetera())->willThrow(ConnectException::create(new Request('GET', GcpMetadata::baseUrl)));

        $this->assertFalse($this->metadata->isAvailable());
    }

    /**
     * @test
     */
    public function it_is_requires_certain_http_response_headers()
    {
        $this->client->request(Argument::cetera())->willReturn(new Response(200));

        $this->assertFalse($this->metadata->isAvailable());
    }

    /**
     * @test
     */
    public function it_requires_a_successful_http_response()
    {
        $this->client->request(Argument::cetera())->willReturn($this->createResponse(500, 'details'));

        $this->expectException(Error::class);
        $this->metadata->instance();
    }

    /**
     * @test
     */
    public function it_catches_unexpected_http_errors()
    {
        $request = $this->createMock(RequestInterface::class);

        $responseBody = $this->prophesize(StreamInterface::class);
        $responseBody->__toString()->willReturn('foo');

        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn($responseBody->reveal());

        $e = new RequestException('Foo', $request, $response->reveal());

        $this->client->request(Argument::cetera())->willThrow($e);

        $this->expectException(Error::class);
        $this->metadata->instance();
    }

    /**
     * @test
     * @dataProvider responseStrings
     */
    public function it_parses_http_responses_containing($expectedResult, $responseString)
    {
        $this->client->request(Argument::cetera())->willReturn($this->createResponse(200, $responseString));

        $this->assertSame($expectedResult, $this->metadata->instance('foo'));
        $this->assertSame($expectedResult, $this->metadata->project('foo'));
    }

    public function responseStrings()
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
