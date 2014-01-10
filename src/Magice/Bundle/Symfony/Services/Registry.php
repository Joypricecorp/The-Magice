<?php
namespace Magice\Bundle\Symfony\Services {

    use Magice\Service\ContainerBasicInterface,
        Magice\Service\ContainerAwareInterface,
        Magice\Registry\RegistryReadOnly;

    /**
     * Class Registry
     *
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Registry extends RegistryReadOnly implements ContainerAwareInterface
    {
        /**
         * @var ContainerBasicInterface $container
         */
        public $container;

        /**
         * Set service container
         *
         * @param ContainerBasicInterface $container
         *
         * @return void
         */
        public function setContainer(ContainerBasicInterface $container)
        {
            $this->container = $container;

            $this->parameters = $container->parameters;
        }
    }
}