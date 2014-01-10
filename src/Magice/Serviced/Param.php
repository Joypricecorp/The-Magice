<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;

    /**
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static mixed get($path, $default = null);
     * @method static mixed set($path, $value);
     */
    class Param extends Serviced
    {
        /**
         * @var string The configuration service name
         */
        const NAME = ':registry';
    }
}