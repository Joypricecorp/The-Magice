<?php
namespace Magice\Bundle\Symfony\Compilers {

    use Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

    /**
     * Class Session
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Session implements CompilerPassInterface
    {
        public function process(ContainerBuilder $builder)
        {
            $builder->setParameter(
                'session.attribute_bag.class',
                'Magice\Session\Attribute\DotNamespacedAttributeBag'
            );
        }
    }
}