<?php

namespace Admitad\ApiClient;

use Admitad\ApiClient\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class HttpClient implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $endpoint = 'https://api.admitad.com';

    /** @var Client */
    protected $client;

    public function __construct(array $config = [], LoggerInterface $logger = null)
    {
        $config = array_merge([
            'base_uri' => $this->endpoint
        ], $config);

        $this->client = new Client($config);

        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param string $httpMethod
     * @param string|UriInterface $uri
     * @param array $options
     * @return TransferExchange
     * @throws RequestException
     */
    public function request($httpMethod, $uri, array $options = [])
    {
        if (isset($options['query'])) {
            $uri = $this->resolveUri($uri, $options['query']);
        }

        $request = new Request($httpMethod, $uri);

        try {
            $response = $this->client->send($request, $options);
        } catch (GuzzleException $e) {
            throw new RequestException($request, $e->getMessage(), $e->getCode(), $e);
        }

        $exchange = new TransferExchange($request, $response);

        return $exchange;
    }

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @return TransferExchange
     */
    public function get($uri, array $options = [])
    {
        return $this->request('GET', $uri, $options);
    }

    /**
     * @param string|UriInterface $uri
     * @param array $options
     * @return TransferExchange
     */
    public function post($uri, array $options = [])
    {
        return $this->request('POST', $uri, $options);
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getBaseUri()
    {
        return new Uri($this->getEndpoint());
    }

    public function resolveUri($path, $query = [])
    {
        return (string)$this
            ->getBaseUri()
            ->withPath($path)
            ->withQuery(http_build_query($query));
    }
}
