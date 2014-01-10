<?php
namespace Magice\Service\Loader {

    use Magice;
    use Symfony\Component\Yaml\Parser,
        Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
    class Yaml extends YamlFileLoader
    {

        private $yamlParser;

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

            foreach (array_keys($content) as $namespace) {

                // lazy bundle config
                if(is_string($content[$namespace])) {
                    unset($content[$namespace]);
                    continue;
                }

                if (in_array($namespace, array('imports', 'parameters', 'services'))) {
                    continue;
                }

                if (!$this->container->hasExtension($namespace)) {
                    $extensionNamespaces = array_filter(
                        array_map(
                            function ($ext) {
                                /**
                                 * @var \Symfony\Component\DependencyInjection\Extension\Extension $ext
                                 */
                                return $ext->getAlias();
                            },
                            $this->container->getExtensions()
                        )
                    );

                    if ( // this not register extension just enalble bundle by magice
                        count($content[$namespace]) == 1
                        && array_key_exists(Magice::NAME_SPACE, $content[$namespace])
                    ) {
                        unset($content[$namespace]);
                        continue;
                    }

                    throw new \InvalidArgumentException(sprintf(
                        'There is no extension able to load the configuration for "%s" (in %s). Looked for namespace "%s", found %s',
                        $namespace,
                        $file,
                        $namespace,
                        $extensionNamespaces ? sprintf('"%s"', implode('", "', $extensionNamespaces)) : 'none'
                    ));
                } else {

                    // ignore `magice` config key
                    // @see https://github.com/symfony/symfony/issues/9138
                    if (array_key_exists(Magice::NAME_SPACE, (array) $content[$namespace])) {
                        unset($content[$namespace][Magice::NAME_SPACE]);
                    }
                }
            }

            return $content;
        }
    }
}