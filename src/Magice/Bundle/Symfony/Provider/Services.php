<?php
namespace Magice\Bundle\Symfony\Provider {

    use Magice\Service\Builder,
        Symfony\Component\DependencyInjection\Reference;

    /**
     * Class Services
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Services
    {
        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Magice Pdo Session Service
         * Id: magice.service.session.pdoservice
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * class: Magice\Session\Handler\PdoService
         *      arguments: Auto set by Magice\Service\ContainerAwareInterface
         */
        public static function SessionPdoService(Builder $builder)
        {
            $builder->register(
                'magice.service.session.pdoservice',
                'Magice\Session\Handler\PdoService'
            );
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Pdo Session Manager
         * Id: magice.session.handler.pdo
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.session.handler.pdo:
         *          class       : Magice\Session\Handler\Pdo
         *          arguments   : ["@magice.service.session.pdoservice", "%magice.session.table%"]
         */
        public static function SessionHandlerPdo(Builder $builder)
        {
            $builder
                ->register('magice.service.session.handler.pdo', 'Magice\Session\Handler\Pdo')
                ->addArgument(new Reference('magice.service.session.pdoservice'))
                ->addArgument('%magice.session.table%');
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Doctrine ORM
         * Id: magice.service.doctrine.namingstrategy
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.doctrine.namingstrategy
         *          class: Magice\Orm\Doctrine\meta\common\NamingStrategy
         */
        /*public static function DoctrineNamingStrategy(Builder $builder)
        {
            $builder
                ->register(
                    'magice.service.doctrine.namingstrategy',
                    'Magice\Orm\Doctrine\meta\common\NamingStrategy'
                );
        }*/

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Twig Extesion
         * Id: magice.service.twig.extension
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.twig.extension
         *          class: Magice\Bundle\Symfony\Services\Twig
         *          tags:
         *              - { name: twig.extension }
         */
        public static function Twig(Builder $builder)
        {
            $builder
                ->register(
                    'magice.service.twig.extension',
                    'Magice\Bundle\Symfony\Services\Twig'
                )
                ->addTag('twig.extension');
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Registry
         * Id: magice.service.registry
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.registry
         *          class: Magice\Bundle\Symfony\Services\Registry
         * @note auto add addMethodCall('setContainer', array('%magice.service.container%')); with it's interface
         */
        public static function Registry(Builder $builder)
        {
            $builder
                ->register(
                    'magice.service.registry',
                    'Magice\Bundle\Symfony\Services\Registry'
                );
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Mvc View
         * Id: magice.service.mvc.view
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.mvc.view
         *          class: Magice\Mvc\View
         *          arguments: ['%magice.output.layout%']
         */
        public static function View(Builder $builder)
        {
            $builder
                ->register('magice.service.mvc.view', 'Magice\Mvc\View')
                ->addArgument('%magice.output.layout%')
                ->addArgument(new Reference('magice.service.mvc.view.asset'));
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Mvc Asset
         * Id: magice.service.mvc.view.asset
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.mvc.view.asset
         *          class: Magice\Mvc\View\Asset\Manager
         *          arguments: ['@magice.asset.library']
         */
        public static function Asset(Builder $builder)
        {
            $builder
                ->register('magice.service.mvc.view.asset', 'Magice\Mvc\View\Asset\Manager')
                ->addArgument(new Reference('magice.service.mvc.view.asset.library'));
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Mvc Asset Library
         * Id: magice.service.mvc.view.asset.library
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.mvc.view.asset.library
         *          class: Magice\Mvc\View\Asset\Library
         *          arguments: ['%kernel.root_dir%', '%kernel.debug%']
         */
        public static function AssetLibrary(Builder $builder)
        {
            $builder
                ->register('magice.service.mvc.view.asset.library', 'Magice\Mvc\View\Asset\Library')
                ->addArgument('')
                ->addArgument('%kernel.debug%')
                ;
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Checking Service
         * Id: magice.service.utils.checking
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.utils.checking
         *          class: Magice\Utils\CheckingService
         */
        public static function Checking(Builder $builder)
        {
            $builder->register(
                'magice.service.utils.checking',
                'Magice\Utils\CheckingService'
            );
        }

        /**
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * Container
         * Id: magice.service.container
         * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
         * YAML -
         * services:
         *      magice.service.container
         *          class       : Magice\Service\Container
         *          arguments   : ['@service_container']
         */
        public static function Container(Builder $builder)
        {
            $builder
                ->register(
                    'magice.service.container',
                    'Magice\Service\Container'
                )
                ->addArgument(new Reference('service_container'));
        }
    }
}