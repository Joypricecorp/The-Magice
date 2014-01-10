<?php
namespace Magice\Bundle\Symfony {

    use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
        Symfony\Component\Config\Definition\Builder\NodeDefinition,
        Symfony\Component\Config\Definition\Builder\TreeBuilder,
        Symfony\Component\Config\Definition\ConfigurationInterface,
        Symfony\Component\Config\Definition\PrototypedArrayNode;

    /**
     * Class Configuration
     *
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     * @note        Automatic import with Symfony\Component\DependencyInjection\Extension\Extension::getConfiguration()
     *              must named Configuration and live in the same of Extension file.
     */
    class Configuration implements ConfigurationInterface
    {
        /**
         * Generates the configuration tree builder.
         * @return TreeBuilder The tree builder
         */
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode    = $treeBuilder->root('magice');

            $this->addSessionConfig($rootNode);
            $this->addVariableConfig($rootNode);

            return $treeBuilder;
        }

        protected function addVariableConfig(ArrayNodeDefinition $tree)
        {
            $tree
                ->children()
                    ->variableNode('locale')
                        ->info('Locale configuration')
                    ->end()
                    ->variableNode('security')
                        ->info('Security configuration')
                    ->end()
                    ->variableNode('dbo')
                        ->info('Database configuration')
                    ->end()
                    ->variableNode('mailer')
                        ->info('Mailer configuration')
                    ->end()
                    ->variableNode('entity')
                        ->info('Entity configuration')
                    ->end()
                    ->arrayNode('output')
                        ->info('Output configuration')
                        ->children()
                            ->scalarNode('layout')->defaultValue('@JP/Layout/Default/index.twig')->end()
                            ->scalarNode('jsonp_callback')->defaultValue('jsonp_callback')->end()
                            ->scalarNode('cdata_callback')->defaultValue('cdata_callback')->end()
                        ->end()
                    ->end()
                ->end();
        }

        protected function addSessionConfig(ArrayNodeDefinition $tree)
        {
            $tree
                ->children()
                    ->arrayNode('session')
                        ->info('Session configuration')
                        ->canBeEnabled()
                        ->children()
                            ->scalarNode('connection')->defaultValue('default')->end()
                            ->arrayNode('table')
                                ->children()
                                    ->scalarNode('name')->isRequired()->end()
                                    ->scalarNode('col_id')->defaultValue('ssn_id')->end()
                                    ->scalarNode('col_data')->defaultValue('ssn_data')->end()
                                    ->scalarNode('col_time')->defaultValue('ssn_time')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
        }
    }
}