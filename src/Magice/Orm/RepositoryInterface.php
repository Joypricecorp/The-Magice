<?php
namespace Magice\Orm {

    use Doctrine\Common\Persistence\ObjectRepository;
    use Pagerfanta\Pagerfanta;

    /**
     * Model repository interface.
     */
    interface RepositoryInterface extends ObjectRepository
    {
        /**
         * Create a new resource
         * @return mixed
         */
        public function createNew();

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