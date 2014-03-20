<?php
/**
 * This file is part of MagiceUikitBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\UikitBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class MagiceUikitExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services/form.xml');
        $loader->load('services/twig.xml');
        //$loader->load('services/session.xml');
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
        $configs = $container->getExtensionConfig($this->getAlias());
        $config  = $this->processConfiguration(new Configuration(), $configs);

        // Configure Twig if TwigBundle is activated and the option
        // "magice_uikit.auto_configure.twig" is set to TRUE (default value).
        if (true === isset($bundles['TwigBundle']) && true === $config['auto_configure']['twig']) {
            $this->configureTwigBundle($container);
        }
    }

    /**
     * @param ContainerBuilder $container The service container
     *
     * @return void
     */
    protected function configureTwigBundle(ContainerBuilder $container)
    {
        foreach (array_keys($container->getExtensions()) as $name) {
            switch ($name) {
                case 'twig':
                    $container->prependExtensionConfig(
                        $name,
                        array(
                            'form' => array('resources' => array('MagiceUikitBundle:Form:uikit.html.twig'))
                        )
                    );
                    break;
            }
        }
    }
}
