<?php

namespace TasmotaHttpClient;

use GuzzleHttp\Client;

/**
 * @method array Latitude(?string $value = null, array $options = [])
 * @method array Longitude(?string $value = null, array $options = [])
 * @method array Status(?integer $value = null, array $options = [])
 * @method array Power(?integer $value = null, array $options = [])
 * @method array Upgrade(?integer $value = null, array $options = [])
 * @method array OtaUrl(?string $value = null, array $options = [])
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
     * @param array  $options
     *
     * @return array
     * @throws UnknownCommandException
     */
    public function send(string $url, array $options = []): array
    {
        $response = $this->client->get($url, $options);

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
        return $this->send($this->url->build($name, array_shift($arguments)), (array) array_shift($arguments));
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @required
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
     * @required
     * @param Url $url
     * @return Request
     */
    public function setUrl(Url $url): Request
    {
        $this->url = $url;
        return $this;
    }
}
