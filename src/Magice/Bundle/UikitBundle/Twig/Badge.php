<?php
namespace Magice\Bundle\UikitBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;

class Badge extends Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return array(
            'badge' => new Twig_Function_Method(
                    $this,
                    'badgeFunction',
                    array('pre_escape' => 'html', 'is_safe' => array('html'))
                )
        );
    }

    /**
     * Returns the HTML code for a badge.
     *
     * @param string $text The text of the badge
     *
     * @return string The HTML code of the badge
     */
    public function badgeFunction($text)
    {
        return sprintf('<span class="badge">%s</span>', $text);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'uk_badge';
    }
}
