<?php
namespace Magice\Paginator;

class Offset
{
    public $start = 0;
    public $limit = 20;
    public $total;

    public function __construct($start, $limit, $total = null)
    {
        $this->start = $start;
        $this->limit = $limit;
        $this->total = $total;
    }
}