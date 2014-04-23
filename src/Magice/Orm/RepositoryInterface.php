<?php
namespace Magice\Orm {

    use Doctrine\Common\Persistence\ObjectRepository;
    use Doctrine\ORM\EntityManager;
    use Symfony\Component\DependencyInjection\ContainerInterface;
    use Pagerfanta\Pagerfanta;

    /**
     * Model repository interface.
     */
    interface RepositoryInterface extends ObjectRepository
    {
        /**
         * Factory for create reposotiry as service without add to doctrine default facotry
         *
         * @param EntityManager      $manager
         * @param string             $entityClass
         * @param ContainerInterface $container
         *
         * @return Repository|ObjectRepository
         * @usage
         * <service id="jp.repository.country" class="Magice\Orm\RepositoryFactory" factory-class="Magice\Orm\RepositoryFactory" factory-method="createDefault">
         *      <argument type="service" id="doctrine.orm.default_entity_manager"/>
         *      <argument>%jp.class.entity.geo.country%</argument>
         *      <argument type="service" id="service_container [optional]"/>
         * </service>
         */
        public static function create(EntityManager $manager, $entityClass, ContainerInterface $container = null);

        /**
         * Get paginated collection
         *
         * @param array $criteria
         * @param array $orderBy
         *
         * @return Pagerfanta
         */
        public function createPaginator(array $criteria = null, array $orderBy = null);
    }
}