<?php
namespace Magice\Service {

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     * @thanks      Laravel for Concept idea of Facade
     * @see         Facades <http://laravel.com/docs/facades>
     */
    abstract class Serviced extends Resolver
    {
        /**
         * Get instance of service object
         *
         * @return mixed
         * @throws ServiceException
         */
        final public static function getServiceInstance()
        {
            $name = static::getServiceName();

            if (empty(static::$serviceResolved[$name])) {
                return self::resolveService($name);
            } else {
                return static::$serviceResolved[$name];
            }
        }
    }
}