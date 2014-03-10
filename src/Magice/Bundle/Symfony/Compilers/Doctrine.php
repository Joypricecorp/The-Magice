<?php
namespace Magice\Bundle\Symfony\Compilers {

    use Symfony\Component\DependencyInjection\Reference,
        Symfony\Component\DependencyInjection\Definition,
        Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

    use Doctrine\Bundle\DoctrineBundle\Registry;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Doctrine implements CompilerPassInterface
    {
        public function process(ContainerBuilder $builder)
        {
            $builder->setParameter('doctrine.orm.entity_manager.class', 'Magice\Orm\Manager');
            $builder->getDefinition('doctrine.orm.entity_manager.abstract')
                ->setClass('Magice\Orm\Manager')
                ->setFactoryClass('Magice\Orm\Manager')
            ;

            /**
             * @var Registry $dc
             */
            $dc    = $builder->get('doctrine');
            $names = $dc->getManagerNames();
            foreach ($names as $name => $id) {
                $config = $builder->getDefinition(sprintf('doctrine.orm.%s_configuration', $name));

                // replace setRepositoryFactory
                if ($config->hasMethodCall('setRepositoryFactory')) {
                    $config->removeMethodCall('setRepositoryFactory');
                }

                $factory = new Definition('Magice\Orm\RepositoryFactory');
                $factory->addMethodCall('setContainer', array(new Reference('service_container')));
                $factory->setPublic(false);

                $config->addMethodCall('setRepositoryFactory', array($factory));
            }
        }
    }
}