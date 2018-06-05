<?php

class UrlTest extends PHPUnit\Framework\TestCase
{
    public function testUrlContainsIpAddress(): void
    {
        $sut = new \TasmotaHttpClient\Url();
        $sut->setIpAddress('0.0.0.0');

        $this->assertSame('http://0.0.0.0/cm?', $sut->build());
    }

    public function testUrlContainsCommand(): void
    {
        $sut = new \TasmotaHttpClient\Url();
        $sut->setIpAddress('0.0.0.0');

        $this->assertSame('http://0.0.0.0/cm?cmnd=command', $sut->build('command'));
    }

    public function testUrlContainsUsernameAndPassword(): void
    {
        $sut = new \TasmotaHttpClient\Url();
        $sut->setIpAddress('0.0.0.0')
            ->setUsername('admin')
            ->setPassword('123456')
        ;

        $this->assertSame('http://0.0.0.0/cm?username=admin&password=123456', $sut->build());
    }
}
