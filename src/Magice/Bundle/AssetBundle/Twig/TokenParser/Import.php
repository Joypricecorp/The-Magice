<?php
namespace Magice\Bundle\AssetBundle\Twig\TokenParser;

use Magice\Asset\Importer;
use Twig_TokenParser;
use Twig_Token;

class Import extends Twig_TokenParser
{
    protected $container;

    public function parse(Twig_Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $name = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
        $stream->expect(Twig_Token::OPERATOR_TYPE, '=');
        $value = $parser->getExpressionParser()->parseExpression();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        Importer::asset($value);

        //return new ImportNode($name, $value, $token->getLine(), $this->getTag());
    }

    public function getTag()
    {
        return 'import';
    }
}