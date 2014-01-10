<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced,
        Magice\Event\DispatcherInterface;

    /**
     * Event
     *
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static string name($name = null);
     * @method static void stop();
     * @method static boolean stopped();
     * @method static DispatcherInterface dispatcher(DispatcherInterface $dispatcher = null);
     */
    class Event extends Serviced
    {
        /**
         * @var string The service class name
         */
        const NAME = '\Magice\Event\Event';
    }
}