<?php

namespace Admitad\Api;

use Admitad\Api\Exception\ApiException;
use Buzz\Client\ClientInterface;
use Buzz\Client\Curl;

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

    public function send(Request $request, Response $response = null, $useAuth = true)
    {
        if (is_null($response)) {
            $response = new Response();
        }

        if (null === $request->getHost()) {
            $request->setHost($this->host);
        }

        if ($useAuth) {
            if (null === $this->accessToken) {
                throw new \InvalidArgumentException("Access token not provided");
            }
            $request->addHeader('Authorization: Bearer ' . $this->accessToken);
        }

        $client = $this->createClient();
        $client->send($request, $response);

        if (!$response->isSuccessful()) {
            throw new ApiException('Operation failed: ' . $response->getError(), $request, $response);
        }

        return $response;
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

    public function get($method, $params = array())
    {
        $resource = $method . '?' . http_build_query($params);
        $request = new Request(Request::METHOD_GET, $resource);
        return $this->send($request);
    }

    public function post($method, $params = array())
    {
        $request = new Request(Request::METHOD_POST, $method);
        $request->setContent(http_build_query($params));
        return $this->send($request);
    }

    public function me()
    {
        return $this->get('/me/');
    }

    public function getReferrals()
    {
        return $this->get('/referrals/');
    }

    public function getReferral($id)
    {
        return $this->get('/referrals/' . $id);
    }

    /**
     * @return ClientInterface
     */
    protected function createClient()
    {
        $curl = new Curl();
        $curl->setTimeout(15);
        return $curl;
    }
}
