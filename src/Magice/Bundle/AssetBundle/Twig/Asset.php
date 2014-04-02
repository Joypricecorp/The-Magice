<?php
/**
 * This file is part of MagiceAssetBundle.
 * (c) 2014 by ツ Liverbool <nukboon@gmail.com>
 */

namespace Magice\Bundle\AssetBundle\Twig;

use Magice\Asset\Importer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Asset extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     * @return string The extension name
     */
    public function getName()
    {
        return 'magice_asset_twig';
    }

    public function getGlobals()
    {
        $webpath = $this->container->get('router')->getContext()->getBaseUrl();
        $webpath = preg_replace('/app_dev\.php|app\.php/i', '', $webpath);

        if ($webpath[strlen($webpath) - 1] == '/') {
            $webpath = substr($webpath, 0, -1);
        }

        Importer::setParameter('web', $webpath);

        return array(
            'import' => new Importer()
        );
    }
}