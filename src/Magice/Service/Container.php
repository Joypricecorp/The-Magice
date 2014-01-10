<?php
namespace Magice\Service {

    use Symfony\Component\DependencyInjection\ContainerInterface;
    use Symfony\Component\DependencyInjection\ScopeInterface;

    /**
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     */

    /**
     * @method getParameter($name);
     * @method hasParameter($name);
     * @method setParameter($name, $value);
     * @method enterScope($name);
     * @method leaveScope($name);
     * @method addScope(ScopeInterface $scope);
     * @method hasScope($name);
     * @method isScopeActive($name);
     */
    class Container implements ContainerBasicInterface
    {
        /**
         * @var ContainerInterface
         */
        protected $container;

        /**
         * @var array $parameters Services parameters
         */
        public $parameters = array();

        /**
         * Constructor
         *
         * @param ContainerInterface $container Inject the container
         */
        function __construct(ContainerInterface $container)
        {
            $this->container = $container;

            // copy services parameters
            $this->parameters = $this->parameters();

            // setup service resolver
            Resolver::setupContainer($this);
        }

        /**
         * Magice method call to Symfony container methods
         * @todo remove
         *
         * @param string $name Method call name
         * @param array  $args List of arguments
         *
         * @return mixed
         */
        function __call($name, $args)
        {
            return call_user_func_array(array($this->container, $name), $args);
        }

        /**
         * Access Service of this container via property accessor
         *
         * @param string $name Parameter name
         *
         * @return mixed
         */
        function __get($name)
        {
            return $this->container->$name;
        }

        /**
         * Read all parameters of services
         * @return array
         * @see http://symfony.com/doc/current/cookbook/bundles/extension.html#parsing-the-configs-array
         */
        function parameters()
        {
            return $this->container->getParameterBag()->all();
        }

        public function set($id, $service, $scope = self::SCOPE_SHARED)
        {
            // @todo check definition exist prevent setter none defined service
            // @todo -cont: this popose to force to define servie before use it

            if (!is_int($scope)) {
                $scope = $scope ? self::SCOPE_SHARED : self::SCOPE_NEW;
            }

            return $this->container->set($id, $service, $scope);
        }

        public function get($id, $behavior = self::INVALID_REFERENCE_EXCEPTION)
        {
            if (strpos($id, ':') === 0) {
                $id = 'magice.service.' . str_replace(':', '', $id);
            }

            return $this->container->get($id, $behavior);
        }

        /**
         * Returns true if the given service is defined.
         *
         * @param string $id The service identifier
         *
         * @return Boolean true if the service is defined, false otherwise
         * @api
         */
        public function has($id)
        {
            return $this->container->has($id);
        }
    }
}