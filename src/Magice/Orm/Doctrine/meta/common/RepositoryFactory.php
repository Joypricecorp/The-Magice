<?php
namespace Magice\Orm\Doctrine\meta\common {

    use Doctrine\ORM\Repository\DefaultRepositoryFactory;
    use Doctrine\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use Magice\Service\ContainerBasicInterface;
    use Magice\Service\ContainerAwareInterface;

    /**
     * This factory is used to create default repository objects for entities at runtime.
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class RepositoryFactory extends DefaultRepositoryFactory implements ContainerAwareInterface
    {
        /**
         * @var ContainerBasicInterface $container
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
         * Create a new repository instance for an entity class.
         *
         * @param \Doctrine\ORM\EntityManagerInterface $entityManager The EntityManager instance.
         * @param string                               $entityName    The name of the entity.
         *
         * @return \Doctrine\Common\Persistence\ObjectRepository
         */
        protected function createRepository(EntityManagerInterface $entityManager, $entityName)
        {
            $metadata            = $entityManager->getClassMetadata($entityName);
            $repositoryClassName = $metadata->customRepositoryClassName;

            if ($repositoryClassName === null) {
                $configuration       = $entityManager->getConfiguration();
                $repositoryClassName = $configuration->getDefaultRepositoryClassName();
            }

            return new $repositoryClassName($entityManager, $metadata, $this->container);
        }
    }
}