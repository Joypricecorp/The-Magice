<?php
namespace Magice\Paginator;

interface PageInterface extends \IteratorAggregate
{
    public function getNumber();

    public function hasNext();

    public function hasPrevious();

    public function hasOtherPages();

    public function getNextPageNumber();

    public function getPreviousPageNumber();

    /**
     * The 1-based index of the first item on this page
     **/
    public function getStartIndex();

    /**
     * The 1-based index of the last item on this page
     **/
    public function getEndIndex();

    public function getLayout();

    public function setLayout(Layout\PageLayoutInterface $layout);

    public function getNavigation();

    public function getPaginator();

    public function isEmpty();
}
