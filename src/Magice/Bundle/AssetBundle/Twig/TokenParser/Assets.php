<?php
namespace Magice\Bundle\AssetBundle\Twig\TokenParser;

use Twig_TokenParser;
use Twig_Token;

class Assets extends Twig_TokenParser
{
    protected $container;

    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new AssetsNode($value, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'assets';
    }
}