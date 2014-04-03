<?php
namespace Magice\Bundle\AssetBundle\Twig\TokenParser;

use Twig_Node_Expression;
use Twig_Compiler;
use Twig_Node;

class ImportNode extends Twig_Node
{
    public function __construct($name, Twig_Node_Expression $value, $line, $tag = null)
    {
        parent::__construct(array('value' => $value), array('name' => $name), $line, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('$context[\'' . $this->getAttribute('name') . '\'] = ')
            ->subcompile($this->getNode('value'))
            ->raw(";\n");
    }
}