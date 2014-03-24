<?php
namespace Magice\Paginator;

interface PaginatorInterface
{
    public function setPageSize($pageSize);

    public function getPageSize();

    public function getPage($numPage);

    public function getTotalPages();

    public function getPageRange();

    public function getCount();

    public function getItems();

    public function setStart($start);

    public function getStart();

    public function setTotal($total);

    public function getTotal();

    public function setNavicatorLayout(Layout\NavigateLayoutInterface $navigate);

    public function getNavigator();
}
