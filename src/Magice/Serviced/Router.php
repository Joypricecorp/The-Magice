<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Router extends Serviced
    {
        /**
         * @var string service name
         */
        const NAME = 'router';

        /**
         * Generate full url
         *
         * @param string $name Rout name
         * @param array  $parameters
         *
         * @return string
         */
        public static function url($name, $parameters = array())
        {
            return static::instance()->generate($name, $parameters, true);
        }

        /**
         * Generate url path
         *
         * @param string $name Rout name
         * @param array  $parameters
         *
         * @return string
         */
        public static function path($name, $parameters = array())
        {
            return str_replace(self::base(), '', static::instance()->generate($name, $parameters, true));
        }

        /**
         * Generate absolute directory path
         *
         * @param  string $name
         * @param array   $parameters
         *
         * @return string
         */
        public static function dir($name, $parameters = array())
        {
            return static::instance()->generate($name, $parameters, false);
        }

        /**
         * Get base url
         *
         * @param string $path The path to append
         *
         * @return string
         */
        public static function base($path = '')
        {
            return static::instance()->getContext()->getBaseUrl() . $path;
        }
    }
}