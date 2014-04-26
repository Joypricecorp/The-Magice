<?php
namespace Magice\Paginator;

interface NavigatorInterface
{
    public function getNavigator(PaginatorInterface $paginator);
}