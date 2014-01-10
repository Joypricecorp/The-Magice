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
    class Security extends Serviced
    {
        /**
         * @var string service name
         */
        const NAME = 'security.context';

        public static function granted($attributes, $object = null)
        {
            return static::instance()->isGranted($attributes, $object);
        }

        /**
         * @return TokenInterface|null
         */
        public static function token()
        {
            return static::instance()->getToken();
        }

        /**
         * @return bool
         * @see http://symfony.com/doc/2.3/cookbook/security/remember_me.html#forcing-the-user-to-re-authenticate-before-accessing-certain-resources
         */
        public static function IS_AUTHENTICATED_FULLY()
        {
            return self::granted('IS_AUTHENTICATED_FULLY');
        }

        /**
         * @return bool
         */
        public static function IS_AUTHENTICATED_ANONYMOUSLY()
        {
            return self::granted('IS_AUTHENTICATED_ANONYMOUSLY');
        }

        /**
         * @return bool
         */
        public static function IS_AUTHENTICATED_REMEMBERED()
        {
            return self::granted('IS_AUTHENTICATED_REMEMBERED');
        }

        /**
         * Short hand to check IS_AUTHENTICATED_FULLY
         * @return bool
         */
        public static function isLogged()
        {
            return self::granted('IS_AUTHENTICATED_FULLY');
        }
    }
}