<?php

namespace Admitad\ApiClient\Exception;

use GuzzleHttp\Psr7\Request;

class RequestException extends AdmitadException
{
    protected $request;

    public function __construct(Request $request, $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }
}
