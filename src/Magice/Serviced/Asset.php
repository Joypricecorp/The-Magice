<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;

    /**
     * Class Asset
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static header($flag = true)
     * @method static footer($flag = true)
     * @method static script($file)
     * @method static scriptDeclared($text)
     * @method static style($file)
     * @method static styleDeclared($text, $media = 'all', $scoped = false)
     * @method static import($selector, $ondemand = true, $callback = null, $namespace = null)
     * @method static output($at = self::ADD_TO_HEADER)
     */
    class Asset extends Serviced
    {
        /**
         * @var string The service name
         */
        const NAME = ':mvc.view.asset';
    }
}