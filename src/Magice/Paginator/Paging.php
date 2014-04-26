<?php
namespace Magice\Paginator;

class Paging
{
    /**
     * @var int Total of this result
     */
    public $total;

    /**
     * @var int|null Total to display of this result
     */
    public $count;

    /**
     * @var int|null Total pages
     */
    public $pages;

    /**
     * @var int|null Current page
     */
    public $page;

    /**
     * @var int|null Page size
     */
    public $size;

    /**
     * @param int|array $total
     * @param int|null  $count
     * @param int|null  $pages
     * @param int|null  $page
     * @param int|null  $size
     */
    public function __construct($total, $count = null, $pages = null, $page = null, $size = null)
    {
        if (is_array($total)) {
            foreach ($total as $k => $v) {
                $this->$k = $v;
            }
        } else {
            $this->total = $total;
            $this->count = $count;
            $this->pages = $pages;
            $this->page  = $page;
            $this->size  = $size;
        }
    }
}