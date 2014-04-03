<?php
namespace Magice\Bundle\AssetBundle\Twig\TokenParser;

use Magice\Asset\Importer;
use Twig_TokenParser;
use Twig_Token;

class Imports extends Twig_TokenParser
{
    protected $container;

    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new ImportsNode($value, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'imports';
    }
}