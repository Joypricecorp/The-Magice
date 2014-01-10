<?php
namespace Magice\Bundle\Symfony {

    use Magice,
        Magice\Utils\Arrays;
    use Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface,
        Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension,
        Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
        Symfony\Component\DependencyInjection\Loader\PhpFileLoader as FileLoader;

    /**
     * Class SymfonyExtension
     * @package     Magice\Bundle\Symfony
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Extension extends SymfonyExtension implements PrependExtensionInterface
    {
        /**
         * @param ContainerBuilder $builder
         *
         * @return void
         * @see http://symfony.com/doc/master/cookbook/bundles/prepend_extension.html
         */
        public function prepend(ContainerBuilder $builder)
        {
            $this->bindTheMagiceConfigToParam($builder);
        }

        /**
         * Responds to the app.config configuration parameter.
         *
         * @param array            $configs
         * @param ContainerBuilder $builder
         */
        public function load(array $configs, ContainerBuilder $builder)
        {
            // register services from provider
            Magice::di('Magice\Bundle\Symfony\Provider');

            // set input alias
            $builder->setAlias('input', 'request');

            // now not any want to do here
            //$config = new Registry($this->precesedConfigurations($configs, $builder));
        }

        /**
         * @param array            $configs
         * @param ContainerBuilder $builder
         *
         * @return array
         */
        public function precesedConfigurations(array $configs, ContainerBuilder $builder)
        {
            $config = $this->getConfiguration($configs, $builder);
            return $this->processConfiguration($config, $configs);
        }

        /**
         * Apply Magice `config` use as base `parameters`
         *
         * @param ContainerBuilder $builder
         */
        protected function bindTheMagiceConfigToParam(ContainerBuilder $builder)
        {
            /**
             * build array param into . (dot) string key style
             * e.g. 'magice.dbo.[session].[host]'
             *
             * @var \Magice\Bundle\Symfony\Extension $extends apply default config from magice extension
             */
            $configs = $this->precesedConfigurations(
                $builder->getExtensionConfig(Magice::NAME_SPACE),
                $builder
            );

            // set all config to root scope
            $builder->setParameter(Magice::NAME_SPACE, $configs);

            // make paths
            $arrays = Arrays::paths($configs, '.');

            // build
            foreach ($arrays as $arr => $value) {
                $builder->setParameter(Magice::NAME_SPACE . '.' . $arr, $value);
            }
        }

        public function getAlias()
        {
            return Magice::NAME_SPACE;
        }
    }
}