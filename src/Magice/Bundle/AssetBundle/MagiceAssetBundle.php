<?php

namespace Magice\Bundle\AssetBundle;

use Magice\Bundle\AssetBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Container;

class MagiceAssetBundle extends Bundle
{
    /**
     * @var Container
     */
    protected $container;

    public function boot()
    {
        $cn = $this->container;
        \Magice\Asset\Configuration::setMode($cn->getParameter('kernel.debug'));
        \Magice\Asset\Configuration::setConfigurationFile($cn->getParameter('kernel.root_dir') . '/../assets.json');
    }

    public function build(ContainerBuilder $bd)
    {
        $bd->addCompilerPass(new Compiler\Doctrine());
    }
}
