<?php
namespace Magice\Paginator;

interface PaginatedInterface extends \Countable
{
    public function getSlice($length, $offset);
}
