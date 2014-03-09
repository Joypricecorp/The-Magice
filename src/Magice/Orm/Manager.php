<?php
namespace Magice\Orm {

    use Doctrine\Common\EventManager;
    use Doctrine\ORM\Configuration;
    use Doctrine\ORM\ORMException;
    use Doctrine\ORM\EntityManager;
    use Doctrine\DBAL\Connection;

    class Manager extends EntityManager
    {
        /**
         * Factory method to create EntityManager instances.
         *
         * @param mixed         $conn         An array with the connection parameters or an existing Connection instance.
         * @param Configuration $config       The Configuration instance to use.
         * @param EventManager  $eventManager The EventManager instance to use.
         *
         * @return EntityManager The created EntityManager.
         * @throws \InvalidArgumentException
         * @throws ORMException
         */
        public static function create($conn, Configuration $config, EventManager $eventManager = null)
        {
            if (!$config->getMetadataDriverImpl()) {
                throw ORMException::missingMappingDriverImpl();
            }

            switch (true) {
                case (is_array($conn)):
                    $conn = \Doctrine\DBAL\DriverManager::getConnection(
                        $conn,
                        $config,
                        ($eventManager ? : new EventManager())
                    );
                    break;

                case ($conn instanceof Connection):
                    if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
                        throw ORMException::mismatchedEventManager();
                    }
                    break;

                default:
                    throw new \InvalidArgumentException("Invalid argument: " . $conn);
            }

            return new self($conn, $config, $conn->getEventManager());
        }

        public function rollback($checkTransactionActive = false)
        {
            // be carefull when use $checkTransactionActive and nested transaction (un-tested)
            if ($checkTransactionActive && !$this->getConnection()->isTransactionActive()) {
                return;
            }

            parent::rollback();
        }
    }
}