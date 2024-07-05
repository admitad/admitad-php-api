<?php

namespace Admitad\Api;

use Admitad\Api\Exception\InvalidResponseException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use LogicException;

class Iterator implements \Iterator, \Countable
{
    protected mixed $offset;

    protected bool $initialized = false;

    protected ?array $meta = null;

    protected ?array $results = null;

    protected bool $finished = false;

    public function __construct(protected Api $api, protected string $method, protected array $params = [], private int $limit = 200)
    {
    }

    public function current(): mixed
    {
        if (!$this->initialized) {
            throw new LogicException('Rewind first');
        }

        return current($this->results);
    }

    /**
     * @throws Exception
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function next(): void
    {
        if (!$this->initialized) {
            throw new LogicException('Rewind first');
        }

        if ($this->finished) {
            return;
        }

        ++$this->offset;

        if ($this->meta['count'] <= $this->offset) {
            $this->finished = true;

            return;
        }

        if (!next($this->results)) {
            $this->load();
        }
    }

    public function key(): ?bool
    {
        if (!$this->initialized) {
            throw new LogicException('Rewind first');
        }

        if ($this->finished) {
            return null;
        }

        return $this->offset;
    }

    public function valid(): bool
    {
        if (!$this->initialized) {
            throw new LogicException('Rewind first');
        }

        return !$this->finished;
    }

    /**
     * @throws Exception
     * @throws InvalidResponseException
     * @throws GuzzleException
     */
    public function rewind(): void
    {
        if ($this->initialized && 0 === $this->offset) {
            return;
        }

        $this->offset = 0;
        $this->initialized = true;
        $this->finished = false;
        $this->load();
    }

    public function count(): int
    {
        if (!$this->initialized) {
            throw new LogicException('Rewind first');
        }

        return $this->meta['count'];
    }

    /**
     * @throws InvalidResponseException
     * @throws Exception
     * @throws GuzzleException
     */
    protected function load(): void
    {
        $response = $this->api->get($this->method, array_merge($this->params, ['limit' => $this->limit, 'offset' => $this->offset]));

        $result = $this->api->getArrayResultFromResponse($response);

        $this->meta = $result['_meta'] ?: ['limit' => $this->limit, 'offset' => $this->offset, 'count' => 0];

        $this->results = $result['results']?: [];

        if ($this->meta['limit'] < $this->limit) {
            $this->limit = $this->meta['limit'];
        }

        if (empty($this->results)) {
            $this->finished = true;
        }
    }
}
