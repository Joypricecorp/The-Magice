<?php
namespace Magice\Paginator {

    use Doctrine\Common\Collections\ArrayCollection;

    class Resource
    {
        /**
         * @var array
         */
        private $data;

        /**
         * @var string
         */
        private $key;

        /**
         * @param Pager  $pager
         * @param string $key
         */
        public function __construct(Pager $pager, $key = 'data')
        {
            // Dirty hack to make serializer works (as expected?, at least like it used to)
            // @see https://github.com/schmittjoh/JMSSerializerBundle/commit/b8d0072bac712df31f6ada43b3ab0d44909a6a95
            $collection = new ArrayCollection();
            foreach ($pager->getIterator() as $item) {
                $collection->add($item);
            }

            $this->key = $key;

            $this->data = array(
                $key    => $collection,
                'pagin' => array(
                    /**
                     * @key int total   - Total of this result
                     * @key int count   - Total to display of this result
                     * @key int pages   - Total pages
                     * @key int page    - Current page
                     * @key int size    - Page size
                     */
                    'total' => $pager->getNbResults(),
                    'count' => $collection->count(),
                    'pages' => $pager->getNbPages(),
                    'page'  => $pager->getCurrentPage(),
                    'size'  => $pager->getMaxPerPage(),
                )
            );
        }

        /**
         * @return string
         */
        public function getKey()
        {
            return $this->key;
        }

        /**
         * @return array
         */
        public function getData()
        {
            return $this->data;
        }
    }
}