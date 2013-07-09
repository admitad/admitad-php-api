<?php

namespace Admitad\Api;

use Buzz\Client\Curl;
use Buzz\Message\Request;
use Psr\Log\LoggerInterface;

class Api
{
    protected $accessToken;
    protected $host = 'https://api.admitad.com';

    public function __construct($accessToken = null)
    {
        $this->accessToken = $accessToken;
    }

    public function requestAccessToken($clientId, $clientSecret, $code, $redirectUri)
    {
        $query = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri
        ];

        $request = new Request(Request::METHOD_POST, '/token/');
        $request->setContent(http_build_query($query));

        return $this->send($request, null, false);
    }

    public function refreshToken($clientId, $clientSecret, $refreshToken)
    {
        $query = [
            'refresh_token' => $refreshToken,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'refresh_token'
        ];

        $request = new Request(Request::METHOD_POST, '/token/');
        $request->setContent(http_build_query($query));

        return $this->send($request, null, false);
    }

    public function send(Request $request, Response $response = null, $auth = true)
    {
        if (is_null($response)) {
            $response = new Response();
        }

        $request->setHost($this->host);
        if ($auth) {
            if (!$this->accessToken) {
                throw new \InvalidArgumentException("Access token not provided");
            }
            $request->addHeader('Authorization: Bearer ' . $this->accessToken);
        }

        $curl = new Curl();
        $curl->setVerifyPeer(false);
        $curl->setTimeout(15);
        $curl->send($request, $response);

        if (!$response->isSuccessful()) {
            throw new ApiException('Operation failed: ' . $response->getContent(), $request, $response);
        }

        return $response;
    }

    public function get($method, $params = [])
    {
        $request = new Request(Request::METHOD_GET, $method . '?' . http_build_query($params));

        return $this->send($request);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }
}
