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
     * @var Layout\NavigateLayoutInterface
     */
    protected $layout;

    /**
     * Cache the count of the items
     * @var integer
     **/
    protected $itemsCountCache;

    function __construct(PaginatedInterface $items, Offset $offset, NavigateLayout $layout = null)
    {
        $this->items  = $items;
        $this->layout = $layout ? : new Layout\NavigateLayout();

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

    public function getPage($pageNumber)
    {
        return new Page($this, $pageNumber);
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
        return (int) ceil($this->getCount() / $this->pageSize);
    }

    public function getPageRange()
    {
        return range(0, $this->getCount() - 1);
    }

    public function getCount()
    {
        if ($this->itemsCountCache === null) {
            $this->itemsCountCache = count($this->items);
        }
        return $this->itemsCountCache;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setNavicatorLayout(Layout\NavigateLayoutInterface $navigate)
    {
        $this->layout = $navigate;
    }

    public function getNavigator()
    {
        return $this->layout->getNavigator($this);
    }
}
