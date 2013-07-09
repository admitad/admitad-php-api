<?php

namespace Admitad\Api;

use Buzz\Message\Request;

class ApiException extends \Exception
{
    protected $response;
    protected $request;

    public function __construct($message, Request $request = null, Response $response = null)
    {
        parent::__construct($message);
        $this->request = $request;
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }
}
