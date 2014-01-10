<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;

    /**
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static string get($name, $default = null)
     * @method static string set($name, $value)
     * @method static string has($name)
     */
    class Session extends Serviced
    {
        /**
         * @var string The service name
         */
        const NAME = 'session';
    }
}