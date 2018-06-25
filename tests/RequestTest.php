<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use TasmotaHttpClient\Request;
use TasmotaHttpClient\UnknownCommandException;

class RequestTest extends PHPUnit\Framework\TestCase
{
    public function testClientMethodGetWasCalled(): void
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('get'),
                $this->equalTo(['http://tasmota.local', []])
            )
            ->willReturn(new Response(200, [], '{}'))
        ;

        $sut = new Request();
        $sut->setClient($client);

        $result = $sut->send('http://tasmota.local');

        $this->assertSame([], $result);
    }

    public function testJsonDecodeWorks(): void
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('get'),
                $this->equalTo(['http://tasmota.local', []])
            )
            ->willReturn(new Response(200, [], '{"key": "value"}'))
        ;

        $sut = new Request();
        $sut->setClient($client);

        $result = $sut->send('http://tasmota.local');

        $this->assertSame(['key' => 'value'], $result);
    }

    public function testJsonDecodeThrowAnExceptionIfJsonIsBroken(): void
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('get'),
                $this->equalTo(['http://tasmota.local', []])
            )
            ->willReturn(new Response(200, [], '{"key: "value"}'))
        ;

        $sut = new Request();
        $sut->setClient($client);

        $this->expectException(InvalidArgumentException::class);
        $sut->send('http://tasmota.local');
    }

    public function testUnknownCommandExceptionIsThrown(): void
    {
        $client = $this->createMock(Client::class);
        $client
            ->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('get'),
                $this->equalTo(['http://tasmota.local', []])
            )
            ->willReturn(new Response(200, [], '{"Command": "Unknown"}'))
        ;

        $sut = new Request();
        $sut->setClient($client);

        $this->expectException(UnknownCommandException::class);
        $sut->send('http://tasmota.local');
    }

}
