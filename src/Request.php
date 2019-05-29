<?php

namespace TasmotaHttpClient;

use GuzzleHttp\Client;
use function GuzzleHttp\Promise\all;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @method array Latitude(?string $value = null, array $options = [], \Closure $callback = null)
 * @method array Longitude(?string $value = null, array $options = [], \Closure $callback = null)
 * @method array Status(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array Power(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array Color(?string $value = null, array $options = [], \Closure $callback = null)
 * @method array Color2(?string $value = null, array $options = [], \Closure $callback = null)
 * @method array CT(?int $value = null, array $options = [], \Closure $callback = null)
 * @method array Dimmer(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array Fade(?bool $value = null, array $options = [], \Closure $callback = null)
 * @method array Speed(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array Scheme(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array LedTable(?bool $value = null, array $options = [], \Closure $callback = null)
 * @method array Wakeup(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array WakeupDuration(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array Upgrade(?integer $value = null, array $options = [], \Closure $callback = null)
 * @method array OtaUrl(?string $value = null, array $options = [], \Closure $callback = null)
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
     * @var PromiseInterface[]
     */
    protected $promises;

    /**
     * @var bool
     */
    protected $asyncRequests = false;

    /**
     * @param string        $url
     * @param array         $options
     * @param \Closure|null $callback
     *
     * @return array
     * @throws UnknownCommandException
     */
    public function send(string $url, array $options = [], \Closure $callback = null): array
    {
        if ($callback === null && !$this->asyncRequests) {
            $response = $this->client->get($url, $options);
            return $this->handleResponse($response);
        } else {
            $this->promises[] = $this->client->getAsync($url, $options)->then(function (ResponseInterface $response) use ($callback) {
                $result = $this->handleResponse($response);

                if ($callback !== null) {
                    $callback($result);
                }
            }, function (\Exception $exception) {
                throw $exception;
            });

            return [];
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return array
     * @throws UnknownCommandException
     */
    public function __call(string $name, array $arguments): array
    {
        return $this->send($this->url->build($name, array_shift($arguments)), (array) array_shift($arguments),  array_shift($arguments));
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
    public function setClient(Client $client): self
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
    public function setUrl(Url $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return Request
     */
    public function startAsyncRequests(): self
    {
        $this->asyncRequests = true;
        return $this;
    }

    /**
     * @return Request
     */
    public function stopAsyncRequests(): self
    {
        $this->asyncRequests = false;
        return $this;
    }

    /**
     * @return Request
     */
    public function finishAsyncRquests(): self
    {
        all($this->promises)->wait(false);
        return $this;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws UnknownCommandException
     */
    protected function handleResponse(ResponseInterface $response): array
    {
        $result = \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        if (!empty($result['Command']) && $result['Command'] === 'Unknown') {
            throw new UnknownCommandException('command is unknown');
        }

        return $result;
    }
}
