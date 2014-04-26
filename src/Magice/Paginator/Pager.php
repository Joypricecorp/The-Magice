<?php
namespace Magice\Paginator {

    use Pagerfanta\Pagerfanta;

    class Pager extends Pagerfanta
    {
        /**
         * @param string $key
         *
         * @return Resource
         */
        public function getResource($key = 'data')
        {
            return new Resource($this, $key);
        }
    }
}