<?php

namespace Admitad\Api;

use Admitad\Api\Exception\InvalidResponseException;

class Response extends \Buzz\Message\Response
{
    private $result;

    public function getResult($field = null)
    {
        if (null === $this->result) {
            $this->result = new Object(json_decode($this->getContent()));
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidResponseException($this->getContent());
            }
        }

        if (null !== $field) {
            if (null !== $this->result && isset($this->result[$field])) {
                return $this->result[$field];
            }
            return null;
        }
        return $this->result;
    }

    public function getError()
    {
        return $this->getResult('error');
    }

    public function getErrorDescription()
    {
        return $this->getResult('error_description');
    }

    public function getErrorCode()
    {
        return $this->getResult('error_code');
    }
}
