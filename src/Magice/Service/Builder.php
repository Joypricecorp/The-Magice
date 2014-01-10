<?php
namespace Magice\Service {

    use Magice;
    use Symfony\Component\DependencyInjection\Definition,
        Symfony\Component\DependencyInjection\Reference,
        Symfony\Component\DependencyInjection\ContainerBuilder,
        Symfony\Component\DependencyInjection\ContainerInterface;

    /**
     * Class Container
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */
    class Builder extends ContainerBuilder
    {
        const SCOPE_SHARED  = ContainerInterface::SCOPE_CONTAINER;
        const SCOPE_NEW     = ContainerInterface::SCOPE_PROTOTYPE;
        const SCOPE_REQUEST = 'request';

        /**
         * Register a service
         *
         * @param string      $id
         * @param mixed       $definition
         * @param bool|string $scoped
         *
         * @return Definition|null;
         * @see  https://github.com/symfony/symfony/tree/master/src/Symfony/Component/DependencyInjection
         * @todo support $definition as Closure
         * @todo support $id as ServiceProvider
         */
        public function register($id, $definition = null, $scoped = self::SCOPE_REQUEST)
        {
            $scoped = is_int($scoped) ? $scoped : ($scoped ? self::SCOPE_SHARED : self::SCOPE_NEW);

            /**
             * Define by alias
             * ::register('newservice', '@service_exist');
             */
            if (strpos($definition, '@') === 0) {
                parent::register($id)->setScope($scoped);
                parent::setAlias($id, str_replace('@', '', $definition));
                return null;
            }

            /**
             * Define with instance
             * ::register('servicename', new instace()|object);
             */
            if (is_object($definition)) {
                parent::register($id, new Definition())
                    ->setSynthetic(true)
                    ->setScope($scoped);

                parent::set($id, $definition);
                return $definition;
            }

            /**
             * Define ContainerAwareInterface class implement
             * ::register('servicename', 'class')->.. normal define by not set addMethodCall of setContainer
             */
            if (class_exists($definition)
                && in_array(Magice::SI_CONTAINER_AWARE, class_implements($definition))
            ) {
                return parent::register($id, $definition)
                    ->addMethodCall('setContainer', array(new Reference('magice.service.container')));
            }

            /**
             * Define with Closure
             * ::register('servicename', function([type-hint]) {
             *      return new Class([type-hint]);
             * });
             */
            if ($definition instanceof \Closure) {
                // todo check type-hint of closure and pass to it, and end args with parent::
                parent::set($id, $definition($this), $scoped);

                return null;
            }

            /**
             * Defint with default
             * ::register('servicename', 'class')->...
             */
            return parent::register($id, $definition);

            // TODO advance pattern

            $method = null;

            if (preg_match('/::/', $definition)) {
                list($definition, $method) = explode('::', $definition);
            }

            // todo fine args of $definition/$method with (xx,xx)

            $args = array();
            if (is_string($definition)) {
                $class = $definition;
            } else {
                $class = $definition['class'];

                // if $key is int assume that you pass default value
                // if $key is string assume that you pass default value of specify key
                // @note if pass default value of arg that is array type you must pass 2 dimentions array
                $args = $definition['args'] ? : array();

                // only-one arg of construct can pass as any expect array
                if (!is_array($args)) {
                    $args = (array) $args;
                }
            }

            // find constructor args to build for support scs->setParameter()
            // it may need when you want pass parameter to construct arg on create instance
            $cls = new \ReflectionClass($class);
            $ctt = $method ? $cls->getMethod($method) : $cls->getConstructor();

            $_args = array();

            if ($ctt->getNumberOfParameters()) {
                /**
                 * @var \ReflectionParameter $pm
                 * // todo autoload class hint as param
                 */
                foreach ($ctt->getParameters() as $pm) {
                    try {
                        $value = $pm->getDefaultValue();
                    } catch (\ReflectionException $e) {
                        $value = null;
                    }

                    $_args[$pm->name] = $value;
                }
            }

            $dfn = $builder->register($id, $class)
                ->setScope(
                    is_int($scoped)
                        ? $scoped
                        : (
                    $scoped
                        ? self::SCOPE_SHARED
                        : self::SCOPE_NEW
                    )
                );

            if ($method) {
                $dfn->setFactoryClass($class)
                    ->setFactoryMethod($method);
            }

            $i = 0;
            foreach ($_args as $k => $v) {
                $key = sprintf('%s.%s', $id, $k);
                $dfn->addArgument('%' . $key . '%');

                if (isset($args[$i]) || isset($args[$k])) {
                    parent::setParameter($key, isset($args[$i]) ? $args[$i] : $args[$k]);
                } else {
                    parent::setParameter($key, $v);
                }

                $i++;
            }

            return $dfn;
        }

        public function uses($id)
        {
            $dfn  = parent::getDefinition($id);
            $args = $dfn->getArguments();

            $_args = func_get_args();
            array_shift($_args);

            $older = array();
            if (count($_args)) {
                foreach ($args as $i => $arg) {
                    $arg         = preg_replace('/%/', '', $arg);
                    $older[$arg] = parent::getParameter($arg);
                    parent::setParameter($arg, $_args[$i]);
                }
            }

            $service = parent::get($id);

            // reset param to default if not shared
            if (count($older) && $dfn->getScope() == self::SCOPE_NEW) {
                foreach ($older as $k => $v) {
                    parent::setParameter($k, $v);
                }
            }

            return $service;
        }
    }
}