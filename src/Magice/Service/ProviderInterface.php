<?php
namespace Magice\Service {

    /**
     * interface ProviderInterface
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    interface ProviderInterface
    {
        /**
         * Register service/service provider to Service container builder
         *
         * @param Builder $builder
         *
         * @return Builder
         */
        public function register(Builder $builder);
    }
}