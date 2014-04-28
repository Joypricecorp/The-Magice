<?php
namespace Magice\Paginator {

    use Doctrine\Common\Collections\ArrayCollection;
    use Symfony\Component\HttpFoundation\Response;

    class Resource
    {
        /**
         * @var array
         */
        public $data;

        /**
         * @var Paging
         */
        public $paging;

        /**
         * @var string
         */
        public $key;

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

            $this->key    = $key;
            $this->paging = new Paging(
                $pager->getNbResults(),
                $collection->count(),
                $pager->getNbPages(),
                $pager->getCurrentPage(),
                $pager->getMaxPerPage()
            );

            $this->data = array(
                $key     => $collection,
                // make ensure all serializer 3pt proper work
                // make it to array
                'paging' => (array) $this->paging
            );
        }

        /**
         * @param Response $response
         */
        public function setContentRangHeader(Response $response)
        {
            $size  = $this->paging->size;
            $count = $this->paging->count;
            $page  = $this->paging->page;
            $total = $this->paging->total;

            $response->headers->set(
                'Content-Range',
                sprintf('%s-%s/%s', (($page - 1) * $size), ($page * $count) - 1, $total)
            );
        }
    }
}