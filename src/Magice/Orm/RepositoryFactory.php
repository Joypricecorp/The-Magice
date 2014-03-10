<?php
namespace Magice\Orm {

    use Doctrine\ORM\Repository\DefaultRepositoryFactory;
    use Doctrine\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\DependencyInjection\Container;
    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerInterface;

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
         * @var ContainerInterface $container
         */
        protected $container;

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
            $metadata   = $entityManager->getClassMetadata($entityName);
            $repository = $metadata->customRepositoryClassName;

            if ($repository === null) {
                $configuration = $entityManager->getConfiguration();
                $repository    = $configuration->getDefaultRepositoryClassName();
            }

            return new $repository($entityManager, $metadata, $this->container);
        }

        /**
         * Sets the Container.
         *
         * @param ContainerInterface|null $container A ContainerInterface instance or null
         *
         * @api
         */
        public function setContainer(ContainerInterface $container = null)
        {
            $this->container = $container;
        }
    }
}