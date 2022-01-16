<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use TasmotaHttpClient\Request;
use TasmotaHttpClient\UnknownCommandException;

class RequestTest extends PHPUnit\Framework\TestCase
{
    public function testClientMethodGetWasCalled(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{}')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sut = new Request();
        $sut->setClient($client);

        $result = $sut->send('http://tasmota.local');

        $this->assertSame([], $result);
    }

    public function testJsonDecodeWorks(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"key": "value"}')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sut = new Request();
        $sut->setClient($client);

        $result = $sut->send('http://tasmota.local');

        $this->assertSame(['key' => 'value'], $result);
    }

    public function testJsonDecodeThrowAnExceptionIfJsonIsBroken(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"key: "value"}')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sut = new Request();
        $sut->setClient($client);

        $this->expectException(InvalidArgumentException::class);
        $sut->send('http://tasmota.local');
    }

    public function testUnknownCommandExceptionIsThrown(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{"Command": "Unknown"}')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $sut = new Request();
        $sut->setClient($client);

        $this->expectException(UnknownCommandException::class);
        $sut->send('http://tasmota.local');
    }
}
