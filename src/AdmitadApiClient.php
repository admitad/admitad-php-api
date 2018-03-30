<?php

namespace Admitad\ApiClient;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class AdmitadApiClient extends HttpClient
{
    protected $accessToken;

    public function __construct($accessToken, array $config = [], LoggerInterface $logger = null)
    {
        $this->accessToken = $accessToken;

        $config = array_replace([
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ]
        ], $config);

        parent::__construct($config, $logger);



    }

    public function iterate($path, $limit = 100, array $options = [])
    {
        return new Iterator($this, $path, $options, $limit);
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }
}
