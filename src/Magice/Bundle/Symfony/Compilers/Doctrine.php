<?php
namespace Magice\Bundle\Symfony\Compilers {

    use Symfony\Component\DependencyInjection\Reference,
        Symfony\Component\DependencyInjection\Definition,
        Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

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
            //$entity_manager = $builder->findDefinition('doctrine.orm.entity_manager');
            $em_name   = $builder->get('doctrine')->getDefaultManagerName();
            $em_config = $builder->getDefinition(sprintf('doctrine.orm.%s_configuration', $em_name));

            // replace setRepositoryFactory
            if ($em_config->hasMethodCall('setRepositoryFactory')) {
                $em_config->removeMethodCall('setRepositoryFactory');
            }

            $repo_factory = new Definition('Magice\Orm\Doctrine\meta\common\RepositoryFactory');
            $repo_factory->addMethodCall('setContainer', array(new Reference('magice.service.container')));
            $repo_factory->setPublic(false);

            $em_config->addMethodCall('setRepositoryFactory', array($repo_factory));
        }
    }
}