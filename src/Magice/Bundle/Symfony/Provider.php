<?php
namespace Magice\Bundle\Symfony {

    use Magice\Service\Builder,
        Magice\Service\ProviderInterface;

    /**
     * Class Provider
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Provider implements ProviderInterface
    {
        /**
         * Register service/service provider to Service container builder
         *
         * @param Builder $builder
         *
         * @return Builder
         */
        public function register(Builder $builder)
        {
            foreach (static::getServiceClasses() as $className) {
                $cls = new \ReflectionClass($className);
                foreach ($cls->getMethods(\ReflectionMethod::IS_STATIC) as $method) {
                    call_user_func_array(array(
                        $className,
                        $method->getName()
                    ), array($builder));
                }
            }
        }

        public static function getServiceClasses()
        {
            return array(
                'Magice\Bundle\Symfony\Provider\Events',
                'Magice\Bundle\Symfony\Provider\Services'
            );
        }
    }
}