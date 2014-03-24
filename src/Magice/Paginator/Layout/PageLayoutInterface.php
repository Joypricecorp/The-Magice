<?php
namespace Magice\Paginator\Layout;

use Magice\Paginator\PageInterface;

interface PageLayoutInterface
{
    public function getNavigation(PageInterface $page, $options = array());
}
