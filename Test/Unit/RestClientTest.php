<?php declare(strict_types=1);

namespace MaxServ\RestClient\Test\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MaxServ\RestClient\Exception\JsonSchemaValidationException;
use MaxServ\RestClient\RestClient;
use MaxServ\RestClient\Validator\JsonSchemaValidator;
use PHPUnit\Framework\TestCase;

class RestClientTest extends TestCase
{
    public function testBasicInstantion()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"foo":"bar"}'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client([
            'base_uri' => 'http://example.org',
            'handler' => $handlerStack
        ]);

        $restClient = new RestClient($client, 'foobar');
        $this->assertInstanceOf(RestClient::class, $restClient);

        $response = $restClient->post('/foobar', []);
        $this->assertNotEmpty($response);
        $this->assertEquals('{"foo":"bar"}', (string)$response->getBody());
    }

    public function testBasicInstantionWithJsonSchema()
    {
        $client = new Client(['base_uri' => 'http://example.org',]);
        $restClient = new RestClient($client, 'foobar', [new JsonSchemaValidator()]);

        $this->expectException(ClientException::class);

        $data = [
            'review' => [
                'title' => 'Foobar',
                'detail' => 'Foobar',
                'nickname' => 'Foobar'
            ]
        ];

        $restClient->post('/foobar', $data);
    }

    public function testBasicInstantionWithWrongJsonSchema()
    {
        $client = new Client(['base_uri' => 'http://example.org',]);
        $restClient = new RestClient($client, 'foobar', [new JsonSchemaValidator()]);

        $this->expectException(JsonSchemaValidationException::class);

        $data = [
            'review' => [
                'title' => 'Foobar'
            ]
        ];

        $restClient->post('/foobar', $data);
    }
}
