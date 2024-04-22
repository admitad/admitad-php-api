<?php

namespace Admitad\Api;

use Admitad\Api\Exception\InvalidResponseException;
use Admitad\Api\Exception\InvalidSignedRequestException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Api
{
    private const MODULE_ID = 'admitad.tracking';

    protected string $host = 'https://api.admitad.com';

    public function __construct(protected ?string $accessToken = null)
    {
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken = null): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function authorizeByPassword(string $clientId, string $clientSecret, string $scope, string $username, string $password): ResponseInterface
    {
        $query = ['client_id' => $clientId, 'grant_type' => 'password', 'username' => $username, 'password' => $password, 'scope' => $scope];

        $request = new Request('POST', '/token/' . http_build_query($query));
        $request = $request->withHeader('Authorization', 'Basic ' . base64_encode($clientId . ':' . $clientSecret));

        return $this->send($request, false);
    }

    public function getAuthorizeUrl($clientId, $redirectUri, $scope, $responseType = 'code'): string
    {
        return $this->host . '/authorize/?' . http_build_query(['client_id' => $clientId, 'redirect_uri' => $redirectUri, 'scope' => $scope, 'response_type' => $responseType]);
    }

    /**
     * @throws InvalidSignedRequestException
     */
    public function parseSignedRequest(?string $signedRequest, string $clientSecret): array
    {
        if (!$signedRequest || !str_contains($signedRequest, '.')) {
            throw new InvalidSignedRequestException('Invalid signed request ' . $signedRequest);
        }

        [$key, $data] = explode('.', $signedRequest);

        $hash = hash_hmac('sha256', $data, (string) $clientSecret);

        if ($hash !== $key) {
            throw new InvalidSignedRequestException('Invalid signed request ' . $signedRequest);
        }

        return json_decode(base64_decode($data), true);
    }

    /**
     * @throws Exception
     */
    public function requestAccessToken(string $clientId, string $clientSecret, string $code, string $redirectUri): ResponseInterface
    {
        $query = ['code' => $code, 'client_id' => $clientId, 'client_secret' => $clientSecret, 'grant_type' => 'authorization_code', 'redirect_uri' => $redirectUri];

        $request = new Request('POST', '/token/', [], http_build_query($query));

        return $this->send($request, false);
    }

    /**
     * @throws Exception
     */
    public function refreshToken(string $clientId, string $clientSecret, string $refreshToken): ResponseInterface
    {
        $query = ['refresh_token' => $refreshToken, 'client_id' => $clientId, 'client_secret' => $clientSecret, 'grant_type' => 'refresh_token'];

        $request = new Request('POST', '/token/', [], http_build_query($query));

        return $this->send($request, false);
    }

    /**
     * @throws Exception
     */
    public function send(RequestInterface|MessageInterface $request, bool $useAuth = true): ResponseInterface
    {
        if ($useAuth) {
            if (null === $this->accessToken) {
                throw new Exception('Access token not provided');
            }

            $request = $request->withHeader('Authorization', 'Bearer ' . $this->accessToken);
        }

        $client = $this->createClient();

        try {
            $response = $client->send($request);
        } catch (Exception $ex) {
            AddMessage2Log($ex->getMessage(), self::MODULE_ID);
        }

        if (200 !== $response->getStatusCode()) {
            throw new Exception('Operation failed: '.json_encode($response->getBody()->getContents()));
        }

        return $response;
    }

    /**
     * @throws Exception
     */
    public function get(string $method, array $params = []): ResponseInterface
    {
        $resource = $method . '?' . http_build_query($params);
        $request = new Request('GET', $resource);

        return $this->send($request);
    }

    public function getIterator(string $method, array $params = [], $limit = 200): Iterator
    {
        return new Iterator($this, $method, $params, $limit);
    }

    /**
     * @throws Exception
     */
    public function post(string $method, array $params = []): ?ResponseInterface
    {
        $request = new Request('POST', $method, [], http_build_query($params));

        return $this->send($request);
    }

    /**
     * @throws Exception
     */
    public function me(): ResponseInterface
    {
        return $this->get('/me/');
    }

    /**
     * @throws Exception
     */
    public function authorizeClient(string $clientId, string $clientSecret, string $scope): ResponseInterface
    {
        $query = ['client_id' => $clientId, 'scope' => $scope, 'grant_type' => 'client_credentials'];

        $request = new Request(
            'POST',
            '/token/',
            [],
            http_build_query($query)
        );
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        $request = $request->withHeader('Authorization', 'Basic ' . base64_encode($clientId . ':' . $clientSecret));

        return $this->send($request, false);
    }

    /**
     * @throws Exception
     * @throws InvalidResponseException
     */
    public function selfAuthorize(string $clientId, string $clientSecret, string $scope): static
    {
        $r = $this->authorizeClient($clientId, $clientSecret, $scope);
        $data = $this->getArrayResultFromResponse($r);
        $accessToken = $data['access_token'] ?? null;
        $this->setAccessToken($accessToken);

        return $this;
    }

    protected function createClient(): Client
    {
        return new Client([
            'base_uri' => $this->host,
            'timeout' => 300,
        ]);
    }


    /**
     * @throws InvalidResponseException
     */
    public function getArrayResultFromResponse(?ResponseInterface $response = null): array
    {
        $arrayResult = [];
        $content = $response->getBody()->getContents();
        if('' === $content) {
            return [];
        }

        $arrayResult = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidResponseException($content);
        }

        return $arrayResult;
    }

    public function getModelFromArrayResult(?ResponseInterface $arrayResult = null): ?Model
    {
        if(null !== $arrayResult) {
            return new Model($arrayResult);
        }

        return null;
    }
}
