<?php
namespace Magice\Bundle\AssetBundle\Twig\TokenParser;

use Twig_Node_Expression;
use Twig_Compiler;
use Twig_Node;

class ImportsNode extends Twig_Node
{
    public function __construct($value, $line, $tag = null)
    {
        parent::__construct(array('value' => $value), array(), $line, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('\Magice\Asset\Importer::asset(\'' . $this->getNode('value')->getAttribute('value') . '\')')
            ->raw(";\n");
    }
}