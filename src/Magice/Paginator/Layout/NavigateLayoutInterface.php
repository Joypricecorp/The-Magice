<?php
namespace Magice\Paginator\Layout;

use Magice\Paginator\PaginatorInterface;

interface NavigateLayoutInterface
{
    public function getNavigator(PaginatorInterface $paginator);
}