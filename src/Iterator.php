<?php


namespace Admitad\ApiClient;

class Iterator implements \Iterator, \Countable
{
    protected $apiClient;

    protected $path;
    protected $options;

    protected $offset;
    protected $limit;

    /**
     * @var \Iterator
     */
    protected $cursor;

    protected $count;

    public function __construct(AdmitadApiClient $client, $path, array $options = [], $limit = 100)
    {
        $this->apiClient = $client;
        $this->path = $path;
        $this->options = array_replace([
            'query' => []
        ], $options);
        $this->limit = $limit;
    }

    public function current()
    {
        $this->init();

        return $this->cursor->current();
    }

    public function init()
    {
        if (null === $this->offset) {
            $this->rewind();
        }
    }

    public function rewind()
    {
        if (0 === $this->offset) {
            return;
        }

        $this->offset = 0;
        $this->cursor = $this->getCursor();
    }

    public function next()
    {
        $this->init();

        $this->cursor->next();
    }

    public function key()
    {
        $this->init();

        return $this->offset;
    }

    public function valid()
    {
        $this->init();

        return $this->cursor->valid();
    }

    protected function getCursor()
    {
        while (true) {
            $options = array_merge_recursive($this->options, [
                'query' => [
                    'limit' => $this->limit,
                    'offset' => $this->offset
                ]
            ]);

            $data = $this->apiClient->get($this->path, $options)->json(true);

            if (isset($data['_meta'])) {
                $this->setMeta($data['_meta']);
            }

            if (empty($data['results'])) {
                break;
            }

            foreach ($data['results'] as $item) {
                yield $this->offset => $item;
                $this->offset++;
            }

            if ($this->offset >= $this->count) {
                break;
            }
        }
    }

    protected function setMeta($meta)
    {
        $this->count = $meta['count'];
        $this->offset = $meta['offset'];
        $this->limit = $meta['limit'];
    }


    public function count()
    {
        $this->init();
        return $this->count;
    }
}
