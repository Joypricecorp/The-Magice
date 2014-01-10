<?php
namespace Magice\Service {

    /**
     * Interface ContainerAwareInterface
     */
    interface ContainerAwareInterface
    {
        /**
         * set Container
         *
         * @param ContainerBasicInterface $container
         *
         * @return ContainerBasicInterface
         */
        public function setContainer(ContainerBasicInterface $container);
    }
}