<?php

namespace Admitad\Api;

class Response extends \Buzz\Message\Response
{
    private $arrayResult;

    public function getArrayResult()
    {
        if (null === $this->arrayResult) {
            $this->arrayResult = json_decode($this->getContent(), true);
        }
        return $this->arrayResult;
    }
}
