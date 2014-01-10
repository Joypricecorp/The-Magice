<?php
namespace Magice\Session\Handler {

    use Magice\Service\ContainerBasicInterface;
    use Magice\Service\ContainerAwareInterface;

    class PdoService implements ContainerAwareInterface
    {
        /**
         * @var ContainerBasicInterface
         */
        protected $container;

        /**
         * set Container
         *
         * @param ContainerBasicInterface $container
         *
         * @return ContainerBasicInterface
         */
        public function setContainer(ContainerBasicInterface $container)
        {
            $this->container = $container;
        }

        /**
         * Get PDO connection
         * @return \PDO|\Doctrine\DBAL\Driver\PDOConnection
         */
        public function getPdo()
        {
            /**
             * @var \Doctrine\DBAL\Connection $conn
             */
            return $this->container
                ->get('doctrine')
                ->getConnection($this->container->getParameter('magice.session.connection'))
                ->getWrappedConnection();
        }
    }
}