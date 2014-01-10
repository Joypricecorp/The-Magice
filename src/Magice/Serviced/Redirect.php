<?php
namespace Magice\Serviced {

    use Magice\Service\Serviced;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Redirect
    {
        /**
         * Redirecto router
         *
         * @param string $name Router name
         * @param int    $status
         * @param array  $parameters
         * @param array  $headers
         *
         * @return RedirectResponse
         */
        public static function to($name, $status = 302, $parameters = array(), $headers = array())
        {
            return new RedirectResponse(Router::url($name, $parameters), $status, $headers);
        }

        /**
         * Redirect to url
         *
         * @param string $url full url
         * @param int    $status
         * @param array  $headers
         *
         * @return RedirectResponse
         */
        public static function url($url, $status = 302, $headers = array())
        {
            return new RedirectResponse($url, $status, $headers);
        }

        /**
         * Redirect to path
         *
         * @param string $path path
         * @param int    $status
         * @param array  $headers
         *
         * @return RedirectResponse
         */
        public static function path($path, $status = 302, $headers = array())
        {
            return new RedirectResponse(Router::base($path), $status, $headers);
        }

        /**
         * Redirect to base url
         *
         * @param int    $status
         * @param array  $headers
         *
         * @return RedirectResponse
         */
        public static function base($status = 302, $headers = array())
        {
            return new RedirectResponse(Router::base(), $status, $headers);
        }
    }
}