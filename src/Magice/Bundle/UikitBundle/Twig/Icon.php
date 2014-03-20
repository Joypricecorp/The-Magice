<?php
/**
 * This file is part of BraincraftedBootstrapBundle.
 * (c) 2012-2013 by Florian Eckerstorfer
 */

namespace Magice\Bundle\UikitBundle\Twig;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Function_Method;

class Icon extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            'parse_icons' => new Twig_Filter_Method(
                    $this,
                    'parseIconsFilter',
                    array('pre_escape' => 'html', 'is_safe' => array('html'))
                )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'icon' => new Twig_Function_Method(
                    $this,
                    'iconFunction',
                    array('pre_escape' => 'html', 'is_safe' => array('html'))
                )
        );
    }

    /**
     * Parses the given string and replaces all occurrences of .icon-[name] with the corresponding icon.
     *
     * @param string $text  The text to parse
     *
     * @return string The HTML code with the icons
     */
    public function parseIconsFilter($text)
    {
        $that = $this;
        return preg_replace_callback(
            '/\.icon-([a-z0-9-]+)/',
            function ($matches) use ($that) {
                return $that->iconFunction($matches[1]);
            },
            $text
        );
    }

    /**
     * Returns the HTML code for the given icon.
     *
     * @param string $icon  The name of the icon
     *
     * @return string The HTML code for the icon
     */
    public function iconFunction($icon)
    {
        return sprintf('<span class="glyphicon glyphicon-%s"></span>', $icon);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'uk_icon';
    }
}
