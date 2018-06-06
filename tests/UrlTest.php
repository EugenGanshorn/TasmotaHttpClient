<?php

use TasmotaHttpClient\Url;

class UrlTest extends PHPUnit\Framework\TestCase
{
    public function testUrlContainsIpAddress(): void
    {
        $sut = new Url();
        $sut->setIpAddress('0.0.0.0');

        $this->assertSame('http://0.0.0.0/cm?', $sut->build());
    }

    public function testUrlContainsCommand(): void
    {
        $sut = new Url();
        $sut->setIpAddress('0.0.0.0');

        $this->assertSame('http://0.0.0.0/cm?cmnd=command', $sut->build('command'));
    }

    public function testUrlContainsPayload(): void
    {
        $sut = new Url();
        $sut->setIpAddress('0.0.0.0');

        $this->assertSame('http://0.0.0.0/cm?cmnd=command%2010', $sut->build('command', 10));
    }

    public function testUrlContainsUsernameAndPassword(): void
    {
        $sut = new Url();
        $sut->setIpAddress('0.0.0.0')
            ->setUsername('admin')
            ->setPassword('123456')
        ;

        $this->assertSame('http://0.0.0.0/cm?user=admin&password=123456', $sut->build());
    }
}
