<?php
namespace Magice\Bundle\AssetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Form implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $bd
     *
     * @api
     */
    public function process(ContainerBuilder $bd)
    {
        $bd->getDefinition('misd_phone_number.form.type')
            ->setClass('Magice\Form\Type\Phone');
    }
}