<?php

namespace Magice\Paginator;

use Magice\Paginator\Exception;
use Magice\Paginator\Layout\NavigateLayout;

/**
 * Pagination class
 **/
class Paginator implements PaginatorInterface
{
    /**
     * Items to paginate. Can be any object implementing the
     * SPL Countable interface
     * @var mixed
     **/
    protected $items;

    /**
     * Max number of records per page
     * @var integer
     **/
    protected $pageSize;

    /**
     * @var integer offset start
     */
    protected $start;

    /**
     * @var integer offset total
     */
    protected $total;

    /**
     * @var NavigatorInterface
     */
    protected $layout;

    function __construct(PaginatedInterface $items, Offset $offset, NavigatorInterface $layout = null)
    {
        $this->items  = $items;
        $this->layout = $layout ? : new Navigator();

        $this->setPageSize($offset->limit);
        $this->setStart($offset->start);
        $this->setTotal($offset->total);
    }

    public function setPageSize($pageSize)
    {
        if (is_int($pageSize) && $pageSize > 0) {
            $this->pageSize = $pageSize;
        } else {
            throw new Exception\InvalidPageSize;
        }
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getTotalPages()
    {
        return (int) ceil($this->getTotal() / $this->pageSize);
    }

    public function getPageRange()
    {
        return range(0, $this->getTotalPages() - 1);
    }

    public function getCount()
    {
        return $this->items->count();
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setNavicatorLayout(NavigatorInterface $navigate)
    {
        $this->layout = $navigate;
    }

    public function getNavigator()
    {
        return $this->layout->getNavigator($this);
    }
}
