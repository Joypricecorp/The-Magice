<?php
namespace Magice\Bundle\Symfony\Compilers {

    use Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

    /**
     * Class Twig
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Twig implements CompilerPassInterface
    {
        public function process(ContainerBuilder $builder)
        {
            // Replace template engine class
            $mode = $builder->getParameter('kernel.debug') ? 'debug.' : '';
            $builder->setParameter(
                sprintf('%stemplating.engine.twig.class', $mode),
                'Magice\Bundle\Symfony\Services\TwigEngine'
            );
        }
    }
}