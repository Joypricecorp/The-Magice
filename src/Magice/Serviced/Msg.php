<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;

    /**
     * Session Flash Message\Serviced
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static add($type, $message);
     * @method static set($type, $message);
     * @method static peek($type, array $default = array())
     * @method static peekAll();
     * @method static get($type, array $default = array());
     * @method static all();
     * @method static setAll(array $messages);
     * @method static has($type);
     * @method static keys();
     */
    class Msg extends Serviced
    {
        /**
         * @var string The service name
         */
        const NAME = 'session.getFlashBag()';
    }
}