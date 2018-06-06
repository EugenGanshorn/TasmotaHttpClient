<?php

namespace TasmotaHttpClient;

use GuzzleHttp\Client;

/**
 * @method array Latitude(?string $value = null)
 * @method array Longitude(?string $value = null)
 */
class Request
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @param string $url
     * @return array
     * @throws UnknownCommandException
     */
    public function send(string $url): array
    {
        $response = $this->client->get($url);

        $result = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        if (!empty($result['Command']) && $result['Command'] === 'Unknown') {
            throw new UnknownCommandException('command is unknown');
        }

        return $result;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return array
     * @throws UnknownCommandException
     */
    public function __call(string $name, array $arguments): array
    {
        return $this->send($this->url->build($name, array_shift($arguments)));
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     * @return Request
     */
    public function setClient(Client $client): Request
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->url;
    }

    /**
     * @param Url $url
     * @return Request
     */
    public function setUrl(Url $url): Request
    {
        $this->url = $url;
        return $this;
    }
}