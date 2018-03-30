<?php

namespace Admitad\ApiClient;

use Admitad\ApiClient\Exception\AdmitadException;
use Admitad\ApiClient\Exception\InvalidJSONException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class TransferExchange
{
    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    public function __construct(Request $request, Response $response = null)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Response
     * @throws AdmitadException
     */
    public function ensureResponse()
    {
        if (!$this->response) {
            throw new AdmitadException($this, 'Response is null');
        }
        return $this->response;
    }

    /**
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws AdmitadException
     * @throws InvalidJSONException
     */
    public function json($assoc = false, $depth = 512, $options = 0)
    {
        $content = $this->ensureResponse()->getBody()->getContents();

        $data = json_decode($content, $assoc, $depth, $options);

        if (json_last_error()) {
            $message = json_last_error_msg();
            throw new InvalidJSONException($this, $message);
        }

        return $data;
    }
}
