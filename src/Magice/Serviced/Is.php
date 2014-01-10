<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;

    /**
     * Class Is
     *
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     *
     * @version     1.0
     * @since       1.0
     */

    /**
     * Methods
     *
     * @method static method($key)
     * @method static response($key = 'json|jsonp|html|raw')
     */
    class Is extends Serviced
    {
        /**
         * @var string The checking service name
         */
        const NAME = ':utils.checking';
    }
}