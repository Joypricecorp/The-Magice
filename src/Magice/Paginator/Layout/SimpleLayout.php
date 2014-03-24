<?php

namespace Magice\Paginator\Layout;

use Magice\Paginator\PageInterface;

class SimpleLayout implements PageLayoutInterface
{
    /**
     * Holds the rendering options
     * @var array
     */
    protected $options;

    public function __construct($options = array())
    {
        $this->options = array_merge(
            array(
                'url'         => '',
                'pageParam'   => 'page',
                'querystring' => ''
            ),
            $options
        );
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function getOption($name)
    {
        return $this->options[$name];
    }

    public function getNavigation(PageInterface $page, $options = array())
    {
        if ($options) {
            $options = array_merge($this->options, $options);
        } else {
            $options = $this->options;
        }

        $buffer = array();
        if ($page->hasPrevious()) {
            $href = "{$options['url']}?{$options['pageParam']}={$page->getPreviousPageNumber()}";
            if ($options['querystring']) {
                $href .= '&' . $options['querystring'];
            }
            $buffer[] = "<a href=\"$href\">&lt; Previous</a>";
        } else {
            $buffer[] = '<span class="inactive">&lt; Previous</span>';
        }
        $buffer[] = ' | ';
        if ($page->hasNext()) {
            $href = "{$options['url']}?{$options['pageParam']}={$page->getNextPageNumber()}";
            if ($options['querystring']) {
                $href .= '&' . $options['querystring'];
            }
            $buffer[] = "<a href=\"$href\">Next &gt;</a>";
        } else {
            $buffer[] = '<span class="inactive">Next &gt;</span>';
        }

        return implode('', $buffer);
    }
}
