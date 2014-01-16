<?php
namespace Magice\Application {

    use Magice\Utils\Arrays,
        Magice\Service\Builder,
        Magice\Service\Loader\Yaml,
        Magice\Registry\Registry,
        Magice\Bundle\Symfony\Bundle,
        Magice\Application\Configurator\Yaml as ConfiguratorYaml;
    use Symfony\Component\HttpKernel\Kernel,
        Symfony\Component\HttpKernel\Config\FileLocator,
        Symfony\Component\Config\Loader\LoaderResolver,
        Symfony\Component\Config\Loader\LoaderInterface,
        Symfony\Component\Config\Loader\DelegatingLoader,
        Symfony\Component\DependencyInjection\ContainerInterface,
        Symfony\Component\DependencyInjection\Loader\ClosureLoader,
        Symfony\Component\DependencyInjection\Loader\IniFileLoader,
        Symfony\Component\DependencyInjection\Loader\PhpFileLoader,
        Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
        Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;

    abstract class Application extends Kernel
    {
        /**
         * Returns the kernel parameters.
         * @return array An array of kernel parameters
         * @override to add kernel.src
         */
        protected function getKernelParameters()
        {
            $bundles = array();
            foreach ($this->bundles as $name => $bundle) {
                $bundles[$name] = get_class($bundle);
            }

            return array_merge(
                array(
                    'kernel.root_dir'        => $this->rootDir,
                    'kernel.environment'     => $this->environment,
                    'kernel.debug'           => $this->debug,
                    'kernel.name'            => $this->name,
                    'kernel.cache_dir'       => $this->getCacheDir(),
                    'kernel.logs_dir'        => $this->getLogDir(),
                    'kernel.bundles'         => $bundles,
                    'kernel.charset'         => $this->getCharset(),
                    'kernel.container_class' => $this->getContainerClass(),
                    // BOON ADD @note this not stable but useful if you not change src folder
                    'kernel.src'             => str_replace($this->name, 'src', $this->rootDir),
                    'kernel.suffix'          => $this->getEnvSuffix()
                ),
                $this->getEnvParameters()
            );
        }

        public function getEnvSuffix()
        {
            switch (strtolower($this->environment)) {
                case 'dev':
                    return '-dev';

                case 'test':
                    return '-test';
                
                default:
                    return '';
            }
        }

        /**
         * Get configuration file path
         * @return string
         */
        public function getConfigFile()
        {
            return $this->rootDir . '/config/config_' . $this->environment . '.yml';
        }

        /**
         * Returns an array of bundles to registers.
         * @return array|\Symfony\Component\HttpKernel\Bundle\BundleInterface[]
         * @api
         */
        public function registerBundles()
        {
            $config = new ConfiguratorYaml($this);
            $config->load($this->getConfigFile());
            $bundles = array_values($config->getBundles());
            //$bundles = $this->bundles();

            // add Magice Bundle
            array_unshift($bundles, new Bundle());
            return $bundles;
        }

        /**
         * Loads the container configuration
         *
         * @param LoaderInterface $loader A LoaderInterface instance
         *
         * @api
         */
        public function registerContainerConfiguration(LoaderInterface $loader)
        {
            $loader->load($this->getConfigFile());
        }

        /**
         * Gets a new Builder instance used to build the service container.
         * @return Builder
         */
        protected function getContainerBuilder()
        {
            // use Magice\Registry & Magice\Service\Builder
            $container = new Builder(new Registry($this->getKernelParameters()));

            if (class_exists('ProxyManager\Configuration')) {
                $container->setProxyInstantiator(new RuntimeInstantiator());
            }

            return $container;
        }

        /**
         * Override to modifie some Magice reqiurement
         *
         * @param ContainerInterface $container
         *
         * @return DelegatingLoader
         * @see https://github.com/symfony/symfony/issues/9138
         */
        protected function getContainerLoader(ContainerInterface $container)
        {
            $locator  = new FileLocator($this);
            $resolver = new LoaderResolver(array(
                new XmlFileLoader($container, $locator),
                new Yaml($container, $locator),
                new IniFileLoader($container, $locator),
                new PhpFileLoader($container, $locator),
                new ClosureLoader($container),
            ));

            return new DelegatingLoader($resolver);
        }

        //abstract public function bundles();
    }
}