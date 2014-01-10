<?php
namespace Magice\Application\Configurator {

    use Magice,
        Magice\Application\Application;
    use Symfony\Component\Yaml\Parser,
        Symfony\Component\Config\Loader\FileLoader,
        Symfony\Component\HttpKernel\Config\FileLocator;

    /**
     * Class Yaml
     * @copyright   2012-2014 ツ Joyprice corporation Ltd.
     * @license     http://www.joyprice.org/license
     * @link        http://www.joyprice.org/themagice
     * @author      ツ Liverbool <liverbool@joyprice.com>
     * @version     1.0
     * @since       1.0
     * @see         https://github.com/symfony/symfony/issues/9138
     */
    class Yaml extends FileLoader
    {

        private $yamlParser;

        private $bundles = array();

        /**
         * @var Application $app
         */
        private $app;

        /**
         * Constructor.
         *
         * @param Application $app A FileLocatorInterface instance
         */
        public function __construct(Application $app)
        {
            $this->app = $app;
            parent::__construct(new FileLocator($app));
        }

        /**
         * Loads a Yaml file.
         *
         * @param mixed  $file The resource
         * @param string $type The resource type
         */
        public function load($file, $type = null)
        {
            $content = $this->loadFile($this->locator->locate($file));

            // empty file
            if (null === $content) {
                return;
            }

            // imports
            $this->parseImports($content, $file);
            $this->registerBundles($content);
        }

        /**
         * Returns true if this class supports the given resource.
         *
         * @param mixed  $resource A resource
         * @param string $type     The resource type
         *
         * @return Boolean true if this class supports the given resource, false otherwise
         */
        public function supports($resource, $type = null)
        {
            return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
        }

        /**
         * Parses all imports
         *
         * @param array  $content
         * @param string $file
         */
        private function parseImports($content, $file)
        {
            if (!isset($content['imports'])) {
                return;
            }

            foreach ($content['imports'] as $import) {
                $this->setCurrentDir(dirname($file));
                $this->import($import['resource'], null, isset($import['ignore_errors']) ? (Boolean) $import['ignore_errors'] : false, $file);
            }
        }

        /**
         * Loads a YAML file.
         *
         * @param string $file
         *
         * @return array The file content
         * @throws \InvalidArgumentException
         */
        protected function loadFile($file)
        {
            if (!stream_is_local($file)) {
                throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $file));
            }

            if (!file_exists($file)) {
                throw new \InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
            }

            if (null === $this->yamlParser) {
                $this->yamlParser = new Parser();
            }

            return $this->validate($this->yamlParser->parse(file_get_contents($file)), $file);
        }

        /**
         * Validates a YAML file.
         *
         * @param mixed  $content
         * @param string $file
         *
         * @return array
         * @throws \InvalidArgumentException When service file is not valid
         */
        private function validate($content, $file)
        {
            if (null === $content) {
                return $content;
            }

            if (!is_array($content)) {
                throw new \InvalidArgumentException(sprintf('The service file "%s" is not valid.', $file));
            }

            return $content;
        }

        /**
         * Loads from Extensions
         *
         * @param array $content
         *
         * @throws \InvalidArgumentException
         * @todo save to cache
         */
        protected function registerBundles($content)
        {
            foreach ($content as $namespace => $values) {
                if (
                    array_key_exists($namespace, $this->bundles)
                    || in_array($namespace, array(Magice::NAME_SPACE, 'imports', 'parameters', 'services'))
                ) {
                    continue;
                }

                // lazy config - bundle_key: 'class...'
                if (!is_array($values)) {
                    $values = array(
                        Magice::NAME_SPACE => $values
                    );
                }

                if (empty($values[Magice::NAME_SPACE])) {
                    throw new \InvalidArgumentException(sprintf(
                        'Cannot found ("%s") config in your bundle ("%s").',
                        Magice::NAME_SPACE,
                        $namespace
                    ));
                }

                $magice = $values[Magice::NAME_SPACE];
                $bundle = null;
                $args   = null;
                $envs   = null;

                // magice: Class\Name
                // magice: 'Class\Name --envs dev,test'
                // magice: 'Class\Name --args xx'
                // magice: 'Class\Name --envs dev,test --args xx'
                if (is_string($magice)) {
                    $magice = preg_replace('/ +/', ' ', trim($magice));
                    $ptts   = explode('--', $magice);

                    @list($bundle, $tmp) = explode(' ', $ptts[0]);

                    array_shift($ptts);

                    if (!empty($ptts)) {
                        array_map(
                            function ($v) use (&$args, &$envs) {
                                $v = trim($v);
                                list($key, $value) = explode(' ', $v);
                                $opts = explode(',', trim($value));

                                switch (strtolower($key)) {
                                    case 'arg':
                                        $args = $opts;
                                        break;
                                    case 'env':
                                        $envs = $opts;
                                        break;
                                }
                            },
                            $ptts
                        );
                    }

                } else {
                    // magice:
                    //      class   : Class\Name
                    //      envs     : dev,test
                    //      args    : xx

                    $bundle = @$magice['class'];
                    $envs   = explode(',', @$magice['env']);
                    $args   = explode(',', @$magice['arg']);
                }

                // check environment
                if (!empty($envs) && $envs !== '*') {
                    $envs = array_map(
                        function ($env) {
                            return trim($env);
                        },
                        $envs
                    );

                    if (!in_array($this->app->getEnvironment(), $envs)) {
                        continue;
                    }
                }

                $bundle = trim($bundle);
                if (empty($bundle)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Cannot found bundle class name of ("%s").',
                        $namespace
                    ));
                }

                if (!class_exists($bundle)) {
                    throw new \InvalidArgumentException(sprintf(
                        'Your class ("%s") of ("%s") not exist.',
                        $bundle,
                        $namespace
                    ));
                }

                if (!empty($args)) {
                    $bundle = new \ReflectionClass($bundle);
                    $bundle = $bundle->newInstance($args);
                } else {
                    $bundle = new $bundle;
                }

                $this->bundles[$namespace] = $bundle;
            }
        }

        /**
         * @return array
         */
        public function getBundles()
        {
            return $this->bundles;
        }
    }
}