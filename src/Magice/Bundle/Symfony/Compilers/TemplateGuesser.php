<?php
namespace Magice\Bundle\Symfony\Compilers {

    use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
    use Symfony\Component\DependencyInjection\ContainerBuilder;

    /**
     * Class TemplateGuesser
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class TemplateGuesser implements CompilerPassInterface
    {
        public function process(ContainerBuilder $builder)
        {
            // Replace template listener class
            $builder->setParameter(
                'sensio_framework_extra.view.guesser.class',
                'Magice\Bundle\Symfony\Services\TemplateGuesser'
            );
        }
    }
}