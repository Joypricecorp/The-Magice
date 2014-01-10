<?php
namespace Magice\Utils {

    use Magice\Service\ContainerBasicInterface;
    use Magice\Service\ContainerAwareInterface;

    /**
     * Class CheckingService What do you want to check?
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class CheckingService implements ContainerAwareInterface
    {
        /**
         * @var ContainerBasicInterface $container
         */
        protected $container;

        public function setContainer(ContainerBasicInterface $container)
        {
            $this->container = $container;
        }

        public function method($key)
        {
            return $this->container->get('request')->isMethod($key);
        }

        /**
         * @param string $key json|jsonp|html|raw
         *
         * @return bool|null
         */
        public function response($key)
        {
            switch (strtolower($key)) {
                case 'json':
                case 'jsonp':
                case 'html':
                case 'raw':
                case 'rawhtml':
                    return $this->container->get('request')->getRequestFormat() == $key;
            }

            return null;
        }

        public function requestMaster(){
            $this->container->get('request');
        }
        public function requestSub(){}

        public function user($user){}
    }
}