<?php

namespace Admitad\ApiClient\Exception;

use Admitad\ApiClient\TransferExchange;

class ResponseException extends AdmitadException
{
    protected $exchange;

    public function __construct(TransferExchange $exchange, $message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->exchange = $exchange;
    }

    public function getExchange()
    {
        return $this->exchange;
    }

}
