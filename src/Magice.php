<?php
/*
 * This file is part of the The Magice Project.
 *
 * @copyright   2012-2014 ツ Joyprice corporation Ltd.
 * @license     http://www.joyprice.org/license
 * @link        http://www.joyprice.org/themagice
 */

namespace {

    use Magice\Service\Builder,
        Magice\Service\Container;

    /**
     * The Magice Singleton Base
     * 
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Magice
    {
        /**
         * @var string Project name
         */
        const NAME = 'The Magice';

        /**
         * @var string Project namespace
         */
        const NAME_SPACE = 'magice';

        /**
         * @var string Project version
         */
        const VERSION = '1.0-alpha';

        /**
         * @var string
         */
        const VAR_UNDEF = 'magice.VAR_UNDEF';

        /**
         * @var string Service Interface: ProviderInterface
         */
        const SI_PROVIDER = 'Magice\Service\ProviderInterface';

        /**
         * @var string Service Interface: ContainerAwareInterface
         */
        const SI_CONTAINER_AWARE = 'Magice\Service\ContainerAwareInterface';

        /**
         * @var Container
         */
        protected static $container;

        /**
         * @var Builder
         */
        protected static $builder;

        /**
         * @var bool $booted Magice Booted
         */
        protected static $booted = false;

        /**
         * Magice build pharse
         *
         * @param Builder $builder
         */
        public static function build(Builder $builder)
        {
            self::$builder = $builder;
        }

        /**
         * Setup the magice
         *
         * @param Container $container
         */
        public static function boot(Container $container)
        {
            // set container
            static::container($container);
            static::$booted = true;
        }

        /**
         * Check magice is booted
         * 
         * @return bool
         */
        public static function booted()
        {
            return self::$booted;
        }

        /**
         * get/set service container
         *
         * @param Container $container
         *
         * @return Container
         */
        public static function container(Container $container = null)
        {
            if ($container) {
                static::$container = $container;
            }

            return static::$container;
        }

        /**
         * Register service (Dependency Injection)
         *
         * @param string $id
         * @param string $args
         * @param bool   $shared
         *
         * @return mixed|void
         */
        public static function di($id, $args = self::VAR_UNDEF, $shared = true)
        {
            if (static::$container) {
                if ($args === self::VAR_UNDEF) {
                    $args = null;
                }

                return static::$container->set($id, $args, $shared);
            } else {

                // Register service provider
                if ($args === self::VAR_UNDEF
                    && class_exists($id)
                    && in_array(self::SI_PROVIDER, class_implements($id))
                ) {
                    /**
                     * @var Magice\Service\ProviderInterface $cls
                     */
                    $cls = new $id();
                    $cls->register(static::$builder);
                    return null;
                }

                return call_user_func_array(array(static::$builder, 'register'), func_get_args());
            }
        }

        /**
         * Get service from container (Dependency Injection Getter)
         *
         * @param string $id Service Id
         *
         * @return mixed
         */
        public static function dig($id)
        {
            return static::$container->get($id);
        }
    }

    /**
     * Alias of Magice
     */
    class mg extends Magice {}

    /**
     * Shorthand to mg::dig('service_id');
     *
     * @param string $id Service Id
     *
     * @return mixed
     */
    function dig($id)
    {
        return mg::dig($id);
    }
    
    // TODO: move to single file
    if(!function_exists('cd')){
        function cd()
        {
            \Kint::$maxLevels = 10;
            \Kint::dump(func_get_args());

            $target = debug_backtrace()[0];
            print_r('<small>');
            print_r('File: ');
            print_r($target['file']);
            print_r(' Line: ');
            print_r($target['line']);
            print_r('</small>');
        }
    }

    if(!function_exists('cs')){
        function cs()
        {
            echo '<pre>';
            print_r(func_get_args());
            echo '<pre>';

            $target = debug_backtrace()[0];
            print_r('<small>');
            print_r('File: ');
            print_r($target['file']);
            print_r(' Line: ');
            print_r($target['line']);
            print_r('</small>');
        }
    }
}