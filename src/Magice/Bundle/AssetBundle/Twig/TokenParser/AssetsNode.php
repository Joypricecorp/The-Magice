<?php
namespace Magice\Bundle\AssetBundle\Twig\TokenParser;

use Twig_Compiler;
use Twig_Node;

class AssetsNode extends Twig_Node
{
    public function __construct($value, $line, $tag = null)
    {
        parent::__construct(array('value' => $value), array(), $line, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        // TODO: use $context['importer']
        $compiler
            ->addDebugInfo($this)
            ->write('\Magice\Asset\Importer::asset(\'' . $this->getNode('value')->getAttribute('value') . '\')')
            ->raw(";\n");
    }
}