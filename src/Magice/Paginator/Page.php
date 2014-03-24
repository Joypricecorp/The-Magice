<?php
namespace Magice\Paginator;

class Page implements PageInterface
{
    /**
     * Paginator for this page
     * @var object
     **/
    protected $paginator;

    /**
     * Page number
     * @var integer
     **/
    protected $page;

    /**
     * PageLayout used to render the navigation links
     * @var object
     **/
    protected $layout;

    /**
     * Wether or not this page is empty
     * @var string
     */
    protected $empty;

    public function __construct($paginator, $page = 1)
    {
        $this->empty = false;

        if ($paginator instanceof Paginator) {
            $this->paginator = $paginator;
        } else {
            throw new Exception\InvalidPage("The paginator must be an instance of a class implementing PaginatorInterface");
        }

        if (is_int($page) && $page > 0) {
            if ($page > $paginator->getTotalPages()) {
                if ($page == 1) {
                    if (!$paginator->getAllowEmptyFirstPage()) {
                        throw new Exception\EmptyPage;
                    } else {
                        $this->empty = true;
                    }
                } else {
                    throw new Exception\InvalidPage;
                }
            }

            $this->page = $page;
        } else {
            throw new Exception\InvalidPage;
        }

        $this->layout = new Layout\SimpleLayout;
    }

    public function isEmpty()
    {
        return $this->empty;
    }

    public function getPaginator()
    {
        return $this->paginator;
    }

    public function getIterator()
    {
        if ($this->isEmpty()) {
            $iterator = array();
        } else {
            $iterator = $this->paginator->getItems()->getSlice(
                $this->paginator->getPageSize(),
                $this->getStartIndex() - 1
            );
        }

        return new \ArrayIterator($iterator);
    }

    public function getNumber()
    {
        return $this->page;
    }

    public function hasNext()
    {
        return $this->page < $this->paginator->getTotalPages();
    }

    public function hasPrevious()
    {
        return $this->page > 1;
    }

    public function hasOtherPages()
    {
        return ($this->hasPrevious() || $this->hasNext());
    }

    public function getNextPageNumber()
    {
        if ($this->page < $this->paginator->getTotalPages()) {
            return $this->page + 1;
        }

        return null;
    }

    public function getPreviousPageNumber()
    {
        if ($this->page > 1) {
            return $this->page - 1;
        }

        return null;
    }

    public function getStartIndex()
    {
        $pageSize = $this->paginator->getPageSize();
        return ($pageSize * $this->page - $pageSize) + 1;
    }

    public function getEndIndex()
    {
        $pageSize = $this->paginator->getPageSize();
        $total    = $this->paginator->getCountItems();
        if (($pageSize * $this->page - $total) > 0) {
            return $total;
        } else {
            return $pageSize * $this->page;
        }
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout(Layout\PageLayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    public function getNavigation($options = array())
    {
        return $this->layout->getNavigation($this, $options);
    }
}
