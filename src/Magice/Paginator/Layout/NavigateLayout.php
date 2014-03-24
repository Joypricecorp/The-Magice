<?php
namespace Magice\Paginator\Layout;

use Magice\Paginator\PaginatorInterface;

class NavigateLayout implements NavigateLayoutInterface
{
    private $midRange = 5;
    private $baseUri;
    private $pageSize;

    public function __construct($baseUri = '')
    {
        $this->baseUri = $baseUri;
    }

    public function start($i)
    {
        return (($i-1) * $this->pageSize);
    }

    public function getNavigator(PaginatorInterface $paginator)
    {
        $pageSize       = $paginator->getPageSize();
        $totalPages     = $paginator->getTotalPages();
        $currentPage    = ceil(($paginator->getStart() + 1) / $pageSize);
        $this->pageSize = $pageSize;

        if ($currentPage < 1) {
            $currentPage = 1;
        }

        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $prevPage = $currentPage - 1;
        $nextPage = $currentPage + 1;

        $buffer   = '';
        $iconPrev = '<i class="uk-icon-angle-double-left"></i>';
        $iconNext = '<i class="uk-icon-angle-double-right"></i>';

        if ($totalPages > 10) {
            $buffer = ($currentPage > 1)
                ? sprintf('<li><a href="%s?start=%s&limit=%s">%s</a></li>', $this->baseUri, $this->start($prevPage), $pageSize, $iconPrev)
                : sprintf('<li class="uk-disabled"><span>%s</span></li>', $iconPrev);

            $startRange = $currentPage - floor($this->midRange / 2);
            $endRange   = $currentPage + floor($this->midRange / 2);

            if ($startRange <= 0) {
                $endRange += abs($startRange) + 1;
                $startRange = 1;
            }
            if ($endRange > $totalPages) {
                $startRange -= $endRange - $totalPages;
                $endRange = $totalPages;
            }

            $range = range($startRange, $endRange);

            for ($i = 1; $i <= $totalPages; $i++) {

                if ($range[0] > 2 And $i == $range[0]) {
                    $buffer .= '<li><span>...</span></li>';
                }

                if ($i == 1 || $i == $totalPages || in_array($i, $range)) {
                    $buffer .= ($i == $currentPage)
                        ? sprintf('<li class="uk-active"><span>%s</span></li>', $i)
                        : sprintf('<li><a href="%s?start=%s&limit=%s">%s</a></li>', $this->baseUri, $this->start($i), $pageSize, $i);
                }

                if ($range[$this->midRange - 1] < $totalPages - 1 And $i == $range[$this->midRange - 1]) {
                    $buffer .= '<li><span>...</span></li>';
                }
            }

            $buffer .= ($currentPage < $totalPages)
                ? sprintf('<li><a href="%s?start=%s&limit=%s">%s</a></li>', $this->baseUri, $this->start($nextPage), $pageSize, $iconNext)
                : sprintf('<li class="uk-disabled"><span>%s</span></li>', $iconNext);

        } else {
            for ($i = 1; $i <= $totalPages; $i++) {
                $buffer .= ($i == $currentPage)
                    ? sprintf('<li class="uk-active"><span>%i</span></li>', $i)
                    : sprintf('<li><a href="%s?start=%s&limit=%s">%s</a></li>', $this->baseUri, $this->start($i), $pageSize, $i);
            }
        }

        return sprintf('<ul class="uk-pagination">%s</ul>', $buffer);
    }
}