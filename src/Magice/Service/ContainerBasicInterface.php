<?php
namespace Magice\Service {

    use Symfony\Component\DependencyInjection\ContainerInterface;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    interface ContainerBasicInterface
    {
        const INVALID_REFERENCE_EXCEPTION = 1;
        const INVALID_REFERENCE_NULL      = 2;
        const INVALID_REFERENCE_IGNORE    = 3;

        const SCOPE_SHARED  = ContainerInterface::SCOPE_CONTAINER;
        const SCOPE_NEW     = ContainerInterface::SCOPE_PROTOTYPE;
        const SCOPE_REQUEST = 'request';

        public function get($id, $behavior = self::INVALID_REFERENCE_EXCEPTION);

        public function set($id, $service, $scope = self::SCOPE_SHARED);

        public function has($id);
    }
}