<?php
namespace Magice\Mvc {

    use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

    /**
     * Class Controller
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    abstract class Controller extends BaseController // todo implement DispatcherAware
    {

        private $_events;
        private $_listeners;

        /**
         * (if set subscriber in string (class) auto create instance with load construct service)
         * @var
         */
        private $_subscribers;

    }
}