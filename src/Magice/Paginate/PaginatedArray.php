<?php
namespace Magice\Paginator;

class PaginatedArray implements PaginatedInterface
{
    protected $data;

    function __construct(array $data)
    {
        $this->data = $data;
    }

    public function count()
    {
        return count($this->data);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}
