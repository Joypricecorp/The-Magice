<?php
namespace Magice\Orm\Doctrine\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * "CONVERT" "(" ArithmeticPrimary AliasResultVariable AliasResultVariable ")".
 * More info:
 * http://dev.mysql.com/doc/refman/5.0/en/cast-functions.html#function_convert
 * @category    Entities
 * @package     Entities\meta\Functions
 * @author      ツ Liverbool <nukboon@gmail.com>
 * @license     MIT License
 */
class ConvertUsing extends FunctionNode
{
    public $field;
    public $using;
    public $charset;

    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf(
            'CONVERT(%s USING %s)',
            $sqlWalker->walkArithmeticPrimary($this->field),
            //$sqlWalker->walkSimpleArithmeticExpression($this->using), // or remove USING and uncomment this
            $sqlWalker->walkSimpleArithmeticExpression($this->charset)
        );
    }

    /**
     * @override
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->field = $parser->ArithmeticPrimary();
        // adopt use bypass validate variable of parse by using AliasResultVariable ...!!
        $this->using   = $parser->AliasResultVariable();
        $this->charset = $parser->AliasResultVariable();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}