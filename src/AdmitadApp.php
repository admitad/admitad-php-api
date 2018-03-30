<?php

namespace Admitad\ApiClient;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

class AdmitadApp implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected $clientId;
    protected $clientSecret;

    protected $httpClient;

    public function __construct($clientId, $clientSecret, LoggerInterface $logger = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->logger = $logger;
        $this->httpClient = new HttpClient([], $logger);
    }

    public function createApiClient($accessToken)
    {
        return new AdmitadApiClient($accessToken, [], $this->logger);
    }

    public function getAuthorizationUrl($scope, $responseType = 'code', $redirectUrl = '')
    {
        $query = [
            'redirect_uri' => $redirectUrl,
            'scope' => $scope,
            'response_type' => $responseType
        ];

        return $this->httpClient->resolveUri('/authorize/', $query);
    }

    public function getAccessTokenByCode($code, $redirectUrl)
    {
        return $this->token([
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUrl
        ]);
    }

    public function refreshAccessToken($refreshToken)
    {
        return $this->token([
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token'
        ]);
    }

    public function getSelfAccessToken($scope)
    {
        return $this->token([
            'scope' => $scope,
            'grant_type' => 'client_credentials'
        ]);
    }

    protected function token(array $params = [])
    {
        $params = array_replace([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ], $params);

        return $this->httpClient->post('/token/', [
            'form_params' => $params,
            'auth' => [$this->clientId, $this->clientSecret]
        ]);
    }
}


