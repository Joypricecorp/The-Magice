<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced,
        Magice\Service\ServiceException
    ;

    /**
     * Class input
     * @package     Magice\Service\Serviced
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method static string method()
     */
    class Input extends Serviced
    {
        /**
         * @var string The service name
         */
        const NAME = 'request';

        /**
         * @NOTE
         * To use specials `Input` methods you MUST change `Request` object ("Symfony\Component\HttpFoundation\Request")
         * on you `web/app.php` and `web/app_dev.php` to `Magice\Input\Input`
         */
    }
}