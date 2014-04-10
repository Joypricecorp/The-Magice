<?php
namespace Magice\Bundle\AssetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Doctrine implements CompilerPassInterface
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
        $key = 'doctrine.dbal.connection_factory.types';

        $bd->setParameter(
            $key,
            array_merge(
                array(
                    'phone' => array(
                        'class'     => 'Magice\Orm\Doctrine\Type\Phone',
                        'commented' => true
                    )
                ),
                $bd->getParameter($key)
            )
        );

    }
}